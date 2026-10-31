@extends('layouts.app')

@php
    $itemStatus = [
        'aktif' => ['Belum diklaim', 'bg-slate-100 text-slate-600', 'bg-slate-400'],
        'verifikasi' => ['Dalam verifikasi', 'bg-amber-100 text-amber-700', 'bg-amber-500'],
        'dikembalikan' => ['Sudah dikembalikan', 'bg-blue-100 text-blue-700', 'bg-blue-500'],
        'kedaluwarsa' => ['Kedaluwarsa', 'bg-slate-100 text-slate-500', 'bg-slate-400'],
    ];
    $claimStatus = [
        'menunggu' => ['Menunggu', 'bg-amber-100 text-amber-700'],
        'disetujui' => ['Disetujui', 'bg-emerald-100 text-emerald-700'],
        'ditolak' => ['Ditolak', 'bg-rose-100 text-rose-700'],
        'selesai' => ['Selesai', 'bg-blue-100 text-blue-700'],
    ];
    $tabAwal = in_array(request('tab'), ['postingan', 'masuk', 'saya'])
        ? request('tab')
        : ($stat['perlu'] > 0
            ? 'masuk'
            : 'postingan');
@endphp

@section('title', 'Dashboard - LostFound Polmed')

@section('content')

    {{-- ================= HEADER ================= --}}
    <section class="max-w-6xl mx-auto px-4 pt-28">
        <div class="relative overflow-hidden rounded-3xl bg-slate-900 p-7 sm:p-9 text-white animate-fade-up">
            <div class="absolute -top-16 -right-10 w-72 h-72 bg-emerald-500/30 rounded-full blur-3xl animate-blob"></div>
            <div class="absolute -bottom-20 left-1/3 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl animate-blob"
                style="animation-delay:-6s"></div>

            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <p class="text-emerald-300 text-sm font-semibold">Dashboard</p>
                    <h1 class="mt-1 text-2xl md:text-3xl font-extrabold tracking-tight">Halo,
                        {{ Str::before(auth()->user()->name, ' ') }}!</h1>
                    <p class="mt-2 text-slate-300 text-sm">
                        @if ($stat['perlu'] > 0)
                            Ada <span class="font-bold text-amber-300">{{ $stat['perlu'] }} klaim</span> yang menunggu
                            pemeriksaanmu.
                        @else
                            Kelola postingan dan klaimmu di sini.
                        @endif
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('items.create', 'hilang') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-500 px-5 py-3 text-sm font-semibold hover:bg-rose-600 hover:-translate-y-0.5 active:scale-95 transition">
                        <x-icon name="plus" class="w-4 h-4" /> Lapor kehilangan
                    </a>
                    <a href="{{ route('items.create', 'temuan') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold hover:bg-emerald-700 hover:-translate-y-0.5 active:scale-95 transition">
                        <x-icon name="plus" class="w-4 h-4" /> Lapor temuan
                    </a>
                </div>
            </div>
        </div>

        {{-- STATISTIK --}}
        <div class="mt-5 grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([['Postingan aktif', $stat['aktif'], 'file-text', 'bg-emerald-100 text-emerald-600'], ['Perlu diperiksa', $stat['perlu'], 'inbox', 'bg-amber-100 text-amber-600'], ['Sudah kembali', $stat['dikembalikan'], 'circle-check', 'bg-blue-100 text-blue-600'], ['Klaim saya', $stat['klaimSaya'], 'shield-check', 'bg-slate-100 text-slate-600']] as $n => $s)
                <div class="reveal bg-white rounded-3xl ring-1 ring-slate-200 p-5 hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/5 transition"
                    style="transition-delay: {{ $n * 80 }}ms">
                    <span class="w-11 h-11 rounded-2xl grid place-items-center {{ $s[3] }}"><x-icon
                            :name="$s[2]" class="w-5 h-5" /></span>
                    <p class="mt-4 text-3xl font-extrabold text-slate-900" data-count="{{ $s[1] }}">0</p>
                    <p class="text-sm text-slate-500">{{ $s[0] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= TAB ================= --}}
    <section class="max-w-6xl mx-auto px-4 pt-10 pb-6" x-data="{ tab: '{{ $tabAwal }}' }">

        <div
            class="inline-flex bg-white p-1 rounded-full shadow-sm ring-1 ring-slate-200 overflow-x-auto no-scrollbar max-w-full">
            @foreach ([['postingan', 'Postingan Saya', $items->count(), 'bg-slate-900'], ['masuk', 'Klaim Masuk', $stat['perlu'], 'bg-amber-500'], ['saya', 'Klaim Saya', $keluar->count(), 'bg-slate-900']] as $t)
                <button type="button" @click="tab = '{{ $t[0] }}'"
                    :class="tab === '{{ $t[0] }}' ? '{{ $t[3] }} text-white shadow' :
                        'text-slate-500 hover:text-slate-900'"
                    class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold transition">
                    {{ $t[1] }}
                    @if ($t[2] > 0)
                        <span class="min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold grid place-items-center"
                            :class="tab === '{{ $t[0] }}' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600'">{{ $t[2] }}</span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- ========== TAB: POSTINGAN SAYA ========== --}}
        <div x-show="tab === 'postingan'" x-transition.opacity class="mt-6 space-y-3">
            @forelse($items as $it)
                @php
                    $s = $itemStatus[$it->status] ?? $itemStatus['aktif'];
                    $tm = $it->jenis === 'temuan';
                @endphp
                <a href="{{ route('items.show', $it) }}"
                    class="group flex items-center gap-4 bg-white rounded-3xl ring-1 ring-slate-200 p-4 hover:shadow-xl hover:shadow-slate-900/5 hover:-translate-y-0.5 transition">
                    <div
                        class="w-20 h-20 rounded-2xl overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 shrink-0 grid place-items-center text-slate-300">
                        @if ($it->foto_utama)
                            <img src="{{ $it->foto_utama }}" alt=""
                                class="w-full h-full object-cover {{ $it->status === 'dikembalikan' ? 'grayscale' : '' }}">
                        @else
                            <x-icon :name="$it->category->ikon ?? 'package'" class="w-9 h-9" />
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold text-white {{ $tm ? 'bg-emerald-600' : 'bg-rose-500' }}">{{ $tm ? 'DITEMUKAN' : 'DICARI' }}</span>
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $s[1] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $s[2] }}"></span>{{ $s[0] }}
                            </span>
                        </div>
                        <h3 class="mt-1.5 font-bold text-slate-900 truncate group-hover:text-emerald-600 transition">
                            {{ $it->judul }}</h3>
                        <p class="text-xs text-slate-400">Diposting {{ $it->created_at->diffForHumans() }}</p>
                    </div>

                    @if ($it->klaim_menunggu_count > 0)
                        <span
                            class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">
                            <x-icon name="inbox" class="w-3.5 h-3.5" /> {{ $it->klaim_menunggu_count }} klaim
                        </span>
                    @endif
                    <x-icon name="arrow-right"
                        class="w-4 h-4 text-slate-300 group-hover:text-emerald-500 group-hover:translate-x-1 transition shrink-0" />
                </a>
            @empty
                @include('partials.dashboard-empty', [
                    'icon' => 'file-text',
                    'title' => 'Belum ada postingan',
                    'text' => 'Kehilangan atau menemukan barang? Buat postingan pertamamu.',
                ])
            @endforelse
        </div>

        {{-- ========== TAB: KLAIM MASUK ========== --}}
        <div x-show="tab === 'masuk'" x-cloak x-transition.opacity class="mt-6 space-y-3">
            @forelse($masuk as $c)
                @php
                    $cs = $claimStatus[$c->status];
                    $tm = $c->item->jenis === 'temuan';
                @endphp
                <a href="{{ route('claims.show', $c) }}"
                    class="group flex items-center gap-4 bg-white rounded-3xl ring-1 p-4 hover:shadow-xl hover:shadow-slate-900/5 hover:-translate-y-0.5 transition
                      {{ $c->status === 'menunggu' ? 'ring-amber-300' : 'ring-slate-200' }}">
                    <span
                        class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-cyan-500 text-white grid place-items-center font-bold shrink-0">
                        {{ strtoupper(mb_substr($c->claimant->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-slate-900 truncate">{{ $c->claimant->name }}</p>
                        <p class="text-sm text-slate-500 truncate">
                            {{ $tm ? 'mengklaim' : 'melaporkan menemukan' }}
                            <span class="font-semibold text-slate-700">{{ $c->item->judul }}</span>
                        </p>
                        <p class="text-xs text-slate-400">{{ $c->created_at->diffForHumans() }}</p>
                    </div>
                    <span
                        class="px-3 py-1 rounded-full text-xs font-semibold {{ $cs[1] }}">{{ $cs[0] }}</span>
                    @if ($c->status === 'menunggu')
                        <span
                            class="hidden sm:inline-flex rounded-xl bg-slate-900 text-white px-4 py-2 text-xs font-semibold group-hover:bg-emerald-600 transition">Periksa</span>
                    @else
                        <x-icon name="arrow-right"
                            class="w-4 h-4 text-slate-300 group-hover:text-emerald-500 group-hover:translate-x-1 transition shrink-0" />
                    @endif
                </a>
            @empty
                @include('partials.dashboard-empty', [
                    'icon' => 'inbox',
                    'title' => 'Belum ada klaim masuk',
                    'text' => 'Klaim atau laporan dari orang lain untuk postinganmu akan muncul di sini.',
                ])
            @endforelse
        </div>

        {{-- ========== TAB: KLAIM SAYA ========== --}}
        <div x-show="tab === 'saya'" x-cloak x-transition.opacity class="mt-6 space-y-3">
            @forelse($keluar as $c)
                @php
                    $cs = $claimStatus[$c->status];
                    $tm = $c->item->jenis === 'temuan';
                @endphp
                <a href="{{ route('claims.show', $c) }}"
                    class="group flex items-center gap-4 bg-white rounded-3xl ring-1 ring-slate-200 p-4 hover:shadow-xl hover:shadow-slate-900/5 hover:-translate-y-0.5 transition">
                    <div
                        class="w-16 h-16 rounded-2xl overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 shrink-0 grid place-items-center text-slate-300">
                        @if ($c->item->foto_utama)
                            <img src="{{ $c->item->foto_utama }}" alt="" class="w-full h-full object-cover">
                        @else
                            <x-icon :name="$c->item->category->ikon ?? 'package'" class="w-8 h-8" />
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <span
                            class="px-2 py-0.5 rounded-full text-[10px] font-extrabold text-white {{ $tm ? 'bg-emerald-600' : 'bg-rose-500' }}">
                            {{ $tm ? 'KLAIM KEPEMILIKAN' : 'LAPOR TEMUAN' }}
                        </span>
                        <p class="mt-1 font-bold text-slate-900 truncate group-hover:text-emerald-600 transition">
                            {{ $c->item->judul }}</p>
                        <p class="text-xs text-slate-400">Diajukan {{ $c->created_at->diffForHumans() }}</p>
                    </div>
                    <span
                        class="px-3 py-1 rounded-full text-xs font-semibold {{ $cs[1] }}">{{ $cs[0] }}</span>
                    <x-icon name="arrow-right"
                        class="w-4 h-4 text-slate-300 group-hover:text-emerald-500 group-hover:translate-x-1 transition shrink-0" />
                </a>
            @empty
                @include('partials.dashboard-empty', [
                    'icon' => 'shield-check',
                    'title' => 'Belum ada klaim',
                    'text' => 'Kalau kamu mengklaim barang atau melaporkan penemuan, riwayatnya muncul di sini.',
                ])
            @endforelse
        </div>
    </section>

@endsection
