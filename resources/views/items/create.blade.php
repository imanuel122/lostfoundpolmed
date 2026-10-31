@extends('layouts.app')

@php
    $temuan = $jenis === 'temuan';

    $t = $temuan
        ? [
            'judul' => 'Lapor Barang Temuan',
            'sub' => 'Bantu pemiliknya menemukan barang ini kembali.',
            'hero' => 'from-emerald-50',
            'blob1' => 'bg-emerald-300/40',
            'blob2' => 'bg-cyan-300/40',
            'num' => 'bg-emerald-600',
            'checked' =>
                'peer-checked:bg-emerald-50 peer-checked:ring-2 peer-checked:ring-emerald-500 peer-checked:text-emerald-700',
            'btn' => 'from-emerald-600 to-teal-600 shadow-emerald-600/30',
            'drop' => 'border-emerald-300 bg-emerald-50/60',
            'lokasi' => 'Lokasi ditemukan',
            'tgl' => 'Tanggal ditemukan',
            'ph' => 'Contoh: Dompet kulit warna hitam',
        ]
        : [
            'judul' => 'Lapor Kehilangan Barang',
            'sub' => 'Ceritakan barangmu yang hilang, siapa tahu ada yang menemukannya.',
            'hero' => 'from-rose-50',
            'blob1' => 'bg-rose-300/40',
            'blob2' => 'bg-orange-200/50',
            'num' => 'bg-rose-500',
            'checked' =>
                'peer-checked:bg-rose-50 peer-checked:ring-2 peer-checked:ring-rose-500 peer-checked:text-rose-700',
            'btn' => 'from-rose-500 to-orange-500 shadow-rose-500/30',
            'drop' => 'border-rose-300 bg-rose-50/60',
            'lokasi' => 'Perkiraan lokasi hilang',
            'tgl' => 'Tanggal hilang',
            'ph' => 'Contoh: Kunci motor Honda gantungan biru',
        ];

    $no = 0;
    $photoError = $errors->first('photos') ?: $errors->first('photos.*');
@endphp

@section('title', $t['judul'] . ' - LostFound Polmed')

