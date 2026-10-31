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
                        float: {
                            '0%,100%': {
                                transform: 'translateY(0) rotate(var(--r,0deg))'
                            },
                            '50%': {
                                transform: 'translateY(-14px) rotate(var(--r,0deg))'
                            }
                        },
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
                        pulseRing: {
                            '0%': {
                                transform: 'scale(.8)',
                                opacity: .8
                            },
                            '100%': {
                                transform: 'scale(2.2)',
                                opacity: 0
                            }
                        },
                    },
                    animation: {
                        float: 'float 6s ease-in-out infinite',
                        'float-slow': 'float 8s ease-in-out infinite',
                        blob: 'blob 14s ease-in-out infinite',
                        'fade-up': 'fadeUp .8s ease-out both',
                        'pulse-ring': 'pulseRing 2s ease-out infinite',
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

        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .7s ease, transform .7s ease;
        }

        .reveal.show {
            opacity: 1;
            transform: none;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .text-gradient {
            background: linear-gradient(90deg, #10b981, #06b6d4, #6366f1);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased font-sans">

    {{-- NAVBAR --}}
    <header x-data="{ open: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 10"
        :class="(scrolled || open) ? 'bg-white/80 backdrop-blur-lg shadow-sm border-b border-slate-200/60' : 'bg-transparent'"
        @keydown.escape.window="open = false" class="fixed top-0 inset-x-0 z-50 transition-all duration-300">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-extrabold text-lg">
                <span
                    class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-cyan-500 grid place-items-center text-white shadow-lg shadow-emerald-500/30">
                    <x-icon name="search" class="w-5 h-5" />
                </span>
                <span>LostFound <span class="text-emerald-600">Polmed</span></span>
            </a>

            <nav class="hidden md:flex items-center gap-1 text-sm font-medium">
                <a href="{{ route('home') }}#daftar" class="px-4 py-2 rounded-full hover:bg-slate-900/5 transition">Cari
                    Barang</a>
                <a href="{{ route('home') }}#cara-kerja"
                    class="px-4 py-2 rounded-full hover:bg-slate-900/5 transition">Cara Kerja</a>

                @guest
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-full hover:bg-slate-900/5 transition">Masuk</a>
                    <a href="{{ route('register') }}"
                        class="ml-1 px-5 py-2 rounded-full bg-slate-900 text-white hover:bg-slate-700 hover:scale-105 active:scale-95 transition">Daftar</a>
                @endguest

                @auth
                    {{-- LONCENG NOTIFIKASI --}}
                    <div x-data="{ n: false }" @click.outside="n = false" class="relative ml-1">
                        <button @click="n = !n" aria-label="Notifikasi"
                            class="relative w-10 h-10 grid place-items-center rounded-full hover:bg-slate-900/5 transition">
                            <x-icon name="bell" class="w-5 h-5" />
                            @if ($notifCount > 0)
                                <span
                                    class="absolute top-0.5 right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-bold grid place-items-center ring-2 ring-white">
                                    {{ $notifCount > 9 ? '9+' : $notifCount }}
                                </span>
                            @endif
                        </button>

                        <div x-show="n" x-cloak x-transition.origin.top.right
                            class="absolute right-0 mt-2 w-[22rem] bg-white rounded-2xl shadow-xl shadow-slate-900/10 ring-1 ring-slate-200 overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                                <p class="font-bold text-sm">Notifikasi</p>
                                @if ($notifCount > 0)
                                    <form method="POST" action="{{ route('notifications.readAll') }}">
                                        @csrf
                                        <button class="text-xs font-semibold text-emerald-600 hover:underline">Tandai semua
                                            dibaca</button>
                                    </form>
                                @endif
                            </div>
                            <div class="max-h-96 overflow-y-auto p-2 space-y-1">
                                @forelse($notifLatest as $n)
                                    @include('partials.notif-row', ['n' => $n])
                                @empty
                                    <p class="text-center text-sm text-slate-400 py-8">Belum ada notifikasi.</p>
                                @endforelse
                            </div>
                            <a href="{{ route('notifications.index') }}"
                                class="block text-center text-sm font-semibold py-3 border-t border-slate-100 hover:bg-slate-50 transition">Lihat
                                semua</a>
                        </div>
                    </div>
                    <div x-data="{ menu: false }" @click.outside="menu = false" class="relative ml-2">
                        <button @click="menu = !menu"
                            class="flex items-center gap-2 pl-1.5 pr-3 py-1.5 rounded-full bg-white ring-1 ring-slate-200 hover:ring-slate-300 hover:shadow-sm transition">
                            <span
                                class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-cyan-500 text-white grid place-items-center text-sm font-bold">
                                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="max-w-[110px] truncate">{{ auth()->user()->name }}</span>
                            <x-icon name="chevron-down" ::class="menu ? 'rotate-180' : ''"
                                class="w-4 h-4 text-slate-400 transition-transform" />
                        </button>

                        <div x-show="menu" x-cloak x-transition.origin.top.right
                            class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl shadow-slate-900/10 ring-1 ring-slate-200 p-2">
                            <div class="px-3 py-2 border-b border-slate-100 mb-1">
                                <p class="text-sm font-bold truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-100 transition">
                                <x-icon name="layout-dashboard" class="w-4 h-4 text-slate-500" /> Dashboard
                            </a>
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-100 transition">
                                <x-icon name="user" class="w-4 h-4 text-slate-500" /> Profil
                            </a>
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.index') }}"
                                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-100 transition">
                                    <x-icon name="shield-check" class="w-4 h-4 text-slate-500" /> Panel Admin
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-rose-600 hover:bg-rose-50 transition">
                                    <x-icon name="log-out" class="w-4 h-4" /> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </nav>

            <button @click="open = !open"
                class="md:hidden w-10 h-10 grid place-items-center rounded-xl hover:bg-slate-900/5" aria-label="Menu">
                <x-icon name="menu" x-show="!open" class="w-6 h-6" />
                <x-icon name="x" x-show="open" x-cloak class="w-6 h-6" />
            </button>
        </div>

        {{-- MENU HP --}}
        <div x-show="open" x-cloak x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="opacity-0 -translate-y-3" x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-end="opacity-0 -translate-y-3" class="md:hidden px-3 pb-3">

            <div
                class="max-h-[calc(100vh-5.5rem)] overflow-y-auto rounded-3xl bg-white ring-1 ring-slate-200 shadow-2xl shadow-slate-900/10 p-3 space-y-3">

                @auth
                    {{-- Kartu profil --}}
                    <a href="{{ route('profile.edit') }}" @click="open = false"
                        class="relative overflow-hidden flex items-center gap-3 rounded-2xl bg-slate-900 p-4 text-white">
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-500/30 rounded-full blur-2xl"></div>
                        <span
                            class="relative w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-cyan-500 grid place-items-center text-lg font-extrabold shadow-lg shadow-emerald-500/30 shrink-0">
                            {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="relative min-w-0 flex-1">
                            <span class="block font-bold truncate">{{ auth()->user()->name }}</span>
                            <span class="block text-xs text-slate-300 truncate">{{ auth()->user()->email }}</span>
                            <span
                                class="inline-block mt-1.5 px-2 py-0.5 rounded-full bg-white/10 ring-1 ring-white/15 text-[10px] font-bold text-emerald-300">
                                {{ auth()->user()->isAdmin() ? 'ADMIN' : 'MAHASISWA' }}
                            </span>
                        </span>
                        <x-icon name="chevron-right" class="relative w-5 h-5 text-slate-400 shrink-0" />
                    </a>

                    {{-- Aksi cepat --}}
                    <div class="grid grid-cols-2 gap-2.5">
                        <a href="{{ route('items.create', 'hilang') }}" @click="open = false"
                            class="flex flex-col items-center gap-1.5 rounded-2xl bg-rose-50 ring-1 ring-rose-200 py-3.5 text-rose-600 font-semibold text-[13px] active:scale-95 transition">
                            <x-icon name="circle-help" class="w-5 h-5" /> Lapor kehilangan
                        </a>
                        <a href="{{ route('items.create', 'temuan') }}" @click="open = false"
                            class="flex flex-col items-center gap-1.5 rounded-2xl bg-emerald-50 ring-1 ring-emerald-200 py-3.5 text-emerald-700 font-semibold text-[13px] active:scale-95 transition">
                            <x-icon name="package-check" class="w-5 h-5" /> Lapor temuan
                        </a>
                    </div>
                @endauth

                @guest
                    <div class="grid grid-cols-2 gap-2.5">
                        <a href="{{ route('login') }}" @click="open = false"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-50 ring-1 ring-slate-200 py-3 font-semibold text-slate-700 active:scale-95 transition">
                            <x-icon name="lock" class="w-4 h-4" /> Masuk
                        </a>
                        <a href="{{ route('register') }}" @click="open = false"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 py-3 font-semibold text-white shadow-lg shadow-emerald-600/30 active:scale-95 transition">
                            <x-icon name="user" class="w-4 h-4" /> Daftar
                        </a>
                    </div>
                @endguest

                {{-- Daftar menu --}}
                <nav class="space-y-1">
                    <a href="{{ route('home') }}#daftar" @click="open = false"
                        class="group flex items-center gap-3 rounded-2xl px-2.5 py-2.5 hover:bg-slate-50 active:bg-slate-100 transition">
                        <span
                            class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 grid place-items-center shrink-0"><x-icon
                                name="search" class="w-5 h-5" /></span>
                        <span class="flex-1 font-semibold text-slate-800">Cari Barang</span>
                        <x-icon name="chevron-right"
                            class="w-4 h-4 text-slate-300 group-hover:translate-x-0.5 transition" />
                    </a>

                    <a href="{{ route('home') }}#cara-kerja" @click="open = false"
                        class="group flex items-center gap-3 rounded-2xl px-2.5 py-2.5 hover:bg-slate-50 active:bg-slate-100 transition">
                        <span
                            class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 grid place-items-center shrink-0"><x-icon
                                name="lightbulb" class="w-5 h-5" /></span>
                        <span class="flex-1 font-semibold text-slate-800">Cara Kerja</span>
                        <x-icon name="chevron-right"
                            class="w-4 h-4 text-slate-300 group-hover:translate-x-0.5 transition" />
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}" @click="open = false"
                            class="group flex items-center gap-3 rounded-2xl px-2.5 py-2.5 hover:bg-slate-50 active:bg-slate-100 transition">
                            <span
                                class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 grid place-items-center shrink-0"><x-icon
                                    name="layout-dashboard" class="w-5 h-5" /></span>
                            <span class="flex-1 font-semibold text-slate-800">Dashboard</span>
                            <x-icon name="chevron-right"
                                class="w-4 h-4 text-slate-300 group-hover:translate-x-0.5 transition" />
                        </a>

                        <a href="{{ route('notifications.index') }}" @click="open = false"
                            class="group flex items-center gap-3 rounded-2xl px-2.5 py-2.5 hover:bg-slate-50 active:bg-slate-100 transition">
                            <span
                                class="w-10 h-10 rounded-xl bg-rose-100 text-rose-500 grid place-items-center shrink-0"><x-icon
                                    name="bell" class="w-5 h-5" /></span>
                            <span class="flex-1 font-semibold text-slate-800">Notifikasi</span>
                            @if ($notifCount > 0)
                                <span
                                    class="min-w-[22px] h-[22px] px-1.5 rounded-full bg-rose-500 text-white text-xs font-bold grid place-items-center">{{ $notifCount > 9 ? '9+' : $notifCount }}</span>
                            @endif
                            <x-icon name="chevron-right"
                                class="w-4 h-4 text-slate-300 group-hover:translate-x-0.5 transition" />
                        </a>

                        <a href="{{ route('profile.edit') }}" @click="open = false"
                            class="group flex items-center gap-3 rounded-2xl px-2.5 py-2.5 hover:bg-slate-50 active:bg-slate-100 transition">
                            <span
                                class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 grid place-items-center shrink-0"><x-icon
                                    name="user" class="w-5 h-5" /></span>
                            <span class="flex-1 font-semibold text-slate-800">Profil</span>
                            <x-icon name="chevron-right"
                                class="w-4 h-4 text-slate-300 group-hover:translate-x-0.5 transition" />
                        </a>

                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.index') }}" @click="open = false"
                                class="group flex items-center gap-3 rounded-2xl px-2.5 py-2.5 hover:bg-slate-50 active:bg-slate-100 transition">
                                <span
                                    class="w-10 h-10 rounded-xl bg-slate-900 text-emerald-300 grid place-items-center shrink-0"><x-icon
                                        name="shield-check" class="w-5 h-5" /></span>
                                <span class="flex-1 font-semibold text-slate-800">Panel Admin</span>
                                <x-icon name="chevron-right"
                                    class="w-4 h-4 text-slate-300 group-hover:translate-x-0.5 transition" />
                            </a>
                        @endif
                    @endauth
                </nav>

                @auth
                    <form method="POST" action="{{ route('logout') }}" class="pt-3 border-t border-slate-100">
                        @csrf
                        <button
                            class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-rose-50 ring-1 ring-rose-200 py-3 font-semibold text-rose-600 hover:bg-rose-100 active:scale-95 transition">
                            <x-icon name="log-out" class="w-4 h-4" /> Keluar
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4500)" x-show="show" x-cloak
            x-transition:enter="transition duration-300" x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:leave="transition duration-300" x-transition:leave-end="opacity-0 -translate-y-4"
            class="fixed top-20 inset-x-0 z-[60] px-4 pointer-events-none">
            <div
                class="pointer-events-auto mx-auto max-w-md flex items-center gap-3 bg-white rounded-2xl shadow-xl shadow-slate-900/10 ring-1 ring-emerald-200 p-3.5">
                <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 grid place-items-center shrink-0">
                    <x-icon name="circle-check" class="w-5 h-5" />
                </span>
                <p class="flex-1 text-sm font-medium">{{ session('success') }}</p>
                <button @click="show = false" class="text-slate-400 hover:text-slate-700"><x-icon name="x"
                        class="w-4 h-4" /></button>
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-slate-900 text-slate-400 mt-20">
        <div class="max-w-6xl mx-auto px-4 py-10 flex flex-col md:flex-row items-center justify-between gap-4 text-sm">
            <p class="font-bold text-white text-base">LostFound <span class="text-emerald-400">Polmed</span></p>
            <p class="inline-flex items-center gap-1.5">
                Temukan, kembalikan, saling bantu.
                <x-icon name="heart" class="w-4 h-4 text-emerald-400" />
            </p>
            <p>© {{ date('Y') }} LostFound Polmed</p>
        </div>
    </footer>

    <script>
        // Muncul bertahap saat di-scroll
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('show');
                    io.unobserve(e.target);
                }
            });
        }, {
            threshold: 0.12
        });
        document.querySelectorAll('.reveal').forEach(el => io.observe(el));

        // Angka berhitung naik
        const countIO = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                const el = e.target,
                    target = +el.dataset.count,
                    dur = 1200,
                    start = performance.now();
                const tick = (now) => {
                    const p = Math.min((now - start) / dur, 1);
                    el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
                    if (p < 1) requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
                countIO.unobserve(el);
            });
        }, {
            threshold: 0.5
        });
        document.querySelectorAll('[data-count]').forEach(el => countIO.observe(el));
    </script>
</body>

</html>
