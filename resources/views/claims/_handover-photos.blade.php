@if ($claim->handoverImages->count())
    @php
        $urut = $claim->handoverImages->sortBy('id')->values();

        // Kelompokkan foto per sesi unggah (selisih <= 60 detik dianggap satu sesi)
        $rounds = [];
        foreach ($urut as $hi) {
            $k = array_key_last($rounds);
            if ($k !== null && abs($hi->created_at->diffInSeconds($rounds[$k][0]->created_at)) <= 60) {
                $rounds[$k][] = $hi;
            } else {
                $rounds[] = [$hi];
            }
        }

        $urls = $urut->map(fn($h) => route('handover.image', $h))->all();
        $jumlah = count($rounds);
        $n = 0;
    @endphp

    <div class="mb-6 space-y-5" x-data="{
        imgs: @js($urls),
        i: 0,
        open: false,
        next() { this.i = (this.i + 1) % this.imgs.length },
        prev() { this.i = (this.i - 1 + this.imgs.length) % this.imgs.length }
    }" @keydown.escape.window="open = false"
        @keydown.arrow-right.window="if (open && imgs.length > 1) next()"
        @keydown.arrow-left.window="if (open && imgs.length > 1) prev()">

        @foreach ($rounds as $r => $fotos)
            @php $terbaru = $r === $jumlah - 1; @endphp
            <div class="{{ $terbaru ? '' : 'opacity-70' }}">
                <div class="flex flex-wrap items-center gap-2">
                    <span
                        class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $terbaru ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $terbaru ? 'Bukti terbaru' : 'Bukti sebelumnya' }}
                    </span>
                    <span class="text-xs text-slate-400">
                        oleh {{ $fotos[0]->uploader->name }} ·
                        {{ $fotos[0]->created_at->translatedFormat('d M Y, H:i') }}
                    </span>
                </div>

                <div class="mt-3 grid grid-cols-3 gap-3">
                    @foreach ($fotos as $hi)
                        <button type="button" @click="i = {{ $n }}; open = true"
                            class="group aspect-square rounded-2xl overflow-hidden ring-1 ring-slate-200 bg-slate-100 cursor-zoom-in">
                            <img src="{{ route('handover.image', $hi) }}" alt="Bukti serah terima" loading="lazy"
                                class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        </button>
                        @php $n++; @endphp
                    @endforeach
                </div>

                @if ($fotos[0]->keterangan)
                    <p class="mt-2 text-sm text-slate-600">"{{ $fotos[0]->keterangan }}"</p>
                @endif
            </div>
        @endforeach

        <p class="flex items-center gap-1.5 text-xs text-slate-400">
            <x-icon name="lock" class="w-3.5 h-3.5 shrink-0" />
            Foto ini privat. Hanya {{ $namaPosting }} ({{ $peranPosting }}), {{ $namaKlaim }}
            ({{ $peranKlaim }}), dan admin yang bisa melihatnya.
        </p>

        {{-- Lightbox --}}
        <template x-teleport="body">
            <div x-show="open" x-cloak x-transition:enter="transition duration-200"
                x-transition:enter-start="opacity-0" x-transition:leave="transition duration-150"
                x-transition:leave-end="opacity-0" @click.self="open = false"
                class="fixed inset-0 z-[90] bg-slate-950/95 backdrop-blur-sm flex items-center justify-center p-4">
                <button type="button" @click="open = false" aria-label="Tutup"
                    class="absolute top-4 right-4 w-11 h-11 rounded-full bg-white/10 text-white grid place-items-center hover:bg-white/20 transition">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
                <template x-if="imgs.length > 1">
                    <div>
                        <button type="button" @click="prev()" aria-label="Sebelumnya"
                            class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 text-white grid place-items-center hover:bg-white/20 transition">
                            <x-icon name="chevron-left" class="w-5 h-5" />
                        </button>
                        <button type="button" @click="next()" aria-label="Berikutnya"
                            class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 text-white grid place-items-center hover:bg-white/20 transition">
                            <x-icon name="chevron-right" class="w-5 h-5" />
                        </button>
                    </div>
                </template>
                <img :src="imgs[i]" alt="Bukti serah terima"
                    class="max-h-[88vh] max-w-full rounded-2xl object-contain shadow-2xl">
            </div>
        </template>
    </div>
@endif
