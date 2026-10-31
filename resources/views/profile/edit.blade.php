@extends('layouts.app')

@section('title', 'Profil - LostFound Polmed')

@section('content')

    <section class="max-w-6xl mx-auto px-4 pt-28">
        <div class="relative overflow-hidden rounded-3xl bg-slate-900 p-7 sm:p-9 text-white animate-fade-up">
            <div class="absolute -top-16 -right-10 w-72 h-72 bg-emerald-500/30 rounded-full blur-3xl animate-blob"></div>
            <div class="absolute -bottom-20 left-1/3 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl animate-blob"
                style="animation-delay:-6s"></div>
            <div class="relative flex items-center gap-5">
                <span
                    class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-cyan-500 grid place-items-center text-2xl font-extrabold shadow-lg shadow-emerald-500/30 shrink-0">
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="text-emerald-300 text-sm font-semibold">Profil</p>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight truncate">{{ $user->name }}</h1>
                    <p class="text-slate-300 text-sm truncate">{{ $user->email }} ·
                        {{ $user->isAdmin() ? 'Admin' : 'Mahasiswa' }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 pt-6 pb-6 grid lg:grid-cols-3 gap-6 items-start">

        <div class="lg:col-span-2 space-y-6">

            {{-- DATA DIRI --}}
            <form method="POST" action="{{ route('profile.update') }}" x-data="{ loading: false }" @submit="loading = true"
                class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 p-6 sm:p-7 space-y-5">
                @csrf
                @method('PATCH')

                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 grid place-items-center"><x-icon
                            name="user" class="w-5 h-5" /></span>
                    <div>
                        <h2 class="font-bold text-slate-900">Data diri</h2>
                        <p class="text-xs text-slate-500">Nomor WhatsApp hanya terbuka untuk penemu atau pemilik setelah
                            klaim disetujui.</p>
                    </div>
                </div>

                <x-field name="name" label="Nama lengkap" icon="user" :value="$user->name" autocomplete="name" />

                <div class="grid sm:grid-cols-2 gap-5">
                    <x-field name="nim" label="NIM" icon="id-card" :value="$user->nim" :optional="true" />
                    <x-field name="no_wa" label="No. WhatsApp" icon="phone" type="tel" :value="$user->no_wa"
                        autocomplete="tel" />
                </div>

                <x-field name="email" label="Email" icon="mail" type="email" :value="$user->email"
                    autocomplete="email" />

                <div class="flex justify-end">
                    <button type="submit" :disabled="loading"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-7 py-3 font-semibold text-white shadow-lg shadow-emerald-600/30 hover:-translate-y-0.5 active:scale-[.98] disabled:opacity-60 disabled:pointer-events-none transition">
                        <x-icon name="check" class="w-4 h-4" />
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan perubahan'">Simpan perubahan</span>
                    </button>
                </div>
            </form>

            {{-- GANTI PASSWORD --}}
            <form method="POST" action="{{ route('profile.password') }}" x-data="{ loading: false }" @submit="loading = true"
                class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 p-6 sm:p-7 space-y-5"
                style="animation-delay:.05s">
                @csrf
                @method('PUT')

                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 grid place-items-center"><x-icon
                            name="lock" class="w-5 h-5" /></span>
                    <div>
                        <h2 class="font-bold text-slate-900">Ganti password</h2>
                        <p class="text-xs text-slate-500">Minimal 8 karakter, mengandung huruf dan angka.</p>
                    </div>
                </div>

                <x-field name="current_password" label="Password sekarang" icon="lock" type="password"
                    autocomplete="current-password" />

                <div class="grid sm:grid-cols-2 gap-5">
                    <x-field name="password" label="Password baru" icon="lock" type="password"
                        autocomplete="new-password" />
                    <x-field name="password_confirmation" label="Ulangi password baru" icon="lock" type="password"
                        autocomplete="new-password" />
                </div>

                <div class="flex justify-end">
                    <button type="submit" :disabled="loading"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-7 py-3 font-semibold text-white hover:bg-slate-700 hover:-translate-y-0.5 active:scale-[.98] disabled:opacity-60 disabled:pointer-events-none transition">
                        <span x-text="loading ? 'Menyimpan...' : 'Ubah password'">Ubah password</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-5 lg:sticky lg:top-24">
            <div class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 p-6" style="animation-delay:.1s">
                <h3 class="font-bold text-slate-900">Aktivitasmu</h3>
                <div class="mt-4 grid grid-cols-3 gap-3 text-center">
                    @foreach ([['Postingan', $stat['postingan'], 'text-emerald-600'], ['Klaim', $stat['klaim'], 'text-amber-600'], ['Kembali', $stat['kembali'], 'text-blue-600']] as $s)
                        <div class="rounded-2xl bg-slate-50 py-4">
                            <p class="text-2xl font-extrabold {{ $s[2] }}">{{ $s[1] }}</p>
                            <p class="text-xs text-slate-500">{{ $s[0] }}</p>
                        </div>
                    @endforeach
                </div>
                <p class="mt-4 text-xs text-slate-400">Bergabung {{ $user->created_at->translatedFormat('d F Y') }}.</p>
                <a href="{{ route('dashboard') }}"
                    class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-emerald-600 hover:underline">
                    <x-icon name="layout-dashboard" class="w-4 h-4" /> Buka dashboard
                </a>
            </div>

            <div class="animate-fade-up rounded-3xl bg-slate-900 text-white p-6 relative overflow-hidden"
                style="animation-delay:.15s">
                <div class="absolute -top-12 -right-12 w-40 h-40 bg-emerald-500/30 rounded-full blur-3xl"></div>
                <div class="relative">
                    <span class="w-10 h-10 rounded-xl bg-white/10 grid place-items-center text-emerald-300"><x-icon
                            name="shield-check" class="w-5 h-5" /></span>
                    <h3 class="mt-4 font-bold">Jaga akunmu</h3>
                    <ul class="mt-3 space-y-2.5 text-sm text-slate-300">
                        <li class="flex gap-2.5"><x-icon name="check"
                                class="w-4 h-4 mt-0.5 text-emerald-400 shrink-0" /> Pastikan nomor WhatsApp aktif, supaya
                            serah terima lancar.</li>
                        <li class="flex gap-2.5"><x-icon name="check"
                                class="w-4 h-4 mt-0.5 text-emerald-400 shrink-0" /> Jangan bagikan passwordmu ke siapa pun.
                        </li>
                    </ul>
                </div>
            </div>
        </aside>
    </section>

@endsection
