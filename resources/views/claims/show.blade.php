@extends('layouts.app')

@php
    $item = $claim->item;
    $temuan = $item->jenis === 'temuan';
    $ikon = $item->category->ikon ?? 'package';

    $st = [
        'menunggu' => [
            'Menunggu pemeriksaan',
            'bg-amber-50 ring-amber-200',
            'bg-amber-100 text-amber-600',
            'text-amber-900',
            'clock',
        ],
        'disetujui' => [
            'Klaim disetujui',
            'bg-emerald-50 ring-emerald-200',
            'bg-emerald-100 text-emerald-600',
            'text-emerald-900',
            'circle-check',
        ],
        'ditolak' => [
            'Klaim ditolak',
            'bg-rose-50 ring-rose-200',
            'bg-rose-100 text-rose-600',
            'text-rose-900',
            'circle-x',
        ],
        'selesai' => [
            'Selesai',
            'bg-blue-50 ring-blue-200',
            'bg-blue-100 text-blue-600',
            'text-blue-900',
            'package-check',
        ],
    ][$claim->status];

    $peran = $temuan ? 'Pengklaim (mengaku pemilik)' : 'Pelapor (mengaku menemukan)';

    $peranPosting = $temuan ? 'penemu' : 'pemilik';
    $peranKlaim = $temuan ? 'pemilik' : 'penemu';
    $namaPosting = $item->user->name;
    $namaKlaim = $claim->claimant->name;
    $peranLawan = $pengklaim ? $peranPosting : $peranKlaim;

    // ini untuk menyiapkan isi pesan WhatsApp yang akan dikirim setelah klaim disetujui
    $saya = auth()->user()->name;
    $namaLawan = $lawan ? Str::before($lawan->name, ' ') : '';
    $linkBarang = route('items.show', $item);

    if ($temuan && $pengklaim) {
        // Pemilik yang mengklaim -> mau mengambil barangnya
        $isiWa =
            "Halo Kak {$namaLawan}, saya {$saya}.\n\n" .
            "Saya pemilik \"{$item->judul}\" yang Kakak temukan dan posting di LostFound Polmed. Klaim saya sudah disetujui, terima kasih banyak sudah menemukannya.\n\n" .
            "Saya ingin mengambil kembali barang saya. Kira-kira kapan dan di mana kita bisa bertemu?\n\n" .
            $linkBarang;
    } elseif ($temuan) {
        // Penemu yang menghubungi dulu -> mau mengembalikan
        $isiWa =
            "Halo Kak {$namaLawan}, saya {$saya}.\n\n" .
            "Saya yang menemukan \"{$item->judul}\" dan memposting di LostFound Polmed. Klaim Kakak sudah saya setujui.\n\n" .
            "Saya ingin mengembalikan barang tersebut kepada Kakak. Kapan dan di mana kita bisa bertemu?\n\n" .
            $linkBarang;
    } elseif ($pengklaim) {
        // Penemu yang melapor untuk barang DICARI -> mau mengembalikan
        $isiWa =
            "Halo Kak {$namaLawan}, saya {$saya}.\n\n" .
            "Saya yang melaporkan telah menemukan \"{$item->judul}\" milik Kakak di LostFound Polmed. Laporan saya sudah disetujui.\n\n" .
            "Saya ingin mengembalikan barang tersebut. Kapan dan di mana kita bisa bertemu?\n\n" .
            $linkBarang;
    } else {
        // Pemilik barang hilang yang menghubungi penemu -> mau mengambil
        $isiWa =
            "Halo Kak {$namaLawan}, saya {$saya}.\n\n" .
            "Saya pemilik \"{$item->judul}\" yang hilang, dan laporan penemuan Kakak di LostFound Polmed sudah saya setujui. Terima kasih banyak atas bantuannya.\n\n" .
            "Saya ingin mengambil kembali barang saya. Kapan dan di mana kita bisa bertemu?\n\n" .
            $linkBarang;
    }
@endphp

@section('title', 'Detail Klaim - LostFound Polmed')

