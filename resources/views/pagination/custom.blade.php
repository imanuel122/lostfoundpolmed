{{-- @var \Illuminate\Pagination\LengthAwarePaginator $paginator --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-col items-center gap-4">

        <p class="text-sm text-slate-500">
            Menampilkan
            <span class="font-bold text-slate-700">{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-bold text-slate-700">{{ $paginator->total() }}</span>
        </p>

        {{-- ===== HP: dua tombol besar ===== --}}
        <div class="flex sm:hidden items-center justify-between gap-3 w-full max-w-sm">
            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex items-center justify-center gap-1.5 rounded-2xl bg-slate-100 text-slate-300 px-4 py-3 text-sm font-semibold cursor-not-allowed">
                    <x-icon name="chevron-left" class="w-4 h-4" /> Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex items-center justify-center gap-1.5 rounded-2xl bg-white ring-1 ring-slate-200 text-slate-700 px-4 py-3 text-sm font-semibold shadow-sm active:scale-95 transition">
                    <x-icon name="chevron-left" class="w-4 h-4" /> Sebelumnya
                </a>
            @endif

            <span class="text-sm font-bold text-slate-500 whitespace-nowrap">
                {{ $paginator->currentPage() }} <span class="font-medium text-slate-300">/</span>
                {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex items-center justify-center gap-1.5 rounded-2xl bg-slate-900 text-white px-4 py-3 text-sm font-semibold shadow-lg shadow-slate-900/20 active:scale-95 transition">
                    Berikutnya <x-icon name="chevron-right" class="w-4 h-4" />
                </a>
            @else
                <span
                    class="inline-flex items-center justify-center gap-1.5 rounded-2xl bg-slate-100 text-slate-300 px-4 py-3 text-sm font-semibold cursor-not-allowed">
                    Berikutnya <x-icon name="chevron-right" class="w-4 h-4" />
                </span>
            @endif
        </div>

        {{-- ===== Desktop dan tablet: nomor halaman ===== --}}
        <div
            class="hidden sm:inline-flex items-center gap-1 bg-white p-1.5 rounded-full ring-1 ring-slate-200 shadow-sm">

            {{-- Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex items-center gap-1.5 px-4 h-10 rounded-full text-sm font-semibold text-slate-300 cursor-not-allowed">
                    <x-icon name="chevron-left" class="w-4 h-4" /> Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex items-center gap-1.5 px-4 h-10 rounded-full text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 active:scale-95 transition">
                    <x-icon name="chevron-left" class="w-4 h-4" /> Sebelumnya
                </a>
            @endif

            <span class="w-px h-5 bg-slate-200 mx-1"></span>

            {{-- Nomor --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span
                        class="w-10 h-10 grid place-items-center text-sm text-slate-400 select-none">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                class="w-10 h-10 grid place-items-center rounded-full bg-slate-900 text-white text-sm font-bold shadow-md shadow-slate-900/20">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" aria-label="Ke halaman {{ $page }}"
                                class="w-10 h-10 grid place-items-center rounded-full text-sm font-semibold text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 hover:-translate-y-0.5 active:scale-90 transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            <span class="w-px h-5 bg-slate-200 mx-1"></span>

            {{-- Berikutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex items-center gap-1.5 px-4 h-10 rounded-full text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 active:scale-95 transition">
                    Berikutnya <x-icon name="chevron-right" class="w-4 h-4" />
                </a>
            @else
                <span
                    class="inline-flex items-center gap-1.5 px-4 h-10 rounded-full text-sm font-semibold text-slate-300 cursor-not-allowed">
                    Berikutnya <x-icon name="chevron-right" class="w-4 h-4" />
                </span>
            @endif
        </div>
    </nav>
@endif
