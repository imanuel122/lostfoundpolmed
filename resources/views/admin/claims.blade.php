@extends('layouts.app')

@php
    $cs = [
        'menunggu' => ['Menunggu', 'bg-amber-100 text-amber-700'],
        'disetujui' => ['Disetujui', 'bg-emerald-100 text-emerald-700'],
        'ditolak' => ['Ditolak', 'bg-rose-100 text-rose-700'],
        'selesai' => ['Selesai', 'bg-blue-100 text-blue-700'],
    ];
@endphp

@section('title', 'Klaim - Panel Admin')

@section('content')
    @include('admin._nav', ['active' => 'claims', 'judul' => 'Kelola Klaim'])

    <section class="max-w-6xl mx-auto px-4 pt-6 pb-6">

        <form method="GET" class="grid grid-cols-2 md:grid-cols-4 gap-3 bg-white p-4 rounded-2xl ring-1 ring-slate-200">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama barang..."
                class="col-span-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-400">
            <select name="status"
                class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                <option value="">Semua status</option>
                @foreach ($cs as $k => $v)
                    <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v[0] }}</option>
                @endforeach
            </select>
            <button
                class="rounded-xl bg-slate-900 text-white text-sm font-semibold px-4 py-2.5 hover:bg-emerald-600 active:scale-95 transition">Terapkan</button>
        </form>

        <div class="mt-5 bg-white rounded-3xl ring-1 ring-slate-200 divide-y divide-slate-100 overflow-hidden">
            @forelse($claims as $c)
                @php
                    $tm = $c->item->jenis === 'temuan';
                    $s = $cs[$c->status];
                @endphp
                <a href="{{ route('claims.show', $c) }}"
                    class="group flex items-center gap-3 p-4 hover:bg-slate-50 transition">
                    <span
                        class="w-11 h-11 rounded-full bg-gradient-to-br from-emerald-500 to-cyan-500 text-white grid place-items-center font-bold shrink-0">
                        {{ strtoupper(mb_substr($c->claimant->name, 0, 1)) }}
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block font-bold text-slate-900 truncate">{{ $c->claimant->name }}</span>
                        <span class="block text-sm text-slate-500 truncate">
                            {{ $tm ? 'mengklaim' : 'melaporkan menemukan' }}
                            <span class="font-semibold text-slate-700">{{ $c->item->judul }}</span>
                        </span>
                        <span class="block text-xs text-slate-400">{{ $c->created_at->diffForHumans() }}</span>
                    </span>
                    <span
                        class="hidden sm:inline px-2.5 py-0.5 rounded-full text-[10px] font-extrabold text-white {{ $tm ? 'bg-emerald-600' : 'bg-rose-500' }}">{{ $tm ? 'KLAIM' : 'LAPOR' }}</span>
                    <span
                        class="px-3 py-1 rounded-full text-xs font-semibold {{ $s[1] }}">{{ $s[0] }}</span>
                    <x-icon name="arrow-right"
                        class="w-4 h-4 text-slate-300 group-hover:text-emerald-500 group-hover:translate-x-1 transition shrink-0" />
                </a>
            @empty
                <p class="py-14 text-center text-sm text-slate-400">Tidak ada klaim yang cocok.</p>
            @endforelse
        </div>

        <div class="mt-6">{{ $claims->links() }}</div>
    </section>
@endsection