@section('content')

    {{-- ================= HEADER ================= --}}
    <section class="relative overflow-hidden pt-28 pb-8 bg-gradient-to-b {{ $t['hero'] }} via-white to-slate-50">
        <div class="absolute -top-24 -left-24 w-96 h-96 {{ $t['blob1'] }} rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-10 -right-24 w-80 h-80 {{ $t['blob2'] }} rounded-full blur-3xl animate-blob"
            style="animation-delay:-5s"></div>

        <div class="relative max-w-6xl mx-auto px-4">
            <a href="{{ route('home') }}"
                class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-slate-900 transition">
                <x-icon name="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition" />
                Kembali ke beranda
            </a>

            <div class="mt-5 animate-fade-up">
                {{-- Pindah jenis --}}
                <div class="inline-flex p-1 rounded-full bg-white ring-1 ring-slate-200 shadow-sm text-sm font-semibold">
                    <a href="{{ route('items.create', 'hilang') }}"
                        class="px-4 py-2 rounded-full transition {{ !$temuan ? 'bg-rose-500 text-white shadow' : 'text-slate-500 hover:text-rose-500' }}">
                        Kehilangan Barang
                    </a>
                    <a href="{{ route('items.create', 'temuan') }}"
                        class="px-4 py-2 rounded-full transition {{ $temuan ? 'bg-emerald-600 text-white shadow' : 'text-slate-500 hover:text-emerald-600' }}">
                        Menemukan Barang
                    </a>
                </div>

                <h1 class="mt-5 text-3xl md:text-4xl font-extrabold tracking-tight text-slate-900">{{ $t['judul'] }}</h1>
                <p class="mt-2 text-slate-500 max-w-xl">{{ $t['sub'] }}</p>
            </div>
        </div>
    </section>

    {{-- ================= FORM ================= --}}
    <section class="max-w-6xl mx-auto px-4 pb-6 grid lg:grid-cols-3 gap-6 items-start">

        <form method="POST" action="{{ route('items.store', $jenis) }}" enctype="multipart/form-data"
            x-data="{ loading: false, photoCount: 0, agreed: false }" @submit="loading = true" class="lg:col-span-2 space-y-5">
            @csrf

            {{-- 1. INFO BARANG --}}
            <div class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 p-6 sm:p-7">
                <div class="flex items-center gap-3 mb-5">
                    <span
                        class="w-8 h-8 rounded-full {{ $t['num'] }} text-white grid place-items-center text-sm font-bold">{{ ++$no }}</span>
                    <div>
                        <h2 class="font-bold text-slate-900">Info barang</h2>
                        <p class="text-xs text-slate-500">Nama dan jenis barangnya apa?</p>
                    </div>
                </div>

                <x-field name="judul" label="Nama barang" icon="tag" :placeholder="$t['ph']" />

                <div class="mt-5">
                    <p class="text-[13px] font-semibold text-slate-700">Kategori</p>
                    <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        @foreach ($categories as $c)
                            <label class="cursor-pointer">
                                <input type="radio" name="category_id" value="{{ $c->id }}" class="peer sr-only"
                                    @checked(old('category_id') == $c->id)>
                                <span
                                    class="flex items-center gap-2.5 rounded-xl bg-slate-50 ring-1 ring-slate-200 px-3 py-2.5 text-sm font-medium text-slate-600
                                         transition hover:ring-slate-300 hover:-translate-y-0.5 peer-focus-visible:ring-2 peer-focus-visible:ring-slate-400 {{ $t['checked'] }}">
                                    <x-icon :name="$c->ikon ?? 'package'" class="w-[18px] h-[18px] shrink-0" />
                                    <span class="truncate">{{ $c->nama }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('category_id')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-rose-600">
                            <x-icon name="alert-circle" class="w-4 h-4 shrink-0" /> {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- 2. LOKASI & WAKTU --}}
            <div class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 p-6 sm:p-7" style="animation-delay:.05s">
                <div class="flex items-center gap-3 mb-5">
                    <span
                        class="w-8 h-8 rounded-full {{ $t['num'] }} text-white grid place-items-center text-sm font-bold">{{ ++$no }}</span>
                    <div>
                        <h2 class="font-bold text-slate-900">Lokasi &amp; waktu</h2>
                        <p class="text-xs text-slate-500">Di mana dan kapan kejadiannya?</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <x-field-select name="location_id" :label="$t['lokasi']" icon="map-pin" :options="$locations->pluck('nama', 'id')"
                            placeholder="Pilih lokasi" />
                        <x-field name="tanggal_kejadian" :label="$t['tgl']" icon="calendar" type="date"
                            max="{{ now()->toDateString() }}" />
                    </div>

                    <x-field name="detail_lokasi" label="Detail lokasi" icon="map-pin" :optional="true"
                        placeholder="Contoh: Dekat rak buku lantai 2" />
                </div>
            </div>

            {{-- 3. CIRI-CIRI --}}
            <div class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 p-6 sm:p-7" style="animation-delay:.1s">
                <div class="flex items-center gap-3 mb-5">
                    <span
                        class="w-8 h-8 rounded-full {{ $t['num'] }} text-white grid place-items-center text-sm font-bold">{{ ++$no }}</span>
                    <div>
                        <h2 class="font-bold text-slate-900">Ciri-ciri barang</h2>
                        <p class="text-xs text-slate-500">Bagian pertama tampil di publik, bagian kedua tidak.</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <x-field-textarea name="deskripsi" label="Deskripsi umum" :optional="true"
                        placeholder="Warna, merek, ukuran, kondisi... (ciri yang aman dilihat orang lain)" />

                    <div class="rounded-2xl bg-amber-50 ring-1 ring-amber-200 p-4">
                        <div class="flex items-start gap-3">
                            <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 grid place-items-center shrink-0">
                                <x-icon name="eye-off" class="w-[18px] h-[18px]" />
                            </span>
                            <div class="text-sm">
                                <p class="font-bold text-amber-900">Ciri rahasia (tidak ditampilkan di publik)</p>
                                <p class="text-amber-800/80 mt-0.5">
                                    @if ($temuan)
                                        Tulis ciri yang hanya diketahui pemilik asli, misalnya isi dompet, goresan khusus,
                                        atau wallpaper HP. Dipakai untuk memverifikasi klaim.
                                    @else
                                        Tulis ciri khusus barangmu (opsional). Berguna untuk mencocokkan laporan dari orang
                                        yang merasa menemukannya.
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <x-field-textarea name="ciri_rahasia" label="" :rows="3" :optional="!$temuan"
                                placeholder="Contoh: ada stiker bunga di bagian dalam, isi 2 kartu ATM BRI" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. PENYIMPANAN (khusus temuan) --}}
            @if ($temuan)
                <div class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 p-6 sm:p-7"
                    style="animation-delay:.15s">
                    <div class="flex items-center gap-3 mb-5">
                        <span
                            class="w-8 h-8 rounded-full {{ $t['num'] }} text-white grid place-items-center text-sm font-bold">{{ ++$no }}</span>
                        <div>
                            <h2 class="font-bold text-slate-900">Barang disimpan di mana?</h2>
                            <p class="text-xs text-slate-500">Supaya pemilik tahu ke mana harus mengambilnya.</p>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-3 gap-2.5">
                        @foreach ([['Saya bawa sendiri', 'user', 'Janjian serah terima'], ['Pos satpam', 'shield-check', 'Dititipkan ke satpam'], ['Bagian kemahasiswaan', 'archive', 'Dititipkan ke kampus']] as $p)
                            <label class="cursor-pointer">
                                <input type="radio" name="lokasi_penyimpanan" value="{{ $p[0] }}"
                                    class="peer sr-only" @checked(old('lokasi_penyimpanan') === $p[0])>
                                <span
                                    class="block h-full rounded-xl bg-slate-50 ring-1 ring-slate-200 p-3.5 transition hover:ring-slate-300 hover:-translate-y-0.5 {{ $t['checked'] }}">
                                    <x-icon :name="$p[1]" class="w-5 h-5" />
                                    <span class="block mt-2 text-sm font-bold">{{ $p[0] }}</span>
                                    <span class="block text-xs text-slate-400">{{ $p[2] }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('lokasi_penyimpanan')
                        <p class="mt-2 flex items-center gap-1.5 text-xs font-medium text-rose-600">
                            <x-icon name="alert-circle" class="w-4 h-4 shrink-0" /> {{ $message }}
                        </p>
                    @enderror
                </div>
            @endif

            {{-- 5. FOTO --}}
            <div class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 p-6 sm:p-7" style="animation-delay:.2s"
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
                
                        {{-- // Beri tahu form: ada berapa foto. Kalau kosong, centang direset. --}}
                        this.photoCount = this.files.length;
                        if (!this.files.length) this.agreed = false;
                    }
                }">
                <div class="flex items-center gap-3 mb-5">
                    <span
                        class="w-8 h-8 rounded-full {{ $t['num'] }} text-white grid place-items-center text-sm font-bold">{{ ++$no }}</span>
                    <div class="flex-1">
                        <h2 class="font-bold text-slate-900">Foto barang</h2>
                        <p class="text-xs text-slate-500">
                            {{ $temuan ? 'Disarankan, supaya pemilik cepat mengenali barangnya.' : 'Opsional. Foto lama barangmu sangat membantu.' }}
                        </p>
                    </div>
                    <span class="text-xs font-semibold text-slate-400"><span x-text="files.length"></span>/5</span>
                </div>

                <input type="file" name="photos[]" x-ref="input" multiple accept="image/jpeg,image/png,image/webp"
                    class="hidden" @change="add($event.target.files)">

                {{-- Area drop --}}
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

                {{-- Pratinjau --}}
                <div x-show="previews.length" x-cloak class="mt-4 grid grid-cols-3 sm:grid-cols-5 gap-3">
                    <template x-for="(src, i) in previews" :key="src">
                        <div
                            class="group relative aspect-square rounded-2xl overflow-hidden ring-1 ring-slate-200 bg-slate-100">
                            <img :src="src" alt="Pratinjau foto" class="w-full h-full object-cover">
                            <span x-show="i === 0"
                                class="absolute bottom-1.5 left-1.5 px-2 py-0.5 rounded-full bg-slate-900/80 text-white text-[10px] font-bold">Utama</span>
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

                {{-- Peringatan umum (selalu tampil) --}}
                <div
                    class="mt-4 flex items-start gap-2.5 rounded-xl bg-slate-50 ring-1 ring-slate-200 px-3.5 py-3 text-xs text-slate-500">
                    <x-icon name="triangle-alert" class="w-4 h-4 shrink-0 text-amber-500 mt-0.5" />
                    <p>Foto tampil di beranda dan bisa dilihat siapa saja. Kalau ada KTM, KTP, atau kartu ATM, tutupi nama,
                        nomor, dan foto wajah sebelum mengunggah.</p>
                </div>

                {{-- Kotak centang (muncul hanya kalau ada foto) --}}
                <label x-show="photoCount > 0" x-cloak x-transition:enter="transition duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    class="mt-3 flex items-start gap-3 cursor-pointer rounded-xl p-4 ring-1 transition select-none"
                    :class="agreed ? 'bg-emerald-50 ring-emerald-300' : 'bg-amber-50 ring-amber-300'">
                    <input type="checkbox" name="privacy_ack" value="1" x-model="agreed"
                        class="mt-0.5 w-5 h-5 shrink-0 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-sm">
                        <span class="block font-bold" :class="agreed ? 'text-emerald-800' : 'text-amber-900'">
                            Saya sudah menutupi data pribadi pada foto
                        </span>
                        <span class="block text-xs mt-0.5" :class="agreed ? 'text-emerald-700/80' : 'text-amber-800/80'">
                            Nama, nomor, dan foto wajah pada KTM, KTP, atau kartu ATM sudah ditutupi atau diburamkan.
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
                style="animation-delay:.25s">
                <a href="{{ route('home') }}"
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
                    <span x-text="loading ? 'Mengirim...' : 'Posting Sekarang'">Posting Sekarang</span>
                </button>
            </div>
        </form>

        {{-- ================= SIDEBAR ================= --}}
        <aside class="space-y-5 lg:sticky lg:top-24">
            <div class="animate-fade-up rounded-3xl bg-slate-900 text-white p-6 relative overflow-hidden"
                style="animation-delay:.1s">
                <div class="absolute -top-12 -right-12 w-40 h-40 {{ $t['blob1'] }} rounded-full blur-3xl"></div>
                <div class="relative">
                    <span class="w-10 h-10 rounded-xl bg-white/10 grid place-items-center text-emerald-300">
                        <x-icon name="lightbulb" class="w-5 h-5" />
                    </span>
                    <h3 class="mt-4 font-bold">Tips posting yang bagus</h3>
                    <ul class="mt-3 space-y-2.5 text-sm text-slate-300">
                        <li class="flex gap-2.5"><x-icon name="check"
                                class="w-4 h-4 mt-0.5 text-emerald-400 shrink-0" /> Gunakan nama barang yang jelas dan
                            spesifik.</li>
                        <li class="flex gap-2.5"><x-icon name="check"
                                class="w-4 h-4 mt-0.5 text-emerald-400 shrink-0" /> Sertakan foto yang terang dan tidak
                            buram.</li>
                        <li class="flex gap-2.5"><x-icon name="check"
                                class="w-4 h-4 mt-0.5 text-emerald-400 shrink-0" /> Jangan tulis ciri rahasia di deskripsi
                            umum.</li>
                        <li class="flex gap-2.5"><x-icon name="check"
                                class="w-4 h-4 mt-0.5 text-emerald-400 shrink-0" /> Isi lokasi serinci mungkin.</li>
                    </ul>
                </div>
            </div>

            <div class="animate-fade-up rounded-3xl bg-white ring-1 ring-slate-200 p-6" style="animation-delay:.15s">
                <span class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 grid place-items-center">
                    <x-icon name="info" class="w-5 h-5" />
                </span>
                <h3 class="mt-4 font-bold text-slate-900">Setelah diposting</h3>
                <ol class="mt-3 space-y-3 text-sm text-slate-500">
                    <li class="flex gap-3"><span
                            class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 text-xs font-bold grid place-items-center shrink-0">1</span>
                        Postingan tampil di beranda.</li>
                    <li class="flex gap-3"><span
                            class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 text-xs font-bold grid place-items-center shrink-0">2</span>
                        {{ $temuan ? 'Pemilik mengajukan klaim beserta bukti.' : 'Penemu melapor beserta foto barangnya.' }}
                    </li>
                    <li class="flex gap-3"><span
                            class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 text-xs font-bold grid place-items-center shrink-0">3</span>
                        Kamu memeriksa dan menyetujui.</li>
                    <li class="flex gap-3"><span
                            class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 text-xs font-bold grid place-items-center shrink-0">4</span>
                        Serah terima, lalu unggah foto bukti.</li>
                </ol>
                <p class="mt-4 text-xs text-slate-400">Postingan otomatis kedaluwarsa setelah 60 hari tanpa klaim.</p>
            </div>
        </aside>
    </section>

@endsection
