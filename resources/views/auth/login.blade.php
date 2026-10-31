@extends('layouts.auth')

@section('title', 'Masuk - LostFound Polmed')
@section('panel_title')
    Selamat datang <span class="text-gradient">kembali.</span>
@endsection
@section('panel_text', 'Masuk untuk memposting barang, mengajukan klaim, dan memantau barangmu yang sedang dicari.')

@section('content')
    <div class="flex items-center gap-4">
        <span
            class="w-12 h-12 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 text-white grid place-items-center shadow-lg shadow-slate-900/20 shrink-0">
            <x-icon name="lock" class="w-6 h-6" />
        </span>
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Masuk ke akunmu</h1>
            <p class="text-sm text-slate-500">Pakai email atau NIM yang terdaftar.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true"
        class="mt-7 space-y-5 {{ $errors->any() ? 'animate-shake' : '' }}">
        @csrf

        <x-field name="login" label="Email atau NIM" icon="mail" placeholder="nama@email.com atau NIM"
            autocomplete="username" autofocus />

        <x-field name="password" label="Password" icon="lock" type="password" placeholder="Masukkan password"
            autocomplete="current-password" />

        <div class="flex items-center justify-between gap-3">
            <label class="flex items-center gap-2.5 text-sm text-slate-600 cursor-pointer select-none w-fit">
                <input type="checkbox" name="remember"
                    class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                Ingat saya
            </label>
            <a href="{{ route('password.request') }}"
                class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 hover:underline">Lupa password?</a>
        </div>

        <button type="submit" :disabled="loading"
            class="group w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3.5 font-semibold text-white
                   shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/40 hover:-translate-y-0.5 active:scale-[.98]
                   disabled:opacity-70 disabled:pointer-events-none transition">
            <svg x-show="loading" x-cloak class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25" />
                <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
            </svg>
            <span x-text="loading ? 'Memproses...' : 'Masuk'">Masuk</span>
            <x-icon name="arrow-right" x-show="!loading" class="w-4 h-4 group-hover:translate-x-1 transition" />
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-bold text-emerald-600 hover:text-emerald-700 hover:underline">Daftar
            sekarang</a>
    </p>
@endsection
