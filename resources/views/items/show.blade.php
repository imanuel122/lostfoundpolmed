@extends('layouts.app')

@php
    $temuan = $item->jenis === 'temuan';
    $ikon = $item->category->ikon ?? 'package';

    $statusMap = [
        'aktif' => ['Belum diklaim', 'bg-slate-100 text-slate-600', 'bg-slate-400'],
        'verifikasi' => ['Dalam verifikasi', 'bg-amber-100 text-amber-700', 'bg-amber-500'],
        'dikembalikan' => ['Sudah dikembalikan', 'bg-blue-100 text-blue-700', 'bg-blue-500'],
        'kedaluwarsa' => ['Kedaluwarsa', 'bg-slate-100 text-slate-500', 'bg-slate-400'],
        'dihapus' => ['Dihapus', 'bg-rose-100 text-rose-700', 'bg-rose-500'],
    ];
    $status = $statusMap[$item->status] ?? $statusMap['aktif'];

    if ($item->status === 'verifikasi' && $klaimDisetujui) {
        $status = ['Menunggu serah terima', 'bg-emerald-100 text-emerald-700', 'bg-emerald-500'];
    }

    $tema = $temuan
        ? [
            'hero' => 'from-emerald-50',
            'blob1' => 'bg-emerald-300/40',
            'blob2' => 'bg-cyan-300/40',
            'btn' => 'from-emerald-600 to-teal-600 shadow-emerald-600/30',
            'label' => 'bg-emerald-600',
            'teks' => 'DITEMUKAN',
            'cta' => 'Ini Barang Saya',
            'ctaIcon' => 'shield-check',
            'ctaInfo' =>
                'Kamu akan diminta menjelaskan ciri barang dan bisa melampirkan foto sebagai bukti kepemilikan.',
        ]
        : [
            'hero' => 'from-rose-50',
            'blob1' => 'bg-rose-300/40',
            'blob2' => 'bg-orange-200/50',
            'btn' => 'from-rose-500 to-orange-500 shadow-rose-500/30',
            'label' => 'bg-rose-500',
            'teks' => 'DICARI',
            'cta' => 'Saya Melihat Barang Ini',
            'ctaIcon' => 'package-check',
            'ctaInfo' => 'Kamu akan diminta menjelaskan kondisi barang dan bisa melampirkan foto yang kamu temukan.',
        ];

    $fotos = $item->images->map(fn($im) => asset('storage/' . $im->path_foto))->values();

    $selesai = in_array($item->status, ['dikembalikan', 'kedaluwarsa', 'dihapus']);
    $namaDepan = Str::before($item->user->name, ' ');

    $infos = array_filter([
        ['map-pin', 'Lokasi', $item->location->nama . ($item->detail_lokasi ? ' — ' . $item->detail_lokasi : '')],
        ['calendar', $temuan ? 'Ditemukan pada' : 'Hilang pada', $item->tanggal_kejadian->translatedFormat('l, d F Y')],
        $temuan && $item->lokasi_penyimpanan ? ['archive', 'Disimpan di', $item->lokasi_penyimpanan] : null,
        ['user', 'Diposting oleh', $namaDepan],
        ['clock', 'Waktu posting', $item->created_at->diffForHumans()],
    ]);

    $waText = rawurlencode(
        ($temuan ? 'Ada barang ditemukan di Polmed: ' : 'Ada yang kehilangan barang di Polmed: ') .
            $item->judul .
            ' — ' .
            url()->current(),
    );
@endphp

@section('title', $item->judul . ' - LostFound Polmed')

