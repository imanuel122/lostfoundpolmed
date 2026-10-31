<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'LostFound Polmed')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    },
                    keyframes: {
                        blob: {
                            '0%,100%': {
                                transform: 'translate(0,0) scale(1)'
                            },
                            '33%': {
                                transform: 'translate(30px,-40px) scale(1.1)'
                            },
                            '66%': {
                                transform: 'translate(-25px,25px) scale(.95)'
                            }
                        },
                        fadeUp: {
                            '0%': {
                                opacity: 0,
                                transform: 'translateY(20px)'
                            },
                            '100%': {
                                opacity: 1,
                                transform: 'translateY(0)'
                            }
                        },
                        shake: {
                            '0%,100%': {
                                transform: 'translateX(0)'
                            },
                            '20%,60%': {
                                transform: 'translateX(-6px)'
                            },
                            '40%,80%': {
                                transform: 'translateX(6px)'
                            }
                        },
                        bob: {
                            '0%,100%': {
                                transform: 'translateY(0)'
                            },
                            '50%': {
                                transform: 'translateY(-4px)'
                            }
                        },
                        progress: {
                            '0%': {
                                width: '0%'
                            },
                            '100%': {
                                width: '100%'
                            }
                        },
                    },
                    animation: {
                        blob: 'blob 14s ease-in-out infinite',
                        'fade-up': 'fadeUp .7s ease-out both',
                        shake: 'shake .4s ease-in-out',
                        bob: 'bob 3.5s ease-in-out infinite',
                    },
                },
            },
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .text-gradient {
            background: linear-gradient(90deg, #34d399, #22d3ee, #818cf8);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased font-sans">

    <div class="min-h-screen grid lg:grid-cols-[1.05fr_1fr]">

        {{-- ============ PANEL KIRI (brand) ============ --}}
        <aside class="relative hidden lg:flex flex-col overflow-hidden bg-slate-900 text-white p-12 xl:p-14">
            {{-- dekorasi latar --}}
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-500/30 rounded-full blur-3xl animate-blob"></div>
            <div class="absolute top-1/3 -right-32 w-96 h-96 bg-cyan-500/20 rounded-full blur-3xl animate-blob"
                style="animation-delay:-5s"></div>
            <div class="absolute -bottom-32 left-1/4 w-96 h-96 bg-indigo-500/25 rounded-full blur-3xl animate-blob"
                style="animation-delay:-9s"></div>
            <div class="absolute inset-0 opacity-[0.06]"
                style="background-image:radial-gradient(#fff 1px,transparent 1px);background-size:24px 24px"></div>

            {{-- 1. Logo --}}
            <a href="{{ route('home') }}" class="relative z-10 flex items-center gap-2.5 font-extrabold text-xl w-fit">
                <span
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-cyan-500 grid place-items-center shadow-lg shadow-emerald-500/30">
                    <x-icon name="search" class="w-5 h-5" />
                </span>
                LostFound <span class="text-emerald-400">Polmed</span>
            </a>

            {{-- 2. Judul + keunggulan (di tengah, memakai sisa ruang) --}}
            <div class="relative z-10 flex-1 flex flex-col justify-center py-10">
                <h2 class="text-4xl xl:text-5xl font-extrabold leading-[1.1] tracking-tight animate-fade-up">
                    @yield('panel_title')
                </h2>
                <p class="mt-4 text-slate-300 max-w-md leading-relaxed animate-fade-up" style="animation-delay:.1s">
                    @yield('panel_text')
                </p>

                <ul class="mt-8 space-y-3 text-sm max-w-md">
                    @foreach ([['file-text', 'Posting dalam hitungan menit', 'Lengkap dengan foto dan lokasi'], ['shield-check', 'Klaim diverifikasi dengan bukti', 'Barang sampai ke pemilik asli'], ['package-check', 'Bukti serah terima tersimpan', 'Foto penyerahan tercatat aman']] as $i => $f)
                        <li class="flex items-center gap-3.5 rounded-2xl bg-white/5 ring-1 ring-white/10 px-4 py-3 animate-fade-up hover:bg-white/10 transition"
                            style="animation-delay: {{ 0.2 + $i * 0.1 }}s">
                            <span
                                class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-300 grid place-items-center shrink-0">
                                <x-icon :name="$f[0]" class="w-5 h-5" />
                            </span>
                            <span>
                                <span class="block font-semibold text-white">{{ $f[1] }}</span>
                                <span class="block text-xs text-slate-400">{{ $f[2] }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- 3. Kartu aktivitas bergantian (berada di alur layout, tidak menimpa teks) --}}
            <div class="relative z-10" x-data="{ i: 0, total: 3, init() { setInterval(() => this.i = (this.i + 1) % this.total, 3800) } }">
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-3 flex items-center gap-2">
                    <span class="relative flex w-2 h-2">
                        <span class="absolute inset-0 rounded-full bg-emerald-400 animate-ping opacity-75"></span>
                        <span class="relative w-2 h-2 rounded-full bg-emerald-400"></span>
                    </span>
                    Aktivitas terbaru
                </p>

                <div class="relative h-[84px] animate-bob">
                    @foreach ([['wallet', 'DITEMUKAN', 'Dompet Hitam', 'Kantin, 5 menit lalu', 'bg-emerald-500/20 text-emerald-300', 'text-emerald-300'], ['key-round', 'DICARI', 'Kunci Motor', 'Parkiran, 1 jam lalu', 'bg-rose-500/20 text-rose-300', 'text-rose-300'], ['circle-check', 'KEMBALI', 'Laptop Asus', 'Sudah diserahterimakan', 'bg-blue-500/20 text-blue-300', 'text-blue-300']] as $n => $c)
                        <div x-show="i === {{ $n }}" x-cloak x-transition:enter="transition duration-500"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:leave="transition duration-300"
                            x-transition:leave-end="opacity-0 -translate-y-4"
                            class="absolute inset-0 flex items-center gap-4 rounded-2xl bg-white/10 backdrop-blur-md ring-1 ring-white/15 px-5">
                            <span class="w-12 h-12 rounded-xl {{ $c[4] }} grid place-items-center shrink-0">
                                <x-icon :name="$c[0]" class="w-6 h-6" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-[11px] font-extrabold tracking-wider {{ $c[5] }}">
                                    {{ $c[1] }}</p>
                                <p class="font-bold truncate">{{ $c[2] }}</p>
                                <p class="text-xs text-slate-400 truncate">{{ $c[3] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- indikator titik --}}
                <div class="mt-4 flex items-center justify-between">
                    <div class="flex gap-1.5">
                        @foreach ([0, 1, 2] as $d)
                            <span class="h-1.5 rounded-full transition-all duration-300"
                                :class="i === {{ $d }} ? 'w-6 bg-emerald-400' : 'w-1.5 bg-white/25'"></span>
                        @endforeach
                    </div>
                    <p class="text-xs text-slate-500">© {{ date('Y') }} LostFound Polmed</p>
                </div>
            </div>
        </aside>

        {{-- ============ PANEL KANAN (form) ============ --}}
        <main class="relative flex flex-col overflow-hidden">
            <div class="absolute -top-24 -right-24 w-80 h-80 bg-emerald-200/50 rounded-full blur-3xl animate-blob">
            </div>
            <div class="absolute bottom-0 -left-24 w-80 h-80 bg-cyan-200/40 rounded-full blur-3xl animate-blob"
                style="animation-delay:-6s"></div>

            <div class="relative flex items-center justify-between px-6 pt-6 lg:px-12">
                <a href="{{ route('home') }}"
                    class="group inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-slate-900 transition">
                    <x-icon name="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition" />
                    Kembali ke beranda
                </a>

                <a href="{{ route('home') }}" class="lg:hidden flex items-center gap-2 font-extrabold">
                    <span
                        class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-cyan-500 grid place-items-center text-white">
                        <x-icon name="search" class="w-4 h-4" />
                    </span>
                    <span class="text-sm">LostFound <span class="text-emerald-600">Polmed</span></span>
                </a>
            </div>

            <div class="relative flex-1 flex items-center justify-center px-5 py-8 lg:px-12">
                <div class="w-full max-w-[460px] animate-fade-up">

                    {{-- Tab Masuk / Daftar --}}
                    <div class="grid grid-cols-2 gap-1 p-1 mb-4 rounded-2xl bg-slate-200/60 text-sm font-semibold">
                        <a href="{{ route('login') }}"
                            class="flex items-center justify-center gap-2 py-2.5 rounded-xl transition
                              {{ request()->routeIs('login') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                            <x-icon name="lock" class="w-4 h-4" /> Masuk
                        </a>
                        <a href="{{ route('register') }}"
                            class="flex items-center justify-center gap-2 py-2.5 rounded-xl transition
                              {{ request()->routeIs('register') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">
                            <x-icon name="user" class="w-4 h-4" /> Daftar
                        </a>
                    </div>

                    @if (session('success'))
                        <div
                            class="mb-4 flex items-start gap-3 rounded-2xl bg-emerald-50 ring-1 ring-emerald-200 p-4 text-sm text-emerald-900">
                            <x-icon name="circle-check" class="w-5 h-5 shrink-0 text-emerald-600 mt-0.5" />
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    {{-- Kartu form --}}
                    <div
                        class="bg-white rounded-3xl ring-1 ring-slate-200/80 shadow-2xl shadow-slate-900/10 p-6 sm:p-8">
                        @yield('content')
                    </div>

                    <p class="mt-5 text-center text-xs text-slate-400 flex items-center justify-center gap-1.5">
                        <x-icon name="shield-check" class="w-4 h-4" />
                        Datamu aman dan hanya dipakai untuk keperluan serah terima barang.
                    </p>
                </div>
            </div>
        </main>
    </div>

</body>

</html>
