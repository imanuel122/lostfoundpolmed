@extends('layouts.app')

@section('title', 'Panel Admin - LostFound Polmed')

@section('content')
    @include('admin._nav', ['active' => 'index', 'judul' => 'Ringkasan'])

    <section x-data class="max-w-6xl mx-auto px-4 pt-6 pb-6 space-y-6">

        {{-- STATISTIK --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            @foreach ([['Total postingan', $stat['total'], 'file-text', 'bg-slate-100 text-slate-600', route('admin.items')], ['Masih dicari', $stat['aktif'], 'search', 'bg-emerald-100 text-emerald-600', route('admin.items', ['status' => 'aktif'])], ['Sudah kembali', $stat['kembali'], 'circle-check', 'bg-blue-100 text-blue-600', route('admin.items', ['status' => 'dikembalikan'])], ['Klaim menunggu', $stat['menunggu'], 'inbox', 'bg-amber-100 text-amber-600', route('admin.claims', ['status' => 'menunggu'])], ['Pengguna', $stat['users'], 'users', 'bg-indigo-100 text-indigo-600', route('admin.users')]] as $n => $s)
                <a href="{{ $s[4] }}"
                    class="reveal bg-white rounded-3xl ring-1 ring-slate-200 p-5 hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/5 transition"
                    style="transition-delay: {{ $n * 70 }}ms">
                    <span class="w-11 h-11 rounded-2xl grid place-items-center {{ $s[3] }}"><x-icon :name="$s[2]"
                            class="w-5 h-5" /></span>
                    <p class="mt-4 text-3xl font-extrabold text-slate-900" data-count="{{ $s[1] }}">0</p>
                    <p class="text-sm text-slate-500">{{ $s[0] }}</p>
                </a>
            @endforeach
        </div>

        {{-- TINGKAT KEBERHASILAN --}}
        <div class="reveal bg-white rounded-3xl ring-1 ring-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900">Tingkat keberhasilan</h2>
                    <p class="text-sm text-slate-500">Persentase postingan yang berakhir dengan barang kembali.</p>
                </div>
                <p class="text-3xl font-extrabold text-emerald-600">{{ $stat['tingkat'] }}%</p>
            </div>
            <div class="mt-4 h-3 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-cyan-500 transition-all duration-1000"
                    style="width: {{ $stat['tingkat'] }}%"></div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">

            {{-- PERLU PERHATIAN --}}
            <div class="reveal bg-white rounded-3xl ring-1 ring-slate-200 p-6">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 grid place-items-center"><x-icon
                            name="triangle-alert" class="w-5 h-5" /></span>
                    <div>
                        <h2 class="font-bold text-slate-900">Perlu perhatian</h2>
                        <p class="text-xs text-slate-500">Klaim disetujui yang prosesnya tersendat.</p>
                    </div>
                </div>

                <div class="mt-4 space-y-2">
                    @forelse($macet as $c)
                        <a href="{{ route('claims.show', $c) }}"
                            class="group flex items-center gap-3 rounded-2xl bg-slate-50 ring-1 ring-slate-200 px-4 py-3 hover:ring-amber-300 transition">
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-bold text-slate-800 truncate">{{ $c->item->judul }}</span>
                                <span class="block text-xs text-slate-500 truncate">
                                    {{ $c->handed_over_at
                                        ? 'Bukti diunggah ' . $c->handed_over_at->diffForHumans() . ', ' . $c->claimant->name . ' belum konfirmasi'
                                        : 'Disetujui ' . $c->reviewed_at?->diffForHumans() . ', belum ada bukti serah terima' }}
                                </span>
                            </span>
                            <x-icon name="arrow-right"
                                class="w-4 h-4 text-slate-300 group-hover:text-amber-500 group-hover:translate-x-1 transition shrink-0" />
                        </a>
                    @empty
                        <p class="text-sm text-slate-400 py-6 text-center">Semua lancar. Tidak ada yang macet.</p>
                    @endforelse
                </div>
            </div>

            {{-- KEDALUWARSA --}}
            <div class="reveal bg-white rounded-3xl ring-1 ring-slate-200 p-6">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 grid place-items-center"><x-icon
                            name="clock" class="w-5 h-5" /></span>
                    <div>
                        <h2 class="font-bold text-slate-900">Postingan kedaluwarsa</h2>
                        <p class="text-xs text-slate-500">Postingan aktif lewat 60 hari tanpa klaim.</p>
                    </div>
                </div>
                <p class="mt-4 text-sm text-slate-500 leading-relaxed">
                    Perintah ini mengubah status postingan yang sudah lewat batas waktu menjadi <b>kedaluwarsa</b> dan
                    memberi tahu pembuatnya.
                    Yang sedang diverifikasi tidak ikut diubah.
                </p>
                <button type="button"
                    @click="$dispatch('open-confirm', {{ Js::from([
                        'action' => route('admin.expire'),
                        'title' => 'Jalankan pengecekan kedaluwarsa?',
                        'text' => 'Postingan aktif yang melewati batas waktu akan ditandai kedaluwarsa.',
                        'button' => 'Ya, jalankan',
                        'tone' => 'emerald',
                    ]) }})"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-600 hover:-translate-y-0.5 active:scale-95 transition">
                    <x-icon name="clock" class="w-4 h-4" /> Jalankan sekarang
                </button>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            @foreach ([['judul' => 'Lokasi paling sering', 'rows' => $topLokasi, 'ikon' => 'map-pin', 'warna' => 'from-emerald-500 to-cyan-500'], ['judul' => 'Kategori paling sering', 'rows' => $topKategori, 'ikon' => 'tag', 'warna' => 'from-rose-500 to-orange-400']] as $b)
                @php $max = max($b['rows']->max('items_count') ?? 0, 1); @endphp
                <div class="reveal bg-white rounded-3xl ring-1 ring-slate-200 p-6">
                    <h2 class="font-bold text-slate-900 flex items-center gap-2">
                        <x-icon :name="$b['ikon']" class="w-4 h-4 text-slate-400" /> {{ $b['judul'] }}
                    </h2>
                    <div class="mt-4 space-y-3.5">
                        @forelse($b['rows'] as $r)
                            <div>
                                <div class="flex justify-between text-sm">
                                    <span class="font-semibold text-slate-700 truncate">{{ $r->nama }}</span>
                                    <span class="text-slate-400">{{ $r->items_count }}</span>
                                </div>
                                <div class="mt-1.5 h-2 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r {{ $b['warna'] }}"
                                        style="width: {{ ($r->items_count / $max) * 100 }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400">Belum ada data.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        {{-- POSTINGAN TERBARU --}}
        <div class="reveal bg-white rounded-3xl ring-1 ring-slate-200 p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-slate-900">Postingan terbaru</h2>
                <a href="{{ route('admin.items') }}" class="text-sm font-semibold text-emerald-600 hover:underline">Lihat
                    semua</a>
            </div>
            <div class="mt-3 divide-y divide-slate-100">
                @php
                    /** @var \Illuminate\Support\Collection<int, \App\Models\Item> $terbaru */
                @endphp
                @foreach ($terbaru as $it)
                    <a href="{{ route('items.show', $it) }}"
                        class="flex items-center gap-3 py-3 hover:bg-slate-50 -mx-2 px-2 rounded-xl transition">
                        <span
                            class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold text-white {{ $it->jenis === 'temuan' ? 'bg-emerald-600' : 'bg-rose-500' }}">{{ $it->jenis === 'temuan' ? 'DITEMUKAN' : 'DICARI' }}</span>
                        <span
                            class="min-w-0 flex-1 truncate text-sm font-semibold text-slate-800">{{ $it->judul }}</span>
                        <span class="hidden sm:block text-xs text-slate-400">{{ $it->user->name }}</span>
                        <span class="text-xs text-slate-400">{{ $it->created_at->diffForHumans() }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
