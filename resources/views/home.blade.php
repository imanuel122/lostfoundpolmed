@extends('layouts.app')

@section('title', 'LostFound Polmed - Barang hilang? Cek di sini dulu')

@section('content')

    {{-- ================= HERO ================= --}}
    <section class="relative overflow-hidden pt-28 pb-20 bg-gradient-to-b from-emerald-50 via-white to-slate-50">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-300/40 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-20 -right-24 w-96 h-96 bg-cyan-300/40 rounded-full blur-3xl animate-blob"
            style="animation-delay:-5s"></div>
        <div class="absolute bottom-0 left-1/3 w-72 h-72 bg-indigo-200/40 rounded-full blur-3xl animate-blob"
            style="animation-delay:-9s"></div>

        {{-- kartu melayang (dekorasi, hanya di layar besar) --}}
        <div class="hidden lg:block absolute left-[6%] top-40 animate-float" style="--r:-8deg">
            <div class="bg-white rounded-2xl shadow-xl shadow-slate-900/10 p-3 flex items-center gap-3 w-52">
                <span class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 grid place-items-center"><x-icon
                        name="wallet" class="w-5 h-5" /></span>
                <div class="text-left">
                    <p class="text-xs font-bold text-emerald-600">DITEMUKAN</p>
                    <p class="text-sm font-semibold">Dompet Hitam</p>
                </div>
            </div>
        </div>
        <div class="hidden lg:block absolute right-[7%] top-52 animate-float-slow" style="--r:7deg">
            <div class="bg-white rounded-2xl shadow-xl shadow-slate-900/10 p-3 flex items-center gap-3 w-52">
                <span class="w-11 h-11 rounded-xl bg-rose-100 text-rose-500 grid place-items-center"><x-icon
                        name="key-round" class="w-5 h-5" /></span>
                <div class="text-left">
                    <p class="text-xs font-bold text-rose-500">DICARI</p>
                    <p class="text-sm font-semibold">Kunci Motor</p>
                </div>
            </div>
        </div>
        <div class="hidden lg:block absolute left-[12%] bottom-16 animate-float-slow" style="--r:5deg;animation-delay:-3s">
            <div class="bg-white rounded-2xl shadow-xl shadow-slate-900/10 p-3 flex items-center gap-3 w-48">
                <span class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 grid place-items-center"><x-icon
                        name="circle-check" class="w-5 h-5" /></span>
                <div class="text-left">
                    <p class="text-xs font-bold text-blue-600">KEMBALI</p>
                    <p class="text-sm font-semibold">Jaket Hitam</p>
                </div>
            </div>
        </div>

        <div class="relative max-w-3xl mx-auto px-4 text-center">
            <span
                class="animate-fade-up inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/80 backdrop-blur border border-emerald-200 text-emerald-700 text-xs font-semibold shadow-sm">
                <span class="relative flex w-2 h-2">
                    <span class="absolute inset-0 rounded-full bg-emerald-500 animate-pulse-ring"></span>
                    <span class="relative w-2 h-2 rounded-full bg-emerald-500"></span>
                </span>
                Lost &amp; Found resmi mahasiswa Polmed
            </span>

            <h1 class="animate-fade-up mt-6 text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-slate-900 leading-[1.1]"
                style="animation-delay:.1s">
                Barang hilang?<br><span class="text-gradient">Cek di sini dulu.</span>
            </h1>
            <p class="animate-fade-up mt-5 text-slate-500 text-base md:text-lg max-w-xl mx-auto"
                style="animation-delay:.2s">
                Temukan barangmu yang tertinggal di kampus, atau bantu kembalikan barang orang lain ke pemiliknya.
            </p>

            {{-- Pencarian --}}
            <form method="GET" action="{{ route('home') }}" class="animate-fade-up mt-8" style="animation-delay:.3s">
                <div
                    class="group flex items-center gap-2 bg-white rounded-2xl p-2 shadow-xl shadow-slate-900/10 ring-1 ring-slate-200 focus-within:ring-2 focus-within:ring-emerald-400 transition">
                    <x-icon name="search" class="w-5 h-5 ml-3 text-slate-400 shrink-0" />
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Cari dompet, kunci, KTM, tumbler..."
                        class="flex-1 min-w-0 bg-transparent px-2 py-3 outline-none placeholder:text-slate-400">
                    <button
                        class="px-6 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-emerald-600 active:scale-95 transition">
                        Cari
                    </button>
                </div>
            </form>

            {{-- Tombol aksi --}}
            <div class="animate-fade-up mt-6 flex flex-col sm:flex-row gap-3 justify-center" style="animation-delay:.4s">
                <a href="{{ route('items.create', 'hilang') }}"
                    class="group inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-rose-500 text-white font-semibold shadow-lg shadow-rose-500/30 hover:bg-rose-600 hover:-translate-y-0.5 active:scale-95 transition">
                    <x-icon name="circle-help" class="w-5 h-5" />
                    Saya Kehilangan Barang
                    <x-icon name="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition" />
                </a>
                <a href="{{ route('items.create', 'temuan') }}"
                    class="group inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-emerald-600 text-white font-semibold shadow-lg shadow-emerald-600/30 hover:bg-emerald-700 hover:-translate-y-0.5 active:scale-95 transition">
                    <x-icon name="package-check" class="w-5 h-5" />
                    Saya Menemukan Barang
                    <x-icon name="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition" />
                </a>
            </div>
        </div>
    </section>

    {{-- ================= STATISTIK ================= --}}
    <section class="max-w-4xl mx-auto px-4 -mt-8 relative z-10">
        <div
            class="reveal grid grid-cols-3 bg-white rounded-3xl shadow-xl shadow-slate-900/5 ring-1 ring-slate-100 divide-x divide-slate-100">
            <div class="py-5 text-center">
                <p class="text-2xl md:text-4xl font-extrabold text-emerald-600" data-count="{{ $hitung['temuan'] }}">0</p>
                <p class="text-xs md:text-sm text-slate-500 mt-1">Barang Ditemukan</p>
            </div>
            <div class="py-5 text-center">
                <p class="text-2xl md:text-4xl font-extrabold text-rose-500" data-count="{{ $hitung['hilang'] }}">0</p>
                <p class="text-xs md:text-sm text-slate-500 mt-1">Sedang Dicari</p>
            </div>
            <div class="py-5 text-center">
                <p class="text-2xl md:text-4xl font-extrabold text-blue-600" data-count="{{ $totalKembali }}">0</p>
                <p class="text-xs md:text-sm text-slate-500 mt-1">Sudah Kembali</p>
            </div>
        </div>
    </section>

    {{-- ================= DAFTAR ================= --}}
    @php
        $tabUrl = fn($nilai) => request()->fullUrlWithQuery(['jenis' => $nilai, 'page' => null]) . '#daftar';
        $katUrl = fn($id) => request()->fullUrlWithQuery(['kategori' => $id, 'page' => null]) . '#daftar';
        $adaFilter = request()->filled('q') || request()->filled('kategori') || request()->filled('lokasi');
    @endphp

    <section id="daftar" class="scroll-mt-20 max-w-6xl mx-auto px-4 pt-16">

        <div class="reveal flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900">
                    {{ $jenis === 'kembali' ? 'Barang yang Sudah Kembali' : 'Postingan Terbaru' }}
                </h2>
                <p class="text-slate-500 mt-1">
                    {{ $jenis === 'kembali'
                        ? 'Cerita sukses: barang-barang ini sudah kembali ke pemiliknya.'
                        : 'Pilih tab untuk menyaring, atau gunakan filter di bawah.' }}
                </p>
            </div>

            {{-- TAB --}}
            <div
                class="inline-flex bg-white p-1 rounded-full shadow-sm ring-1 ring-slate-200 self-start overflow-x-auto no-scrollbar max-w-full">
                <a href="{{ $tabUrl(null) }}"
                    class="whitespace-nowrap px-4 py-2 rounded-full text-sm font-semibold transition {{ !$jenis ? 'bg-slate-900 text-white shadow' : 'text-slate-500 hover:text-slate-900' }}">
                    Semua <span class="opacity-70">({{ $hitung['semua'] }})</span>
                </a>
                <a href="{{ $tabUrl('temuan') }}"
                    class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold transition {{ $jenis === 'temuan' ? 'bg-emerald-600 text-white shadow' : 'text-slate-500 hover:text-emerald-600' }}">
                    <span class="w-2 h-2 rounded-full {{ $jenis === 'temuan' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                    Temuan <span class="opacity-70">({{ $hitung['temuan'] }})</span>
                </a>
                <a href="{{ $tabUrl('hilang') }}"
                    class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold transition {{ $jenis === 'hilang' ? 'bg-rose-500 text-white shadow' : 'text-slate-500 hover:text-rose-500' }}">
                    <span class="w-2 h-2 rounded-full {{ $jenis === 'hilang' ? 'bg-white' : 'bg-rose-500' }}"></span>
                    Hilang <span class="opacity-70">({{ $hitung['hilang'] }})</span>
                </a>
                <a href="{{ $tabUrl('kembali') }}"
                    class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold transition {{ $jenis === 'kembali' ? 'bg-blue-600 text-white shadow' : 'text-slate-500 hover:text-blue-600' }}">
                    <x-icon name="circle-check" class="w-4 h-4" />
                    Sudah Kembali <span class="opacity-70">({{ $hitung['kembali'] }})</span>
                </a>
            </div>
        </div>

        {{-- CHIP KATEGORI --}}
        <div class="reveal mt-6 flex gap-2 overflow-x-auto no-scrollbar pb-1">
            <a href="{{ $katUrl(null) }}"
                class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium border transition hover:-translate-y-0.5
                  {{ !request('kategori') ? 'bg-emerald-50 border-emerald-300 text-emerald-700' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300' }}">
                <x-icon name="sparkles" class="w-4 h-4" /> Semua
            </a>
            @foreach ($categories as $c)
                <a href="{{ $katUrl($c->id) }}"
                    class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium border transition hover:-translate-y-0.5
                      {{ request('kategori') == $c->id ? 'bg-emerald-50 border-emerald-300 text-emerald-700' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300' }}">
                    <x-icon :name="$c->ikon ?? 'package'" class="w-4 h-4" /> {{ $c->nama }}
                </a>
            @endforeach
        </div>

        {{-- FILTER LANJUTAN --}}
        <div x-data="{ open: {{ request()->filled('lokasi') || request()->filled('q') ? 'true' : 'false' }} }" class="mt-4">
            <button @click="open = !open" type="button"
                class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                <x-icon name="sliders" class="w-4 h-4" />
                Filter lanjutan
                <x-icon name="chevron-down" ::class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" />
            </button>

            <form x-show="open" x-cloak x-transition.opacity method="GET" action="{{ route('home') }}#daftar"
                class="mt-3 grid grid-cols-1 md:grid-cols-4 gap-3 bg-white p-4 rounded-2xl ring-1 ring-slate-200 shadow-sm">
                @if ($jenis)
                    <input type="hidden" name="jenis" value="{{ $jenis }}">
                @endif
                @if (request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif

                <input type="text" name="q" value="{{ request('q') }}" placeholder="Kata kunci..."
                    class="md:col-span-2 rounded-xl border border-slate-200 px-4 py-2.5 outline-none focus:ring-2 focus:ring-emerald-400">

                <select name="lokasi"
                    class="rounded-xl border border-slate-200 px-3 py-2.5 outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                    <option value="">Semua lokasi</option>
                    @foreach ($locations as $l)
                        <option value="{{ $l->id }}" @selected(request('lokasi') == $l->id)>{{ $l->nama }}</option>
                    @endforeach
                </select>

                <button
                    class="rounded-xl bg-slate-900 text-white font-semibold px-4 py-2.5 hover:bg-emerald-600 active:scale-95 transition">
                    Terapkan
                </button>
            </form>
        </div>

        @if ($adaFilter)
            <div class="mt-4">
                <a href="{{ route('home', array_filter(['jenis' => $jenis])) }}#daftar"
                    class="inline-flex items-center gap-2 text-sm px-3 py-1.5 rounded-full bg-slate-900 text-white hover:bg-rose-500 transition">
                    <x-icon name="x" class="w-4 h-4" /> Hapus semua filter
                </a>
            </div>
        @endif

        {{-- KARTU --}}
        @if ($items->count())
            <div class="mt-8 grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach ($items as $item)
                    @include('partials.item-card', ['item' => $item, 'i' => $loop->index])
                @endforeach
            </div>

            <div class="mt-10">{{ $items->fragment('daftar')->links() }}</div>
        @else
            <div class="reveal mt-10 text-center py-20 bg-white rounded-3xl ring-1 ring-slate-200">
                <span
                    class="inline-grid place-items-center w-20 h-20 rounded-full bg-slate-100 text-slate-400 animate-float">
                    <x-icon name="search-x" class="w-10 h-10" />
                </span>
                <p class="mt-4 font-bold text-lg">
                    {{ $jenis === 'kembali' ? 'Belum ada barang yang kembali' : 'Belum ada postingan yang cocok' }}</p>
                <p class="text-slate-500 text-sm mt-1">
                    {{ $jenis === 'kembali' ? 'Barang yang selesai diserahterimakan akan tampil di sini.' : 'Coba ganti kata kunci atau hapus filter.' }}
                </p>
            </div>
        @endif
    </section>

    {{-- ================= CARA KERJA ================= --}}
    <section id="cara-kerja" class="scroll-mt-20 max-w-6xl mx-auto px-4 pt-24">
        <div class="reveal text-center max-w-xl mx-auto">
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900">Gimana Cara Kerjanya?</h2>
            <p class="text-slate-500 mt-2">Tiga langkah sederhana supaya barang kembali ke pemiliknya.</p>
        </div>

        <div class="mt-10 grid md:grid-cols-3 gap-5">
            @foreach ([['1', 'file-text', 'Posting', 'Kehilangan atau menemukan barang? Buat postingan lengkap dengan foto dan lokasi.', 'from-rose-500 to-orange-400'], ['2', 'shield-check', 'Verifikasi', 'Pemilik mengajukan klaim beserta bukti. Penemu memeriksa kecocokannya.', 'from-amber-500 to-yellow-400'], ['3', 'arrow-left-right', 'Serah Terima', 'Bertemu, serahkan barang, dan unggah foto bukti penyerahan. Selesai!', 'from-emerald-500 to-cyan-400']] as $n => $s)
                <div class="reveal group relative bg-white rounded-3xl p-6 ring-1 ring-slate-200 hover:ring-transparent hover:shadow-2xl hover:shadow-slate-900/10 hover:-translate-y-1 transition duration-300"
                    style="transition-delay: {{ $n * 100 }}ms">
                    <span
                        class="absolute top-5 right-6 text-6xl font-extrabold text-slate-100 group-hover:text-slate-200 transition">{{ $s[0] }}</span>
                    <div
                        class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $s[4] }} text-white grid place-items-center shadow-lg group-hover:scale-110 group-hover:rotate-6 transition">
                        <x-icon :name="$s[1]" class="w-7 h-7" />
                    </div>
                    <h3 class="mt-5 text-lg font-bold">{{ $s[2] }}</h3>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed">{{ $s[3] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= CTA ================= --}}
    <section class="max-w-6xl mx-auto px-4 pt-24">
        <div class="reveal relative overflow-hidden rounded-[2rem] bg-slate-900 px-6 py-14 text-center">
            <div class="absolute -top-20 -left-10 w-72 h-72 bg-emerald-500/30 rounded-full blur-3xl animate-blob"></div>
            <div class="absolute -bottom-24 -right-10 w-72 h-72 bg-cyan-500/30 rounded-full blur-3xl animate-blob"
                style="animation-delay:-6s"></div>
            <div class="relative">
                <h2 class="text-2xl md:text-4xl font-extrabold text-white">Ada barang yang kamu temukan?</h2>
                <p class="mt-3 text-slate-300 max-w-md mx-auto">Satu postingan kecil bisa membuat harimu dan hari orang
                    lain jauh lebih baik.</p>
                <a href="{{ route('items.create', 'temuan') }}"
                    class="group inline-flex items-center gap-2 mt-7 px-8 py-3.5 rounded-2xl bg-white text-slate-900 font-bold hover:scale-105 active:scale-95 transition shadow-xl">
                    Mulai Posting
                    <x-icon name="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition" />
                </a>
            </div>
        </div>
    </section>

@endsection
