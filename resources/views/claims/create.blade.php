@extends('layouts.app')

@php
    $temuan = $item->jenis === 'temuan';

    $t = $temuan
        ? [
            'judul' => 'Ajukan Klaim Kepemilikan',
            'sub' => 'Buktikan bahwa barang ini milikmu. Pembuat postingan akan memeriksanya.',
            'hero' => 'from-emerald-50',
            'blob1' => 'bg-emerald-300/40',
            'blob2' => 'bg-cyan-300/40',
            'num' => 'bg-emerald-600',
            'btn' => 'from-emerald-600 to-teal-600 shadow-emerald-600/30',
            'drop' => 'border-emerald-300 bg-emerald-50/60',
            'bukti' => 'Jelaskan ciri barangmu',
            'ph' =>
                'Contoh: Di dalam ada KTP atas nama saya, dua kartu ATM BRI, dan foto keluarga. Ada goresan di sudut kanan bawah.',
            'hint' =>
                'Sebutkan ciri yang tidak terlihat di foto postingan. Ini yang dicocokkan dengan ciri rahasia penemu.',
            'fotoSub' => 'Opsional. Misalnya foto lama kamu membawa barang ini, nota, atau kotak pembeliannya.',
        ]
        : [
            'judul' => 'Lapor: Saya Melihat Barang Ini',
            'sub' => 'Ceritakan barang yang kamu temukan supaya pemiliknya bisa memastikan.',
            'hero' => 'from-rose-50',
            'blob1' => 'bg-rose-300/40',
            'blob2' => 'bg-orange-200/50',
            'num' => 'bg-rose-500',
            'btn' => 'from-rose-500 to-orange-500 shadow-rose-500/30',
            'drop' => 'border-rose-300 bg-rose-50/60',
            'bukti' => 'Ceritakan kondisi barang yang kamu temukan',
            'ph' =>
                'Contoh: Saya menemukan kunci motor Honda dengan gantungan biru, ditemukan di dekat tangga parkiran.',
            'hint' => 'Jelaskan warna, kondisi, dan di mana serta kapan kamu menemukannya.',
            'fotoSub' => 'Disarankan. Foto barang yang kamu temukan membantu pemilik memastikan.',
        ];

    $no = 0;
    $ikon = $item->category->ikon ?? 'package';
    $photoError = $errors->first('photos') ?: $errors->first('photos.*');
@endphp

@section('title', $t['judul'] . ' - LostFound Polmed')

