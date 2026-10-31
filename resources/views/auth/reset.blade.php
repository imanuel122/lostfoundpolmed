@extends('layouts.auth')

@section('title', 'Password Baru - LostFound Polmed')
@section('panel_title')
    Buat password <span class="text-gradient">yang baru.</span>
@endsection
@section('panel_text', 'Pilih password yang kuat dan belum pernah kamu pakai di tempat lain.')

@section('content')
    <div class="flex items-center gap-4">
        <span
            class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-cyan-500 text-white grid place-items-center shadow-lg shadow-emerald-500/30 shrink-0">
            <x-icon name="lock" class="w-6 h-6" />
        </span>
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Password baru</h1>
            <p class="text-sm text-slate-500">Minimal 8 karakter, huruf dan angka.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('password.update') }}" x-data="{ loading: false }" @submit="loading = true"
        class="mt-7 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-field name="email" label="Email" icon="mail" type="email" :value="$email" autocomplete="email" />
        <x-field name="password" label="Password baru" icon="lock" type="password" placeholder="Minimal 8 karakter"
            autocomplete="new-password" />
        <x-field name="password_confirmation" label="Ulangi password baru" icon="lock" type="password"
            placeholder="Ketik ulang password" autocomplete="new-password" />

        <button type="submit" :disabled="loading"
            class="group w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3.5 font-semibold text-white shadow-lg shadow-emerald-600/30 hover:-translate-y-0.5 active:scale-[.98] disabled:opacity-70 disabled:pointer-events-none transition">
            <span x-text="loading ? 'Menyimpan...' : 'Simpan password baru'">Simpan password baru</span>
        </button>
    </form>
@endsection
