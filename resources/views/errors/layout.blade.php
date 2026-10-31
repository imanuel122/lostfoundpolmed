@php
    $tones = [
        'emerald' => [
            'from-emerald-50',
            'bg-emerald-300/40',
            'bg-cyan-300/40',
            'from-emerald-500 to-cyan-500',
            'shadow-emerald-500/30',
        ],
        'amber' => [
            'from-amber-50',
            'bg-amber-300/40',
            'bg-orange-200/50',
            'from-amber-500 to-orange-500',
            'shadow-amber-500/30',
        ],
        'rose' => [
            'from-rose-50',
            'bg-rose-300/40',
            'bg-orange-200/50',
            'from-rose-500 to-orange-500',
            'shadow-rose-500/30',
        ],
    ];
    $t = $tones[$tone ?? 'emerald'];
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} - LostFound Polmed</title>
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
                        float: {
                            '0%,100%': {
                                transform: 'translateY(0)'
                            },
                            '50%': {
                                transform: 'translateY(-12px)'
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
                    },
                    animation: {
                        blob: 'blob 14s ease-in-out infinite',
                        float: 'float 5s ease-in-out infinite',
                        'fade-up': 'fadeUp .7s ease-out both'
                    },
                }
            }
        }
    </script>
</head>

<body class="bg-slate-50 text-slate-800 antialiased font-sans">
    <main
        class="relative min-h-screen overflow-hidden grid place-items-center px-4 bg-gradient-to-b {{ $t[0] }} via-white to-slate-50">
        <div class="absolute -top-24 -left-24 w-96 h-96 {{ $t[1] }} rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-1/3 -right-24 w-96 h-96 {{ $t[2] }} rounded-full blur-3xl animate-blob"
            style="animation-delay:-5s"></div>

        <div class="relative w-full max-w-lg text-center py-16">
            <a href="{{ route('home') }}" class="animate-fade-up inline-flex items-center gap-2 font-extrabold text-lg">
                <span
                    class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-cyan-500 grid place-items-center text-white shadow-lg shadow-emerald-500/30">
                    <x-icon name="search" class="w-5 h-5" />
                </span>
                LostFound <span class="text-emerald-600">Polmed</span>
            </a>

            <div class="animate-fade-up mt-10" style="animation-delay:.1s">
                <span
                    class="animate-float inline-grid place-items-center w-24 h-24 rounded-3xl bg-gradient-to-br {{ $t[3] }} text-white shadow-2xl {{ $t[4] }}">
                    <x-icon :name="$icon" class="w-12 h-12" />
                </span>
                <p class="mt-6 text-7xl sm:text-8xl font-extrabold tracking-tight text-slate-900/10 leading-none">
                    {{ $code }}</p>
                <h1 class="-mt-4 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">{{ $title }}
                </h1>
                <p class="mt-3 text-slate-500 leading-relaxed">{{ $text }}</p>
            </div>

            <div class="animate-fade-up mt-8 flex flex-col sm:flex-row gap-3 justify-center"
                style="animation-delay:.2s">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-6 py-3 font-semibold text-white hover:bg-emerald-600 hover:-translate-y-0.5 active:scale-95 transition">
                    <x-icon name="house" class="w-4 h-4" /> Ke beranda
                </a>
                <button type="button"
                    onclick="history.length > 1 ? history.back() : location.assign('{{ route('home') }}')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-white ring-1 ring-slate-200 px-6 py-3 font-semibold text-slate-600 hover:bg-slate-50 hover:-translate-y-0.5 active:scale-95 transition">
                    <x-icon name="arrow-left" class="w-4 h-4" /> Halaman sebelumnya
                </button>
            </div>
        </div>
    </main>
</body>

</html>
