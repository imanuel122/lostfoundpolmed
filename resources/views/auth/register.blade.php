@extends('layouts.auth')

@section('title', 'Daftar - LostFound Polmed')
@section('panel_title')
    Bergabung dan <span class="text-gradient">saling bantu.</span>
@endsection
@section('panel_text',
    'Buat akun gratis untuk memposting barang hilang atau temuan, dan bantu teman sekampusmu
    mendapatkan barangnya kembali.')

@section('content')
    <div x-data="{
        loading: false,
        pw: '',
        get score() {
            let s = 0;
            if (this.pw.length >= 8) s++;
            if (/[a-z]/.test(this.pw) && /[A-Z]/.test(this.pw)) s++;
            if (/\d/.test(this.pw)) s++;
            if (/[^A-Za-z0-9]/.test(this.pw)) s++;
            return this.pw.length ? Math.max(s, 1) : 0;
        },
        get label() { return ['', 'Lemah', 'Cukup', 'Bagus', 'Kuat'][this.score]; },
        get color() { return ['bg-slate-200', 'bg-rose-500', 'bg-amber-500', 'bg-lime-500', 'bg-emerald-500'][this.score]; }
    }">

        <div class="flex items-center gap-4">
            <span
                class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-cyan-500 text-white grid place-items-center shadow-lg shadow-emerald-500/30 shrink-0">
                <x-icon name="user" class="w-6 h-6" />
            </span>
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Buat akun baru</h1>
                <p class="text-sm text-slate-500">Cukup satu menit, gratis.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" @submit="loading = true"
            class="mt-7 space-y-4 {{ $errors->any() ? 'animate-shake' : '' }}">
            @csrf

            <x-field name="name" label="Nama lengkap" icon="user" placeholder="Nama sesuai KTM" autocomplete="name" />

            <div class="grid sm:grid-cols-2 gap-4">
                <x-field name="nim" label="NIM" icon="id-card" placeholder="Nomor induk" :optional="true" />
                <x-field name="no_wa" label="No. WhatsApp" icon="phone" type="tel" placeholder="081234567890"
                    autocomplete="tel" />
            </div>

            <x-field name="email" label="Email" icon="mail" type="email" placeholder="nama@email.com"
                autocomplete="email" :hint="config('lostfound.allowed_email_domain')
                    ? 'Gunakan email kampus @' . config('lostfound.allowed_email_domain')
                    : null" />

            <div>
                <x-field name="password" label="Password" icon="lock" type="password" placeholder="Minimal 8 karakter"
                    autocomplete="new-password" x-model="pw" />

                <div x-show="pw.length" x-cloak x-transition.opacity class="mt-2.5">
                    <div class="flex gap-1.5">
                        <template x-for="n in 4" :key="n">
                            <span class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                :class="n <= score ? color : 'bg-slate-200'"></span>
                        </template>
                    </div>
                    <p class="mt-1.5 text-xs text-slate-500">Kekuatan: <span class="font-semibold" x-text="label"></span>
                    </p>
                </div>
            </div>

            <x-field name="password_confirmation" label="Ulangi password" icon="lock" type="password"
                placeholder="Ketik ulang password" autocomplete="new-password" />

            <button type="submit" :disabled="loading"
                class="group w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3.5 font-semibold text-white
                       shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/40 hover:-translate-y-0.5 active:scale-[.98]
                       disabled:opacity-70 disabled:pointer-events-none transition">
                <svg x-show="loading" x-cloak class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                        class="opacity-25" />
                    <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                </svg>
                <span x-text="loading ? 'Membuat akun...' : 'Daftar'">Daftar</span>
                <x-icon name="arrow-right" x-show="!loading" class="w-4 h-4 group-hover:translate-x-1 transition" />
            </button>

            <p class="text-xs text-center text-slate-400 leading-relaxed">
                Nomor WhatsApp tidak ditampilkan ke publik. Hanya terbuka untuk pembuat postingan atau pengklaim setelah
                klaim disetujui.
            </p>
        </form>

        <p class="mt-5 text-center text-sm text-slate-500">
            Sudah punya akun?
            <a href="{{ route('login') }}"
                class="font-bold text-emerald-600 hover:text-emerald-700 hover:underline">Masuk</a>
        </p>
    </div>
@endsection
