@php
    $status = [
        'aktif' => ['Belum diklaim', 'bg-slate-100 text-slate-600', 'bg-slate-400'],
        'verifikasi' => ['Dalam verifikasi', 'bg-amber-100 text-amber-700', 'bg-amber-500'],
        'dikembalikan' => ['Sudah dikembalikan', 'bg-blue-100 text-blue-700', 'bg-blue-500'],
    ][$item->status] ?? ['-', 'bg-slate-100 text-slate-600', 'bg-slate-400'];

    $temuan = $item->jenis === 'temuan';
    $ikon = $item->category->ikon ?? 'package';
    $redup = $item->status === 'dikembalikan' && request('jenis') !== 'kembali';
@endphp

<div class="reveal" style="transition-delay: {{ ($i % 4) * 90 }}ms">
    <a href="{{ route('items.show', $item) }}"
        class="group block h-full bg-white rounded-3xl overflow-hidden ring-1 ring-slate-200
              hover:ring-transparent hover:shadow-2xl hover:shadow-slate-900/10 hover:-translate-y-1.5
              transition duration-300 {{ $redup ? 'opacity-75' : '' }}">

        {{-- FOTO --}}
        <div class="relative aspect-[4/3] bg-gradient-to-br from-slate-100 to-slate-200 overflow-hidden">
            @if ($item->foto_utama)
                <img src="{{ $item->foto_utama }}" alt="{{ $item->judul }}" loading="lazy"
                    class="w-full h-full object-cover group-hover:scale-110 transition duration-500
                            {{ $redup ? 'grayscale' : '' }}">
            @else
                <div
                    class="w-full h-full grid place-items-center text-slate-300 group-hover:text-emerald-400 group-hover:scale-110 group-hover:rotate-3 transition duration-500">
                    <x-icon :name="$ikon" class="w-16 h-16" />
                </div>
            @endif

            <div
                class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition">
            </div>

            {{-- Label jenis --}}
            <span
                class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold tracking-wide text-white shadow-lg backdrop-blur
                         {{ $temuan ? 'bg-emerald-600/95' : 'bg-rose-500/95' }}">
                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                {{ $temuan ? 'DITEMUKAN' : 'DICARI' }}
            </span>

            @if ($item->status === 'dikembalikan')
                <span
                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-blue-600 text-white grid place-items-center shadow-lg">
                    <x-icon name="check" class="w-4 h-4" />
                </span>
            @endif
        </div>

        {{-- ISI --}}
        <div class="p-4">
            <p
                class="inline-flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                <x-icon :name="$ikon" class="w-3.5 h-3.5" />
                {{ $item->category->nama ?? '' }}
            </p>
            <h3 class="mt-1 font-bold text-slate-900 truncate group-hover:text-emerald-600 transition">
                {{ $item->judul }}</h3>

            <div class="mt-3 space-y-1.5 text-sm text-slate-500">
                <p class="flex items-center gap-2 truncate">
                    <x-icon name="map-pin" class="w-4 h-4 shrink-0 text-slate-400" />
                    <span class="truncate">{{ $item->location->nama }}</span>
                </p>
                <p class="flex items-center gap-2">
                    <x-icon name="calendar" class="w-4 h-4 shrink-0 text-slate-400" />
                    {{ $item->tanggal_kejadian->translatedFormat('d M Y') }}
                </p>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <span
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $status[1] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $status[2] }}"></span>
                    {{ $status[0] }}
                </span>
                <x-icon name="arrow-right"
                    class="w-4 h-4 text-slate-300 group-hover:text-emerald-500 group-hover:translate-x-1 transition" />
            </div>
        </div>
    </a>
</div>
