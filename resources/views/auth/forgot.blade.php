@extends('layouts.auth')

@section('title', 'Lupa Password - LostFound Polmed')
@section('panel_title')
    Lupa password? <span class="text-gradient">Tenang saja.</span>
@endsection
@section('panel_text', 'Masukkan email akunmu. Kami kirim tautan untuk membuat password baru.')

@section('content')
    <div class="flex items-center gap-4">
        <span
            class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 text-white grid place-items-center shadow-lg shadow-amber-500/30 shrink-0">
            <x-icon name="mail" class="w-6 h-6" />
        </span>
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Atur ulang password</h1>
            <p class="text-sm text-slate-500">Tautan dikirim ke emailmu.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('password.email') }}" x-data="{ loading: false }" @submit="loading = true"
        class="mt-7 space-y-5">
        @csrf
        <x-field name="email" label="Email" icon="mail" type="email" placeholder="nama@email.com"
            autocomplete="email" autofocus />

        <button type="submit" :disabled="loading"
            class="group w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3.5 font-semibold text-white shadow-lg shadow-emerald-600/30 hover:-translate-y-0.5 active:scale-[.98] disabled:opacity-70 disabled:pointer-events-none transition">
            <x-icon name="send" class="w-4 h-4" />
            <span x-text="loading ? 'Mengirim...' : 'Kirim tautan reset'">Kirim tautan reset</span>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Ingat passwordnya?
        <a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:text-emerald-700 hover:underline">Masuk</a>
    </p>
@endsection
