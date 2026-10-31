@extends('layouts.app')

@section('title', $judul . ' - Panel Admin')

@section('content')
    @include('admin._nav', ['active' => $type, 'judul' => 'Kelola ' . $judul])

    <section class="max-w-3xl mx-auto px-4 pt-6 pb-6 space-y-5">

        {{-- TAMBAH --}}
        <form method="POST" action="{{ route('admin.master.store', $type) }}"
            class="bg-white rounded-3xl ring-1 ring-slate-200 p-5 flex flex-col sm:flex-row gap-3">
            @csrf
            <input type="text" name="nama" value="{{ old('nama') }}"
                placeholder="{{ $type === 'kategori' ? 'Nama kategori baru' : 'Nama lokasi baru' }}" required
                class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-400">
            @if ($pakaiIkon)
                <select name="ikon" required
                    class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                    <option value="">Pilih ikon</option>
                    @foreach ($ikonList as $ic)
                        <option value="{{ $ic }}" @selected(old('ikon') === $ic)>{{ $ic }}</option>
                    @endforeach
                </select>
            @endif
            <button
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-600/30 hover:-translate-y-0.5 active:scale-95 transition">
                <x-icon name="plus" class="w-4 h-4" /> Tambah
            </button>
        </form>

        {{-- DAFTAR --}}
        <div class="bg-white rounded-3xl ring-1 ring-slate-200 divide-y divide-slate-100 overflow-hidden">
            @foreach ($rows as $r)
                @php $kunci = $r->nama === 'Lainnya' || $r->items_count > 0; @endphp
                <div x-data="{ edit: false }" class="px-4 py-3">

                    <div x-show="!edit" class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 grid place-items-center shrink-0">
                            <x-icon :name="$pakaiIkon ? $r->ikon ?? 'package' : 'map-pin'" class="w-5 h-5" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900 truncate">{{ $r->nama }}</p>
                            <p class="text-xs text-slate-400">{{ $r->items_count }} postingan</p>
                        </div>

                        <button type="button" @click="edit = true" aria-label="Ubah"
                            class="w-9 h-9 grid place-items-center rounded-lg text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition">
                            <x-icon name="pencil" class="w-4 h-4" />
                        </button>

                        @if ($kunci)
                            <span class="w-9 h-9 grid place-items-center text-slate-200"
                                title="{{ $r->nama === 'Lainnya' ? 'Tidak bisa dihapus' : 'Masih dipakai postingan' }}">
                                <x-icon name="lock" class="w-4 h-4" />
                            </span>
                        @else
                            <button type="button" aria-label="Hapus"
                                @click="$dispatch('open-confirm', {{ Js::from([
                                    'action' => route('admin.master.destroy', [$type, $r->id]),
                                    'title' => 'Hapus ' . strtolower($judul) . ' ini?',
                                    'text' => '"' . $r->nama . '" akan dihapus permanen.',
                                    'button' => 'Ya, hapus',
                                    'tone' => 'rose',
                                ]) }})"
                                class="w-9 h-9 grid place-items-center rounded-lg text-rose-400 hover:text-rose-600 hover:bg-rose-50 transition">
                                <x-icon name="trash-2" class="w-4 h-4" />
                            </button>
                        @endif
                    </div>

                    <form x-show="edit" x-cloak method="POST" action="{{ route('admin.master.update', [$type, $r->id]) }}"
                        class="flex flex-col sm:flex-row gap-2">
                        @csrf
                        <input type="text" name="nama" value="{{ $r->nama }}" required
                            class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-400">
                        @if ($pakaiIkon)
                            <select name="ikon"
                                class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
                                @foreach ($ikonList as $ic)
                                    <option value="{{ $ic }}" @selected($r->ikon === $ic)>{{ $ic }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        <button
                            class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-600 active:scale-95 transition">Simpan</button>
                        <button type="button" @click="edit = false"
                            class="rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-500 hover:bg-slate-100 transition">Batal</button>
                    </form>
                </div>
            @endforeach
        </div>

        <p class="text-xs text-slate-400 flex items-center gap-1.5">
            <x-icon name="info" class="w-4 h-4 shrink-0" />
            Yang sudah dipakai postingan atau bernama "Lainnya" tidak bisa dihapus (ikon gembok). Mengubah nama aman,
            postingan lama ikut berganti nama.
        </p>
    </section>
@endsection