@section('content')

    {{-- ================= LATAR + HEADER ================= --}}
    <section class="relative overflow-hidden pt-28 pb-6 bg-gradient-to-b {{ $tema['hero'] }} via-white to-slate-50">
        <div class="absolute -top-24 -left-24 w-96 h-96 {{ $tema['blob1'] }} rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-10 -right-24 w-80 h-80 {{ $tema['blob2'] }} rounded-full blur-3xl animate-blob"
            style="animation-delay:-5s"></div>

        <div class="relative max-w-6xl mx-auto px-4">
            <a href="{{ route('home') }}#daftar"
                class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-slate-900 transition">
                <x-icon name="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition" />
                Kembali ke daftar
            </a>
        </div>
    </section>

    {{-- ================= ISI ================= --}}
    <section class="max-w-6xl mx-auto px-4 pb-6 grid lg:grid-cols-5 gap-6">

        {{-- ---------- GALERI ---------- --}}
        <div class="lg:col-span-3 animate-fade-up" x-data="{
            imgs: @js($fotos),
            i: 0,
            open: false,
            next() { this.i = (this.i + 1) % this.imgs.length },
            prev() { this.i = (this.i - 1 + this.imgs.length) % this.imgs.length }
        }" @keydown.escape.window="open = false"
            @keydown.arrow-right.window="if (open && imgs.length > 1) next()"
            @keydown.arrow-left.window="if (open && imgs.length > 1) prev()">

            <div
                class="relative aspect-[4/3] rounded-3xl overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 ring-1 ring-slate-200 shadow-xl shadow-slate-900/5">

                @if ($fotos->count())
                    <img :src="imgs[i]" alt="{{ $item->judul }}"
                        class="w-full h-full object-cover cursor-zoom-in {{ $item->status === 'dikembalikan' ? 'grayscale' : '' }}"
                        @click="open = true">

                    <button type="button" @click="open = true" aria-label="Perbesar foto"
                        class="absolute bottom-3 right-3 w-10 h-10 rounded-full bg-white/90 backdrop-blur text-slate-700 grid place-items-center shadow hover:bg-white hover:scale-110 transition">
                        <x-icon name="maximize-2" class="w-4 h-4" />
                    </button>

                    @if ($fotos->count() > 1)
                        <button type="button" @click="prev()" aria-label="Foto sebelumnya"
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 backdrop-blur text-slate-700 grid place-items-center shadow hover:bg-white hover:scale-110 transition">
                            <x-icon name="chevron-left" class="w-5 h-5" />
                        </button>
                        <button type="button" @click="next()" aria-label="Foto berikutnya"
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 backdrop-blur text-slate-700 grid place-items-center shadow hover:bg-white hover:scale-110 transition">
                            <x-icon name="chevron-right" class="w-5 h-5" />
                        </button>
                        <span
                            class="absolute bottom-3 left-3 px-3 py-1 rounded-full bg-slate-900/75 text-white text-xs font-bold backdrop-blur">
                            <span x-text="i + 1"></span> / {{ $fotos->count() }}
                        </span>
                    @endif
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                        <x-icon :name="$ikon" class="w-24 h-24" />
                        <p class="mt-3 text-sm font-medium text-slate-400">Belum ada foto</p>
                    </div>
                @endif

                <span
                    class="absolute top-4 left-4 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-extrabold tracking-wide text-white shadow-lg {{ $tema['label'] }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span> {{ $tema['teks'] }}
                </span>
            </div>

            {{-- Thumbnail --}}
            @if ($fotos->count() > 1)
                <div class="mt-3 flex gap-2.5 overflow-x-auto no-scrollbar pb-1">
                    <template x-for="(src, n) in imgs" :key="n">
                        <button type="button" @click="i = n"
                            class="shrink-0 w-20 h-20 rounded-2xl overflow-hidden ring-2 transition hover:-translate-y-0.5"
                            :class="i === n ? 'ring-slate-900' : 'ring-transparent opacity-60 hover:opacity-100'">
                            <img :src="src" alt="" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            @endif

            {{-- Lightbox --}}
            <div x-show="open" x-cloak x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0"
                x-transition:leave="transition duration-150" x-transition:leave-end="opacity-0" @click.self="open = false"
                class="fixed inset-0 z-[70] bg-slate-950/95 backdrop-blur-sm flex items-center justify-center p-4">
                <button type="button" @click="open = false" aria-label="Tutup"
                    class="absolute top-4 right-4 w-11 h-11 rounded-full bg-white/10 text-white grid place-items-center hover:bg-white/20 transition">
                    <x-icon name="x" class="w-5 h-5" />
                </button>

                @if ($fotos->count() > 1)
                    <button type="button" @click="prev()" aria-label="Sebelumnya"
                        class="absolute left-3 sm:left-6 w-11 h-11 rounded-full bg-white/10 text-white grid place-items-center hover:bg-white/20 transition">
                        <x-icon name="chevron-left" class="w-5 h-5" />
                    </button>
                    <button type="button" @click="next()" aria-label="Berikutnya"
                        class="absolute right-3 sm:right-6 w-11 h-11 rounded-full bg-white/10 text-white grid place-items-center hover:bg-white/20 transition">
                        <x-icon name="chevron-right" class="w-5 h-5" />
                    </button>
                @endif

                <img :src="imgs[i]" alt="{{ $item->judul }}"
                    class="max-h-[88vh] max-w-full rounded-2xl object-contain shadow-2xl">
            </div>
        </div>

        {{-- ---------- PANEL AKSI (kanan di layar besar) ---------- --}}
        <aside
            class="lg:col-span-2 lg:col-start-4 lg:row-start-1 lg:row-span-2 lg:self-start lg:sticky lg:top-24 space-y-5">

            <div class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 shadow-xl shadow-slate-900/5 p-6"
                style="animation-delay:.05s">
                <div class="flex flex-wrap items-center gap-2">
                    <span
                        class="inline-flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        <x-icon :name="$ikon" class="w-3.5 h-3.5" /> {{ $item->category->nama }}
                    </span>
                </div>

                <h1 class="mt-2 text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 leading-tight">
                    {{ $item->judul }}</h1>

                <span
                    class="inline-flex items-center gap-1.5 mt-3 px-3 py-1 rounded-full text-xs font-semibold {{ $status[1] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $status[2] }}"></span> {{ $status[0] }}
                </span>

                {{-- ===== Area tombol aksi ===== --}}
                <div class="mt-6">

                    @if ($item->status === 'dikembalikan')
                        <div class="rounded-2xl bg-blue-50 ring-1 ring-blue-200 p-4 flex gap-3">
                            <span
                                class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 grid place-items-center shrink-0"><x-icon
                                    name="circle-check" class="w-5 h-5" /></span>
                            <div class="text-sm">
                                <p class="font-bold text-blue-900">Barang ini sudah dikembalikan</p>
                                <p class="text-blue-800/80 mt-0.5">
                                    Selesai pada {{ $item->completed_at?->translatedFormat('d F Y') ?? '-' }}. Terima kasih
                                    sudah saling membantu.
                                </p>
                            </div>
                        </div>
                        @if ($klaimDisetujui && ($pemilik || $admin || auth()->id() === $klaimDisetujui->claimant_id))
                            <a href="{{ route('claims.show', $klaimDisetujui) }}"
                                class="mt-3 w-full inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-6 py-3 font-semibold text-white hover:bg-slate-700 active:scale-[.98] transition">
                                <x-icon name="package-check" class="w-4 h-4" /> Lihat bukti serah terima
                            </a>
                        @endif
                    @elseif($item->status === 'kedaluwarsa')
                        <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-200 p-4 flex gap-3">
                            <span
                                class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 grid place-items-center shrink-0"><x-icon
                                    name="clock" class="w-5 h-5" /></span>
                            <div class="text-sm">
                                <p class="font-bold text-slate-800">Postingan sudah kedaluwarsa</p>
                                <p class="text-slate-500 mt-0.5">Tidak ada klaim dalam batas waktu. Hubungi pihak kampus
                                    untuk menanyakan barang ini.</p>
                            </div>
                        </div>
                    @elseif($item->status === 'dihapus')
                        <div class="rounded-2xl bg-rose-50 ring-1 ring-rose-200 p-4 flex gap-3">
                            <span
                                class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 grid place-items-center shrink-0"><x-icon
                                    name="triangle-alert" class="w-5 h-5" /></span>
                            <p class="text-sm font-bold text-rose-900 self-center">Postingan ini sudah dihapus.</p>
                        </div>
                    @elseif($pemilik)
                        {{-- PEMILIK POSTINGAN --}}
                        <div class="rounded-2xl bg-slate-50 ring-1 ring-slate-200 p-4">
                            <p class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <x-icon name="user" class="w-4 h-4" /> Ini postingan kamu
                            </p>
                            <p class="text-sm text-slate-500 mt-1">
                                @if ($klaimDisetujui)
                                    Klaim dari <span
                                        class="font-bold text-emerald-600">{{ $klaimDisetujui->claimant->name }}</span>
                                    sudah disetujui.
                                @elseif($klaimMenunggu > 0)
                                    Ada <span class="font-bold text-amber-600">{{ $klaimMenunggu }} klaim</span> yang
                                    menunggu pemeriksaanmu.
                                @elseif($daftarKlaim->isEmpty())
                                    {{ $temuan ? 'Belum ada yang mengajukan klaim.' : 'Belum ada laporan dari penemu.' }}
                                @else
                                    Semua klaim sudah diproses.
                                @endif
                            </p>
                        </div>

                        {{-- TOMBOL SERAH TERIMA --}}
                        @if ($klaimDisetujui && $klaimDisetujui->status === 'disetujui')
                            <a href="{{ route('claims.show', $klaimDisetujui) }}"
                                class="mt-3 group w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3.5 font-semibold text-white shadow-lg shadow-emerald-600/30 hover:-translate-y-0.5 active:scale-[.98] transition">
                                <x-icon name="package-check" class="w-5 h-5" />
                                {{ $klaimDisetujui->handed_over_at ? 'Lihat status serah terima' : 'Lanjutkan serah terima' }}
                                <x-icon name="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition" />
                            </a>
                        @endif

                        {{-- DAFTAR KLAIM --}}
                        @if ($daftarKlaim->count())
                            <div class="mt-3 space-y-2">
                                @foreach ($daftarKlaim as $k)
                                    @php
                                        $badge = [
                                            'menunggu' => 'bg-amber-100 text-amber-700',
                                            'disetujui' => 'bg-emerald-100 text-emerald-700',
                                            'ditolak' => 'bg-rose-100 text-rose-700',
                                            'selesai' => 'bg-blue-100 text-blue-700',
                                        ][$k->status];
                                    @endphp
                                    <a href="{{ route('claims.show', $k) }}"
                                        class="group flex items-center gap-3 rounded-xl bg-white ring-1 ring-slate-200 px-4 py-3 hover:ring-slate-300 hover:-translate-y-0.5 transition">
                                        <span
                                            class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 grid place-items-center text-sm font-bold shrink-0">
                                            {{ strtoupper(mb_substr($k->claimant->name, 0, 1)) }}
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span
                                                class="block text-sm font-semibold text-slate-800 truncate">{{ $k->claimant->name }}</span>
                                            <span
                                                class="block text-xs text-slate-400">{{ $k->created_at->diffForHumans() }}</span>
                                        </span>
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $badge }}">{{ ucfirst($k->status) }}</span>
                                        <x-icon name="arrow-right"
                                            class="w-4 h-4 text-slate-300 group-hover:text-slate-600 group-hover:translate-x-0.5 transition" />
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    @elseif($sudahKlaim)
                        @if ($sudahKlaim->status === 'disetujui')
                            <div class="rounded-2xl bg-emerald-50 ring-1 ring-emerald-200 p-4 flex gap-3">
                                <span
                                    class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 grid place-items-center shrink-0"><x-icon
                                        name="circle-check" class="w-5 h-5" /></span>
                                <div class="text-sm">
                                    <p class="font-bold text-emerald-900">Klaim kamu disetujui</p>
                                    <p class="text-emerald-800/80 mt-0.5">Hubungi pembuat postingan untuk serah terima,
                                        lalu konfirmasi di halaman klaim.</p>
                                </div>
                            </div>
                            <a href="{{ route('claims.show', $sudahKlaim) }}"
                                class="mt-3 w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3 font-semibold text-white shadow-lg shadow-emerald-600/30 hover:-translate-y-0.5 active:scale-[.98] transition">
                                <x-icon name="phone" class="w-4 h-4" /> Hubungi &amp; serah terima
                            </a>
                        @else
                            <div class="rounded-2xl bg-amber-50 ring-1 ring-amber-200 p-4 flex gap-3">
                                <span
                                    class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 grid place-items-center shrink-0"><x-icon
                                        name="clock" class="w-5 h-5" /></span>
                                <div class="text-sm">
                                    <p class="font-bold text-amber-900">Klaim kamu sedang diproses</p>
                                    <p class="text-amber-800/80 mt-0.5">Kamu akan diberi tahu setelah pembuat postingan
                                        memeriksanya.</p>
                                </div>
                            </div>
                            <a href="{{ route('claims.show', $sudahKlaim) }}"
                                class="mt-3 w-full inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-6 py-3 font-semibold text-white hover:bg-slate-700 active:scale-[.98] transition">
                                <x-icon name="file-text" class="w-4 h-4" /> Lihat klaim saya
                            </a>
                        @endif
                    @elseif($klaimDisetujui)
                        {{-- ORANG LAIN (termasuk tamu): barang sudah dalam proses serah terima --}}
                        <div class="rounded-2xl bg-emerald-50 ring-1 ring-emerald-200 p-4 flex gap-3">
                            <span
                                class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 grid place-items-center shrink-0"><x-icon
                                    name="package-check" class="w-5 h-5" /></span>
                            <div class="text-sm">
                                <p class="font-bold text-emerald-900">Sedang dalam proses serah terima</p>
                                <p class="text-emerald-800/80 mt-0.5">Klaim untuk barang ini sudah disetujui, jadi tidak
                                    menerima klaim baru.</p>
                            </div>
                        </div>
                    @elseif(auth()->check())
                        {{-- PENGGUNA LOGIN, BUKAN PEMILIK --}}
                        <a href="{{ route('claims.create', $item) }}"
                            class="group w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r {{ $tema['btn'] }} px-6 py-3.5 font-semibold text-white shadow-lg hover:-translate-y-0.5 active:scale-[.98] transition">
                            <x-icon :name="$tema['ctaIcon']" class="w-5 h-5" />
                            {{ $tema['cta'] }}
                            <x-icon name="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition" />
                        </a>
                        <p class="mt-3 text-xs text-slate-500 leading-relaxed">{{ $tema['ctaInfo'] }}</p>
                        @if ($item->status === 'verifikasi')
                            <p class="mt-2 text-xs text-amber-600 font-medium">Sudah ada klaim yang sedang diverifikasi,
                                tapi kamu tetap bisa mengajukan.</p>
                        @endif
                    @else
                        {{-- TAMU --}}
                        <a href="{{ route('login') }}"
                            class="group w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r {{ $tema['btn'] }} px-6 py-3.5 font-semibold text-white shadow-lg hover:-translate-y-0.5 active:scale-[.98] transition">
                            <x-icon name="lock" class="w-5 h-5" />
                            Masuk untuk {{ $temuan ? 'mengklaim' : 'melapor' }}
                        </a>
                        <p class="mt-3 text-xs text-slate-500 leading-relaxed">
                            Kamu harus login supaya identitas jelas dan klaim palsu bisa dicegah. Setelah masuk, kamu
                            kembali ke halaman ini.
                            Belum punya akun? <a href="{{ route('register') }}"
                                class="font-bold text-emerald-600 hover:underline">Daftar</a>.
                        </p>
                    @endif
                </div>

                {{-- ===== Bagikan ===== --}}
                <div class="mt-6 pt-5 border-t border-slate-100" x-data="{ copied: false }">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Bantu sebarkan</p>
                    <div class="mt-3 grid grid-cols-2 gap-2.5">
                        <button type="button"
                            @click="navigator.clipboard.writeText(window.location.href).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-50 ring-1 ring-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:-translate-y-0.5 active:scale-95 transition">
                            <x-icon name="link" x-show="!copied" class="w-4 h-4" />
                            <x-icon name="check" x-show="copied" x-cloak class="w-4 h-4 text-emerald-600" />
                            <span x-text="copied ? 'Tersalin!' : 'Salin tautan'">Salin tautan</span>
                        </button>
                        <a href="https://wa.me/?text={{ $waText }}" target="_blank" rel="noopener"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-50 ring-1 ring-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:-translate-y-0.5 active:scale-95 transition">
                            <x-icon name="share-2" class="w-4 h-4" /> Bagikan WA
                        </a>
                    </div>
                </div>
            </div>

            {{-- Info keamanan --}}
            <div class="animate-fade-up rounded-3xl bg-slate-900 text-white p-6 relative overflow-hidden"
                style="animation-delay:.1s">
                <div class="absolute -top-12 -right-12 w-40 h-40 {{ $tema['blob1'] }} rounded-full blur-3xl"></div>
                <div class="relative">
                    <span class="w-10 h-10 rounded-xl bg-white/10 grid place-items-center text-emerald-300"><x-icon
                            name="shield-check" class="w-5 h-5" /></span>
                    <h3 class="mt-4 font-bold">Aman dan terverifikasi</h3>
                    <ul class="mt-3 space-y-2.5 text-sm text-slate-300">
                        <li class="flex gap-2.5"><x-icon name="check"
                                class="w-4 h-4 mt-0.5 text-emerald-400 shrink-0" /> Nomor WhatsApp baru terbuka setelah
                            klaim disetujui.</li>
                        <li class="flex gap-2.5"><x-icon name="check"
                                class="w-4 h-4 mt-0.5 text-emerald-400 shrink-0" /> Ciri rahasia dipakai untuk mencocokkan
                            pemilik asli.</li>
                        <li class="flex gap-2.5"><x-icon name="check"
                                class="w-4 h-4 mt-0.5 text-emerald-400 shrink-0" /> Serah terima dicatat dengan foto bukti.
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        {{-- ---------- DETAIL ---------- --}}
        <div class="lg:col-span-3 space-y-5">

            <div class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 p-6 sm:p-7"
                style="animation-delay:.1s">
                <h2 class="font-bold text-slate-900">Detail {{ $temuan ? 'penemuan' : 'kehilangan' }}</h2>

                <dl class="mt-5 grid sm:grid-cols-2 gap-x-6 gap-y-5">
                    @foreach ($infos as $row)
                        <div class="flex gap-3 {{ $loop->first ? 'sm:col-span-2' : '' }}">
                            <span
                                class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 grid place-items-center shrink-0">
                                <x-icon :name="$row[0]" class="w-5 h-5" />
                            </span>
                            <div class="min-w-0">
                                <dt class="text-xs font-semibold text-slate-400">{{ $row[1] }}</dt>
                                <dd class="text-sm font-semibold text-slate-800 break-words">{{ $row[2] }}</dd>
                            </div>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 p-6 sm:p-7"
                style="animation-delay:.15s">
                <h2 class="font-bold text-slate-900">Deskripsi</h2>
                @if ($item->deskripsi)
                    <p class="mt-3 text-slate-600 leading-relaxed whitespace-pre-line">{{ $item->deskripsi }}</p>
                @else
                    <p class="mt-3 text-sm text-slate-400 italic">Pembuat postingan tidak menambahkan deskripsi.</p>
                @endif
            </div>

            {{-- Ciri rahasia: hanya pemilik postingan dan admin --}}
            @if (($pemilik || $admin) && $item->ciri_rahasia)
                <div class="animate-fade-up rounded-3xl bg-amber-50 ring-1 ring-amber-200 p-6 sm:p-7"
                    style="animation-delay:.2s">
                    <div class="flex items-start gap-3">
                        <span
                            class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 grid place-items-center shrink-0"><x-icon
                                name="eye-off" class="w-5 h-5" /></span>
                        <div>
                            <h2 class="font-bold text-amber-900">Ciri rahasia</h2>
                            <p class="text-xs text-amber-800/70">Hanya terlihat
                                olehmu{{ $admin && !$pemilik ? ' dan admin' : '' }}. Pengunjung lain tidak bisa melihat
                                ini.</p>
                        </div>
                    </div>
                    <p class="mt-4 text-amber-900 leading-relaxed whitespace-pre-line">{{ $item->ciri_rahasia }}</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ================= MUNGKIN BERKAITAN ================= --}}
    @if ($berkaitan->count())
        <section class="max-w-6xl mx-auto px-4 pt-14">
            <div class="reveal">
                <h2 class="text-2xl font-extrabold text-slate-900">Mungkin berkaitan</h2>
                <p class="text-slate-500 mt-1">
                    {{ $temuan ? 'Laporan kehilangan' : 'Barang temuan' }} dengan kategori
                    <span class="font-semibold text-slate-700">{{ $item->category->nama }}</span>. Siapa tahu cocok.
                </p>
            </div>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach ($berkaitan as $b)
                    @include('partials.item-card', ['item' => $b, 'i' => $loop->index])
                @endforeach
            </div>
        </section>
    @endif

@endsection
