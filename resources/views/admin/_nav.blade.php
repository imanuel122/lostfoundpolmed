@php
    $tabs = [
        ['index', 'Ringkasan', 'bar-chart', route('admin.index')],
        ['items', 'Postingan', 'file-text', route('admin.items')],
        ['claims', 'Klaim', 'shield-check', route('admin.claims')],
        ['users', 'Pengguna', 'users', route('admin.users')],
        ['kategori', 'Kategori', 'tag', route('admin.master', 'kategori')],
        ['lokasi', 'Lokasi', 'map-pin', route('admin.master', 'lokasi')],
    ];
@endphp

<section class="max-w-6xl mx-auto px-4 pt-28">
    <div class="relative overflow-hidden rounded-3xl bg-slate-900 p-6 sm:p-8 text-white animate-fade-up">
        <div class="absolute -top-16 -right-10 w-64 h-64 bg-emerald-500/30 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute -bottom-20 left-1/3 w-56 h-56 bg-cyan-500/20 rounded-full blur-3xl animate-blob"
            style="animation-delay:-6s"></div>
        <div class="relative flex items-center gap-4">
            <span
                class="w-12 h-12 rounded-2xl bg-white/10 ring-1 ring-white/15 text-emerald-300 grid place-items-center shrink-0">
                <x-icon name="shield-check" class="w-6 h-6" />
            </span>
            <div>
                <p class="text-emerald-300 text-sm font-semibold">Panel Admin</p>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">{{ $judul }}</h1>
            </div>
        </div>
    </div>

    <div
        class="mt-5 inline-flex bg-white p-1 rounded-full shadow-sm ring-1 ring-slate-200 overflow-x-auto no-scrollbar max-w-full">
        @foreach ($tabs as $t)
            <a href="{{ $t[3] }}"
                class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold transition
                      {{ $active === $t[0] ? 'bg-slate-900 text-white shadow' : 'text-slate-500 hover:text-slate-900' }}">
                <x-icon :name="$t[2]" class="w-4 h-4" /> {{ $t[1] }}
            </a>
        @endforeach
    </div>

    @if ($errors->any())
        <div class="mt-4 flex items-start gap-3 rounded-2xl bg-rose-50 ring-1 ring-rose-200 p-4 text-sm text-rose-800">
            <x-icon name="alert-circle" class="w-5 h-5 shrink-0 mt-0.5" />
            <div>
                @foreach ($errors->all() as $e)
                    <p>{{ $e }}</p>
                @endforeach
            </div>
        </div>
    @endif
</section>

{{-- MODAL KONFIRMASI BERSAMA --}}
<div x-data="{ open: false, d: {}, loading: false }" @open-confirm.window="d = $event.detail; open = true; loading = false"
    @keydown.escape.window="open = false">
    <template x-teleport="body">
        <div x-show="open" x-cloak x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0"
            x-transition:leave="transition duration-150" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[80] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open = false"></div>

            <div x-show="open" x-transition:enter="transition duration-300 ease-out"
                x-transition:enter-start="opacity-0 translate-y-6 scale-95" x-transition:leave="transition duration-150"
                x-transition:leave-end="opacity-0 scale-95" role="dialog" aria-modal="true"
                class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl shadow-slate-900/30 ring-1 ring-slate-200 overflow-hidden">
                <div class="absolute -top-16 -right-16 w-48 h-48 rounded-full blur-3xl"
                    :class="d.tone === 'rose' ? 'bg-rose-300/40' : 'bg-emerald-300/40'"></div>

                <div class="relative p-7">
                    <span class="w-14 h-14 rounded-2xl text-white grid place-items-center shadow-lg"
                        :class="d.tone === 'rose' ? 'bg-gradient-to-br from-rose-500 to-orange-500 shadow-rose-500/30' :
                            'bg-gradient-to-br from-emerald-500 to-teal-500 shadow-emerald-500/30'">
                        <x-icon name="triangle-alert" x-show="d.tone === 'rose'" class="w-7 h-7" />
                        <x-icon name="circle-check" x-show="d.tone !== 'rose'" class="w-7 h-7" />
                    </span>

                    <h3 class="mt-5 text-xl font-extrabold tracking-tight text-slate-900" x-text="d.title"></h3>
                    <p class="mt-1.5 text-sm text-slate-500" x-text="d.text"></p>

                    <form method="POST" :action="d.action" @submit="loading = true" class="mt-6">
                        @csrf
                        <template x-if="d.reason">
                            <div class="mb-5">
                                <label class="block text-[13px] font-semibold text-slate-700"
                                    x-text="d.label || 'Alasan'"></label>
                                <textarea name="catatan" rows="3" maxlength="300" required minlength="5" placeholder="Tulis alasan singkat..."
                                    class="mt-1.5 w-full rounded-xl px-4 py-3 text-sm outline-none resize-none ring-1 bg-slate-50 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-rose-400 transition"></textarea>
                                <p class="mt-1 text-xs text-slate-400">Alasan ini dikirim ke pembuat postingan.</p>
                            </div>
                        </template>

                        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                            <button type="button" @click="open = false"
                                class="rounded-xl px-5 py-3 text-sm font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition">Batal</button>
                            <button type="submit" :disabled="loading"
                                class="inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3 font-semibold text-white shadow-lg hover:-translate-y-0.5 active:scale-[.98] disabled:opacity-60 disabled:pointer-events-none transition"
                                :class="d.tone === 'rose' ? 'bg-rose-500 shadow-rose-500/30 hover:bg-rose-600' :
                                    'bg-gradient-to-r from-emerald-600 to-teal-600 shadow-emerald-600/30'">
                                <span x-text="loading ? 'Memproses...' : d.button"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