@section('content')

    <section class="relative overflow-hidden pt-28 pb-6 bg-gradient-to-b from-slate-100 via-white to-slate-50">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-300/30 rounded-full blur-3xl animate-blob"></div>
        <div class="relative max-w-4xl mx-auto px-4">
            <a href="{{ route('items.show', $item) }}"
                class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-slate-900 transition">
                <x-icon name="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition" />
                Kembali ke detail barang
            </a>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 animate-fade-up">Detail
                {{ $temuan ? 'Klaim' : 'Laporan' }}</h1>
        </div>
    </section>

    <section class="max-w-4xl mx-auto px-4 pb-6 space-y-5">

        {{-- STATUS --}}
        <div class="animate-fade-up rounded-3xl ring-1 p-5 flex gap-4 {{ $st[1] }}">
            <span class="w-12 h-12 rounded-2xl grid place-items-center shrink-0 {{ $st[2] }}"><x-icon
                    :name="$st[4]" class="w-6 h-6" /></span>
            <div class="text-sm self-center">
                <p class="font-bold text-base {{ $st[3] }}">{{ $st[0] }}</p>
                <p class="mt-0.5 {{ $st[3] }} opacity-80">
                    @if ($claim->status === 'menunggu')
                        {{ $pengklaim ? 'Pembuat postingan sedang memeriksa buktimu.' : 'Bandingkan bukti di bawah dengan ciri rahasia, lalu setujui atau tolak.' }}
                    @elseif($claim->status === 'disetujui')
                        Silakan hubungi {{ $peranLawan }} ({{ $lawan?->name }}) untuk janjian serah terima.
                    @elseif($claim->status === 'ditolak')
                        {{ $claim->catatan_admin ?: 'Bukti belum cocok dengan ciri barang.' }}
                    @else
                        Barang sudah diserahterimakan.
                    @endif
                </p>
            </div>
        </div>

        {{-- PANEL REVIEW (pembuat postingan / admin) --}}
        @if ($bolehReview && $claim->status === 'menunggu')
            <div x-data="{ tolak: {{ $errors->has('catatan_admin') ? 'true' : 'false' }}, loading: false, konfirmasi: false }" @keydown.escape.window="konfirmasi = false"
                class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 shadow-xl shadow-slate-900/5 p-6">
                <div class="flex items-start gap-3">
                    <span class="w-10 h-10 rounded-xl bg-slate-900 text-white grid place-items-center shrink-0">
                        <x-icon name="shield-check" class="w-5 h-5" />
                    </span>
                    <div>
                        <h2 class="font-bold text-slate-900">Periksa klaim ini</h2>
                        <p class="text-sm text-slate-500">Setujui hanya kalau bukti cocok dengan ciri barang yang
                            sebenarnya.</p>
                    </div>
                </div>

                <div x-show="!tolak" class="mt-5 grid sm:grid-cols-2 gap-3">
                    <button type="button" @click="konfirmasi = true"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3.5 font-semibold text-white shadow-lg shadow-emerald-600/30 hover:-translate-y-0.5 active:scale-[.98] transition">
                        <x-icon name="circle-check" class="w-5 h-5" /> Setujui klaim
                    </button>
                    <button type="button" @click="tolak = true"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-50 ring-1 ring-rose-200 px-6 py-3.5 font-semibold text-rose-600 hover:bg-rose-100 hover:-translate-y-0.5 active:scale-[.98] transition">
                        <x-icon name="circle-x" class="w-5 h-5" /> Tolak klaim
                    </button>
                </div>

                <form x-show="tolak" x-cloak method="POST" action="{{ route('claims.reject', $claim) }}"
                    @submit="loading = true" class="mt-5">
                    @csrf
                    <label for="catatan_admin" class="block text-[13px] font-semibold text-slate-700">Alasan
                        penolakan</label>
                    <textarea id="catatan_admin" name="catatan_admin" rows="3" maxlength="300"
                        placeholder="Contoh: Ciri yang disebutkan belum sesuai dengan barang ini."
                        class="mt-1.5 w-full rounded-xl px-4 py-3 text-sm outline-none transition resize-none placeholder:text-slate-400 ring-1
                             {{ $errors->has('catatan_admin') ? 'bg-rose-50/60 ring-rose-300 focus:ring-2 focus:ring-rose-400' : 'bg-slate-50 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-rose-400' }}">{{ old('catatan_admin') }}</textarea>
                    @error('catatan_admin')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-rose-600">
                            <x-icon name="alert-circle" class="w-4 h-4 shrink-0" /> {{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1.5 text-xs text-slate-400">Alasan ini akan dibaca oleh pengklaim.</p>

                    <div class="mt-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" @click="tolak = false"
                            class="py-3 px-4 text-sm font-semibold text-slate-500 hover:text-slate-900 transition">Batal</button>
                        <button type="submit" :disabled="loading"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-500 px-6 py-3 font-semibold text-white shadow-lg shadow-rose-500/30 hover:bg-rose-600 active:scale-[.98] disabled:opacity-60 transition">
                            <x-icon name="circle-x" class="w-4 h-4" /> Kirim penolakan
                        </button>
                    </div>
                </form>

                {{-- MODAL KONFIRMASI SETUJUI --}}
                <template x-teleport="body">
                    <div x-show="konfirmasi" x-cloak x-transition:enter="transition duration-200"
                        x-transition:enter-start="opacity-0" x-transition:leave="transition duration-150"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 z-[80] flex items-center justify-center p-4">

                        {{-- latar gelap --}}
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="konfirmasi = false"></div>

                        {{-- kotak modal --}}
                        <div x-show="konfirmasi" x-transition:enter="transition duration-300 ease-out"
                            x-transition:enter-start="opacity-0 translate-y-6 scale-95"
                            x-transition:leave="transition duration-150" x-transition:leave-end="opacity-0 scale-95"
                            role="dialog" aria-modal="true"
                            class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl shadow-slate-900/30 ring-1 ring-slate-200 overflow-hidden">

                            <div class="absolute -top-16 -right-16 w-48 h-48 bg-emerald-300/40 rounded-full blur-3xl"></div>

                            <div class="relative p-7">
                                <span
                                    class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 text-white grid place-items-center shadow-lg shadow-emerald-500/30">
                                    <x-icon name="shield-check" class="w-7 h-7" />
                                </span>

                                <h3 class="mt-5 text-xl font-extrabold tracking-tight text-slate-900">Setujui klaim ini?
                                </h3>
                                <p class="mt-1.5 text-sm text-slate-500">
                                    Kamu akan menyetujui
                                    {{ $temuan ? 'klaim kepemilikan' : 'laporan penemuan' }}
                                    dari <span class="font-bold text-slate-700">{{ $claim->claimant->name }}</span>
                                    untuk <span class="font-bold text-slate-700">{{ $item->judul }}</span>.
                                </p>

                                <ul class="mt-5 space-y-3 text-sm">
                                    <li class="flex gap-3">
                                        <span
                                            class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 grid place-items-center shrink-0"><x-icon
                                                name="phone" class="w-3.5 h-3.5" /></span>
                                        <span class="text-slate-600">Nomor WhatsApp kamu dan
                                            <b>{{ $claim->claimant->name }}</b> akan <b>saling terbuka</b>.</span>
                                    </li>
                                    <li class="flex gap-3">
                                        <span
                                            class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 grid place-items-center shrink-0"><x-icon
                                                name="package-check" class="w-3.5 h-3.5" /></span>
                                        <span class="text-slate-600">Setelah ini kalian bisa janjian untuk <b>serah
                                                terima</b> barang.</span>
                                    </li>
                                    <li class="flex gap-3">
                                        <span
                                            class="w-6 h-6 rounded-full bg-amber-100 text-amber-600 grid place-items-center shrink-0"><x-icon
                                                name="triangle-alert" class="w-3.5 h-3.5" /></span>
                                        <span class="text-slate-600">Pastikan bukti sudah cocok dengan ciri rahasia.
                                            Persetujuan <b>belum bisa dibatalkan</b>.</span>
                                    </li>
                                </ul>

                                <form method="POST" action="{{ route('claims.approve', $claim) }}"
                                    @submit="loading = true"
                                    class="mt-7 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                                    @csrf
                                    <button type="button" @click="konfirmasi = false"
                                        class="rounded-xl px-5 py-3 text-sm font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition">
                                        Periksa lagi
                                    </button>
                                    <button type="submit" :disabled="loading"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3 font-semibold text-white shadow-lg shadow-emerald-600/30 hover:-translate-y-0.5 active:scale-[.98] disabled:opacity-60 disabled:pointer-events-none transition">
                                        <svg x-show="loading" x-cloak class="w-4 h-4 animate-spin" viewBox="0 0 24 24"
                                            fill="none">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4" class="opacity-25" />
                                            <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="4"
                                                stroke-linecap="round" />
                                        </svg>
                                        <x-icon name="circle-check" x-show="!loading" class="w-4 h-4" />
                                        <span x-text="loading ? 'Memproses...' : 'Ya, setujui'">Ya, setujui</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        @endif

        {{-- CIRI RAHASIA (untuk membandingkan) --}}
        @if ($bolehReview && $item->ciri_rahasia)
            <div class="animate-fade-up rounded-3xl bg-amber-50 ring-1 ring-amber-200 p-6">
                <div class="flex items-start gap-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 grid place-items-center shrink-0"><x-icon
                            name="eye-off" class="w-5 h-5" /></span>
                    <div>
                        <h2 class="font-bold text-amber-900">Ciri rahasia di postingan</h2>
                        <p class="text-xs text-amber-800/70">Hanya terlihat olehmu. Cocokkan dengan bukti pengklaim di
                            bawah.</p>
                    </div>
                </div>
                <p class="mt-4 text-amber-900 leading-relaxed whitespace-pre-line">{{ $item->ciri_rahasia }}</p>
            </div>
        @endif

        {{-- WHATSAPP (hanya setelah disetujui) --}}
        @if (in_array($claim->status, ['disetujui', 'selesai']) && $lawan?->no_wa)
            <div
                class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <p class="text-xs font-semibold text-slate-400">Hubungi {{ $peranLawan }}</p>
                    <p class="font-bold text-slate-900">{{ $lawan->name }}</p>
                </div>
                <a href="https://wa.me/{{ $lawan->no_wa }}?text={{ rawurlencode($isiWa) }}" target="_blank"
                    rel="noopener"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 font-semibold text-white shadow-lg shadow-emerald-600/30 hover:bg-emerald-700 hover:-translate-y-0.5 active:scale-95 transition">
                    <x-icon name="phone" class="w-4 h-4" /> Chat WhatsApp
                </a>
            </div>
        @endif

        {{-- HANDOVER --}}
        @include('claims._handover')

        {{-- BARANG --}}
        <a href="{{ route('items.show', $item) }}"
            class="animate-fade-up group flex gap-4 bg-white rounded-3xl ring-1 ring-slate-200 p-4 hover:shadow-xl hover:shadow-slate-900/5 hover:-translate-y-0.5 transition">
            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 shrink-0">
                @if ($item->foto_utama)
                    <img src="{{ $item->foto_utama }}" alt="{{ $item->judul }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full grid place-items-center text-slate-300"><x-icon :name="$ikon"
                            class="w-10 h-10" /></div>
                @endif
            </div>
            <div class="min-w-0 self-center">
                <span
                    class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-extrabold text-white {{ $temuan ? 'bg-emerald-600' : 'bg-rose-500' }}">{{ $temuan ? 'DITEMUKAN' : 'DICARI' }}</span>
                <h3 class="mt-1 font-bold text-slate-900 truncate group-hover:text-emerald-600 transition">
                    {{ $item->judul }}</h3>
                <p class="text-sm text-slate-500 truncate">{{ $item->location->nama }} ·
                    {{ $item->tanggal_kejadian->translatedFormat('d M Y') }}</p>
            </div>
        </a>

        {{-- ISI KLAIM --}}
        <div class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 p-6 sm:p-7">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="font-bold text-slate-900">{{ $temuan ? 'Bukti kepemilikan' : 'Barang yang ditemukan' }}</h2>
                <span class="text-xs text-slate-400">{{ $claim->created_at->translatedFormat('d M Y, H:i') }}</span>
            </div>

            <dl class="mt-4 grid sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-xs font-semibold text-slate-400">{{ $peran }}</dt>
                    <dd class="font-semibold text-slate-800">{{ $claim->claimant->name }}</dd>
                </div>
                @if ($claim->lokasi_barang_sekarang)
                    <div>
                        <dt class="text-xs font-semibold text-slate-400">Barang sekarang ada di</dt>
                        <dd class="font-semibold text-slate-800">{{ $claim->lokasi_barang_sekarang }}</dd>
                    </div>
                @endif
            </dl>

            <p class="mt-5 text-slate-600 leading-relaxed whitespace-pre-line">{{ $claim->bukti_deskripsi }}</p>

            @if ($claim->images->count())
                <p class="mt-6 text-xs font-semibold uppercase tracking-wider text-slate-400">Foto pendukung
                    ({{ $claim->images->count() }})</p>
                <div class="mt-3 grid grid-cols-3 sm:grid-cols-5 gap-3">
                    @foreach ($claim->images as $im)
                        <a href="{{ route('claims.image', $im) }}" target="_blank"
                            class="group aspect-square rounded-2xl overflow-hidden ring-1 ring-slate-200 bg-slate-100">
                            <img src="{{ route('claims.image', $im) }}" alt="Foto pendukung" loading="lazy"
                                class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        </a>
                    @endforeach
                </div>
                <p class="mt-3 flex items-center gap-1.5 text-xs text-slate-400">
                    <x-icon name="lock" class="w-3.5 h-3.5" /> Foto ini privat. Hanya {{ $namaPosting }}
                    ({{ $peranPosting }}), {{ $namaKlaim }} ({{ $peranKlaim }}), dan admin yang bisa melihatnya.
                </p>
            @endif
        </div>
    </section>

@endsection
