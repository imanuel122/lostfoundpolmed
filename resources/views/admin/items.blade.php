@extends('layouts.app')

@php
    $statusBadge = [
        'aktif' => ['Belum diklaim', 'bg-slate-100 text-slate-600'],
        'verifikasi' => ['Verifikasi', 'bg-amber-100 text-amber-700'],
        'dikembalikan' => ['Dikembalikan', 'bg-blue-100 text-blue-700'],
        'kedaluwarsa' => ['Kedaluwarsa', 'bg-slate-100 text-slate-500'],
        'dihapus' => ['Dihapus', 'bg-rose-100 text-rose-700'],
    ];
@endphp

@section('title', 'Postingan - Panel Admin')

@section('content')
    @include('admin._nav', ['active' => 'items', 'judul' => 'Kelola Postingan'])

    <section x-data class="max-w-6xl mx-auto px-4 pt-6 pb-6">

        <form method="GET" class="grid grid-cols-2 md:grid-cols-5 gap-3 bg-white p-4 rounded-2xl ring-1 ring-slate-200">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama barang..."
                class="col-span-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-400">
            <select name="jenis"
                class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                <option value="">Semua jenis</option>
                <option value="temuan" @selected(request('jenis') === 'temuan')>Temuan</option>
                <option value="hilang" @selected(request('jenis') === 'hilang')>Hilang</option>
            </select>
            <select name="status"
                class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                <option value="">Semua status</option>
                @foreach ($statusBadge as $k => $v)
                    <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v[0] }}</option>
                @endforeach
            </select>
            <button
                class="rounded-xl bg-slate-900 text-white text-sm font-semibold px-4 py-2.5 hover:bg-emerald-600 active:scale-95 transition">Terapkan</button>
        </form>

        <div class="mt-5 bg-white rounded-3xl ring-1 ring-slate-200 divide-y divide-slate-100 overflow-hidden">
            @forelse($items as $it)
                @php $sb = $statusBadge[$it->status]; @endphp
                <div
                    class="flex flex-col md:flex-row md:items-center gap-3 p-4 {{ $it->status === 'dihapus' ? 'bg-rose-50/40' : '' }}">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold text-white {{ $it->jenis === 'temuan' ? 'bg-emerald-600' : 'bg-rose-500' }}">{{ $it->jenis === 'temuan' ? 'DITEMUKAN' : 'DICARI' }}</span>
                            <span
                                class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $sb[1] }}">{{ $sb[0] }}</span>
                        </div>
                        <a href="{{ route('items.show', $it) }}"
                            class="mt-1 block font-bold text-slate-900 truncate hover:text-emerald-600 transition">{{ $it->judul }}</a>
                        <p class="text-xs text-slate-400 truncate">
                            {{ $it->category->nama }} · {{ $it->location->nama }} · oleh {{ $it->user->name }} ·
                            {{ $it->claims_count }} klaim · {{ $it->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('items.show', $it) }}"
                            class="rounded-xl bg-slate-50 ring-1 ring-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">Lihat</a>

                        @if ($it->status === 'dihapus')
                            <button type="button"
                                @click="$dispatch('open-confirm', {{ Js::from([
                                    'action' => route('admin.items.restore', $it),
                                    'title' => 'Pulihkan postingan?',
                                    'text' => '"' . $it->judul . '" akan tampil kembali di beranda.',
                                    'button' => 'Ya, pulihkan',
                                    'tone' => 'emerald',
                                ]) }})"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 ring-1 ring-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100 transition">
                                <x-icon name="rotate-ccw" class="w-4 h-4" /> Pulihkan
                            </button>
                        @else
                            <button type="button"
                                @click="$dispatch('open-confirm', {{ Js::from([
                                    'action' => route('admin.items.destroy', $it),
                                    'title' => 'Hapus postingan ini?',
                                    'text' => '"' . $it->judul . '" disembunyikan dari beranda. Datanya tetap tersimpan dan bisa dipulihkan.',
                                    'button' => 'Ya, hapus',
                                    'tone' => 'rose',
                                    'reason' => true,
                                    'label' => 'Alasan penghapusan',
                                ]) }})"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-rose-50 ring-1 ring-rose-200 px-4 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-100 transition">
                                <x-icon name="trash-2" class="w-4 h-4" /> Hapus
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <p class="py-14 text-center text-sm text-slate-400">Tidak ada postingan yang cocok.</p>
            @endforelse
        </div>

        <div class="mt-6">{{ $items->links() }}</div>
    </section>
@endsection