@section('content')

    <section class="relative overflow-hidden pt-28 pb-8 bg-gradient-to-b {{ $t['hero'] }} via-white to-slate-50">
        <div class="absolute -top-24 -left-24 w-96 h-96 {{ $t['blob1'] }} rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-10 -right-24 w-80 h-80 {{ $t['blob2'] }} rounded-full blur-3xl animate-blob"
            style="animation-delay:-5s"></div>

        <div class="relative max-w-6xl mx-auto px-4">
            <a href="{{ route('items.show', $item) }}"
                class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-slate-900 transition">
                <x-icon name="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition" />
                Kembali ke detail barang
            </a>
            <div class="mt-5 animate-fade-up">
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">{{ $t['judul'] }}</h1>
                <p class="mt-2 text-slate-500 max-w-xl">{{ $t['sub'] }}</p>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 pb-6 grid lg:grid-cols-3 gap-6 items-start">

        <form method="POST" action="{{ route('claims.store', $item) }}" enctype="multipart/form-data"
            x-data="{ loading: false, photoCount: 0, agreed: false }" @submit="loading = true" class="lg:col-span-2 space-y-5">
            @csrf

            {{-- 1. BUKTI --}}
            <div class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 p-6 sm:p-7">
                <div class="flex items-center gap-3 mb-5">
                    <span
                        class="w-8 h-8 rounded-full {{ $t['num'] }} text-white grid place-items-center text-sm font-bold">{{ ++$no }}</span>
                    <div>
                        <h2 class="font-bold text-slate-900">
                            {{ $temuan ? 'Bukti kepemilikan' : 'Barang yang kamu temukan' }}</h2>
                        <p class="text-xs text-slate-500">Semakin spesifik, semakin cepat diverifikasi.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <x-field-textarea name="bukti_deskripsi" :label="$t['bukti']" :rows="5" :placeholder="$t['ph']"
                        :hint="$t['hint']" />

                    @unless ($temuan)
                        <x-field name="lokasi_barang_sekarang" label="Barang sekarang ada di mana?" icon="map-pin"
                            placeholder="Contoh: Saya bawa sendiri / dititip di pos satpam" />
                    @endunless
                </div>
            </div>

            {{-- 2. FOTO --}}
            <div class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 p-6 sm:p-7" style="animation-delay:.05s"
                x-data="{
                    files: [],
                    previews: [],
                    error: '',
                    drag: false,
                    max: 5,
                    maxSize: 5 * 1024 * 1024,
                    add(list) {
                        this.error = '';
                        for (const f of Array.from(list)) {
                            if (!['image/jpeg', 'image/png', 'image/webp'].includes(f.type)) { this.error = 'Format foto harus JPG, PNG, atau WEBP.'; continue; }
                            if (f.size > this.maxSize) { this.error = 'Ukuran tiap foto maksimal 5 MB.'; continue; }
                            if (this.files.length >= this.max) { this.error = 'Maksimal ' + this.max + ' foto.'; break; }
                            this.files.push(f);
                            this.previews.push(URL.createObjectURL(f));
                        }
                        this.sync();
                    },
                    remove(i) {
                        URL.revokeObjectURL(this.previews[i]);
                        this.files.splice(i, 1);
                        this.previews.splice(i, 1);
                        this.error = '';
                        this.sync();
                    },
                    sync() {
                        const dt = new DataTransfer();
                        this.files.forEach(f => dt.items.add(f));
                        this.$refs.input.files = dt.files;
                        this.photoCount = this.files.length;
                        if (!this.files.length) this.agreed = false;
                    }
                }">
                <div class="flex items-center gap-3 mb-5">
                    <span
                        class="w-8 h-8 rounded-full {{ $t['num'] }} text-white grid place-items-center text-sm font-bold">{{ ++$no }}</span>
                    <div class="flex-1">
                        <h2 class="font-bold text-slate-900">Foto pendukung</h2>
                        <p class="text-xs text-slate-500">{{ $t['fotoSub'] }}</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-400"><span x-text="files.length"></span>/5</span>
                </div>

                <input type="file" name="photos[]" x-ref="input" multiple accept="image/jpeg,image/png,image/webp"
                    class="hidden" @change="add($event.target.files)">

                <button type="button" @click="$refs.input.click()" @dragover.prevent="drag = true"
                    @dragleave.prevent="drag = false" @drop.prevent="drag = false; add($event.dataTransfer.files)"
                    x-show="files.length < max"
                    :class="drag ? '{{ $t['drop'] }} scale-[1.01]' : 'border-slate-300 bg-slate-50 hover:bg-slate-100'"
                    class="w-full rounded-2xl border-2 border-dashed px-4 py-9 text-center transition">
                    <span
                        class="mx-auto w-14 h-14 rounded-2xl bg-white ring-1 ring-slate-200 shadow-sm grid place-items-center text-slate-500">
                        <x-icon name="cloud-upload" class="w-7 h-7" />
                    </span>
                    <span class="block mt-3 text-sm font-bold text-slate-700">Klik untuk pilih foto, atau seret ke
                        sini</span>
                    <span class="block mt-1 text-xs text-slate-400">JPG, PNG, WEBP. Maksimal 5 foto, 5 MB per foto.</span>
                </button>

                <div x-show="previews.length" x-cloak class="mt-4 grid grid-cols-3 sm:grid-cols-5 gap-3">
                    <template x-for="(src, i) in previews" :key="src">
                        <div
                            class="group relative aspect-square rounded-2xl overflow-hidden ring-1 ring-slate-200 bg-slate-100">
                            <img :src="src" alt="Pratinjau foto" class="w-full h-full object-cover">
                            <button type="button" @click="remove(i)" aria-label="Hapus foto"
                                class="absolute top-1.5 right-1.5 w-7 h-7 rounded-full bg-white/95 text-rose-600 grid place-items-center shadow
                                       sm:opacity-0 group-hover:opacity-100 hover:bg-rose-600 hover:text-white transition">
                                <x-icon name="trash-2" class="w-4 h-4" />
                            </button>
                        </div>
                    </template>
                </div>

                <p x-show="error" x-cloak class="mt-3 flex items-center gap-1.5 text-xs font-medium text-rose-600">
                    <x-icon name="alert-circle" class="w-4 h-4 shrink-0" /> <span x-text="error"></span>
                </p>
                @if ($photoError)
                    <p class="mt-3 flex items-center gap-1.5 text-xs font-medium text-rose-600">
                        <x-icon name="alert-circle" class="w-4 h-4 shrink-0" /> {{ $photoError }}
                    </p>
                @endif

                <div
                    class="mt-4 flex items-start gap-2.5 rounded-xl bg-slate-50 ring-1 ring-slate-200 px-3.5 py-3 text-xs text-slate-500">
                    <x-icon name="lock" class="w-4 h-4 shrink-0 text-emerald-600 mt-0.5" />
                    <p>Foto klaim bersifat privat. Hanya kamu, pembuat postingan, dan admin yang bisa melihatnya. Tetap
                        tutupi data pribadi pada KTM, KTP, atau kartu ATM.</p>
                </div>

                <label x-show="photoCount > 0" x-cloak x-transition:enter="transition duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    class="mt-3 flex items-start gap-3 cursor-pointer rounded-xl p-4 ring-1 transition select-none"
                    :class="agreed ? 'bg-emerald-50 ring-emerald-300' : 'bg-amber-50 ring-amber-300'">
                    <input type="checkbox" name="privacy_ack" value="1" x-model="agreed"
                        class="mt-0.5 w-5 h-5 shrink-0 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-sm">
                        <span class="block font-bold" :class="agreed ? 'text-emerald-800' : 'text-amber-900'">Saya sudah
                            menutupi data pribadi pada foto</span>
                        <span class="block text-xs mt-0.5" :class="agreed ? 'text-emerald-700/80' : 'text-amber-800/80'">
                            Nama, nomor, dan foto wajah pada kartu identitas sudah ditutupi atau diburamkan.
                        </span>
                    </span>
                </label>
                @error('privacy_ack')
                    <p class="mt-2 flex items-center gap-1.5 text-xs font-medium text-rose-600">
                        <x-icon name="alert-circle" class="w-4 h-4 shrink-0" /> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- SUBMIT --}}
            <div class="animate-fade-up flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3"
                style="animation-delay:.1s">
                <a href="{{ route('items.show', $item) }}"
                    class="text-center text-sm font-semibold text-slate-500 hover:text-slate-900 transition py-3">Batal</a>

                <p x-show="photoCount > 0 && !agreed" x-cloak
                    class="text-xs text-amber-600 font-medium sm:ml-auto sm:mr-4 text-center">
                    Centang pernyataan privasi dulu untuk melanjutkan.
                </p>

                <button type="submit" :disabled="loading || (photoCount > 0 && !agreed)"
                    class="group inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r {{ $t['btn'] }} px-8 py-3.5 font-semibold text-white shadow-lg
                           hover:-translate-y-0.5 active:scale-[.98] disabled:opacity-50 disabled:grayscale disabled:pointer-events-none transition">
                    <svg x-show="loading" x-cloak class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                            class="opacity-25" />
                        <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                    </svg>
                    <x-icon name="send" x-show="!loading" class="w-4 h-4" />
                    <span
                        x-text="loading ? 'Mengirim...' : '{{ $temuan ? 'Kirim Klaim' : 'Kirim Laporan' }}'">{{ $temuan ? 'Kirim Klaim' : 'Kirim Laporan' }}</span>
                </button>
            </div>
        </form>

        {{-- SIDEBAR --}}
        <aside class="space-y-5 lg:sticky lg:top-24">
            <div class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 overflow-hidden"
                style="animation-delay:.1s">
                <div class="relative aspect-[16/10] bg-gradient-to-br from-slate-100 to-slate-200">
                    @if ($item->foto_utama)
                        <img src="{{ $item->foto_utama }}" alt="{{ $item->judul }}"
                            class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full grid place-items-center text-slate-300"><x-icon :name="$ikon"
                                class="w-14 h-14" /></div>
                    @endif
                    <span
                        class="absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-extrabold text-white {{ $temuan ? 'bg-emerald-600' : 'bg-rose-500' }}">
                        {{ $temuan ? 'DITEMUKAN' : 'DICARI' }}
                    </span>
                </div>
                <div class="p-5">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        {{ $item->category->nama }}</p>
                    <h3 class="mt-1 font-bold text-slate-900">{{ $item->judul }}</h3>
                    <p class="mt-2 flex items-center gap-2 text-sm text-slate-500"><x-icon name="map-pin"
                            class="w-4 h-4 text-slate-400" /> {{ $item->location->nama }}</p>
                    <p class="mt-1 flex items-center gap-2 text-sm text-slate-500"><x-icon name="calendar"
                            class="w-4 h-4 text-slate-400" /> {{ $item->tanggal_kejadian->translatedFormat('d M Y') }}</p>
                </div>
            </div>

            <div class="animate-fade-up rounded-3xl bg-slate-900 text-white p-6 relative overflow-hidden"
                style="animation-delay:.15s">
                <div class="absolute -top-12 -right-12 w-40 h-40 {{ $t['blob1'] }} rounded-full blur-3xl"></div>
                <div class="relative">
                    <span class="w-10 h-10 rounded-xl bg-white/10 grid place-items-center text-emerald-300"><x-icon
                            name="lightbulb" class="w-5 h-5" /></span>
                    <h3 class="mt-4 font-bold">Tips supaya cepat disetujui</h3>
                    <ul class="mt-3 space-y-2.5 text-sm text-slate-300">
                        <li class="flex gap-2.5"><x-icon name="check"
                                class="w-4 h-4 mt-0.5 text-emerald-400 shrink-0" /> Sebutkan ciri yang tidak ada di foto
                            postingan.</li>
                        <li class="flex gap-2.5"><x-icon name="check"
                                class="w-4 h-4 mt-0.5 text-emerald-400 shrink-0" /> Jujur dan spesifik. Tebakan asal akan
                            ditolak.</li>
                        <li class="flex gap-2.5"><x-icon name="check"
                                class="w-4 h-4 mt-0.5 text-emerald-400 shrink-0" /> Lampirkan foto pendukung kalau ada.
                        </li>
                    </ul>
                </div>
            </div>
        </aside>
    </section>

@endsection
