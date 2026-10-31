@if (in_array($claim->status, ['disetujui', 'selesai']))
    @php
        $user = auth()->user();
        $sudahSerah = (bool) $claim->handed_over_at;
        $selesai = $claim->status === 'selesai';
        $done = [true, $sudahSerah, $selesai];
        $cur = collect($done)->search(false);
        $bisaKonfirmasi = $pengklaim || $user->isAdmin();
        $oto = $bolehReview && $sudahSerah && !$selesai && $claim->handed_over_at->lte(now()->subDays(3));

        $kata = $temuan
            ? [
                'upload' => 'Barang sudah saya serahkan',
                'konfirm' => 'Saya sudah menerima barang',
                'tolak' => 'Belum menerima',
                'ketUp' => 'Foto saat menyerahkan barang (misalnya barang di tangan pemilik).',
            ]
            : [
                'upload' => 'Barang sudah saya terima',
                'konfirm' => 'Benar, barang sudah saya serahkan',
                'tolak' => 'Belum diserahkan',
                'ketUp' => 'Foto saat menerima barang kembali.',
            ];
    @endphp

    <div x-data="{ konfirmasi: false, tolak: {{ $errors->has('dispute_note') ? 'true' : 'false' }}, loading: false }" @keydown.escape.window="konfirmasi = false"
        class="animate-fade-up bg-white rounded-3xl ring-1 ring-slate-200 shadow-xl shadow-slate-900/5 p-6 sm:p-7">

        <div class="flex items-start gap-3">
            <span
                class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 text-white grid place-items-center shrink-0">
                <x-icon name="package-check" class="w-5 h-5" />
            </span>
            <div>
                <h2 class="font-bold text-slate-900">Serah terima</h2>
                <p class="text-sm text-slate-500">Bukti penyerahan dicatat supaya {{ $peranPosting }} dan
                    {{ $peranKlaim }} sama-sama terlindungi.</p>
            </div>
        </div>

        {{-- Langkah --}}
        <ol class="mt-6 grid grid-cols-3 gap-2">
            @foreach (['Klaim disetujui', 'Bukti serah terima', 'Dikonfirmasi'] as $n => $label)
                <li class="text-center">
                    <span
                        class="mx-auto w-9 h-9 rounded-full grid place-items-center text-sm font-bold transition
                             {{ $done[$n] ? 'bg-emerald-600 text-white' : ($cur === $n ? 'bg-amber-100 text-amber-700 ring-2 ring-amber-300' : 'bg-slate-100 text-slate-400') }}">
                        @if ($done[$n])
                            <x-icon name="check" class="w-4 h-4" />
                        @else
                            {{ $n + 1 }}
                        @endif
                    </span>
                    <span
                        class="block mt-2 text-xs font-semibold {{ $done[$n] || $cur === $n ? 'text-slate-800' : 'text-slate-400' }}">{{ $label }}</span>
                </li>
            @endforeach
        </ol>

        <div class="mt-6">

            {{-- Bukti Serah Terima --}}
            @include('claims._handover-photos')

            {{-- ===== SELESAI ===== --}}
            @if ($selesai)
                <div class="rounded-2xl bg-blue-50 ring-1 ring-blue-200 p-4 flex gap-3">
                    <span
                        class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 grid place-items-center shrink-0"><x-icon
                            name="circle-check" class="w-5 h-5" /></span>
                    <div class="text-sm self-center">
                        <p class="font-bold text-blue-900">Serah terima selesai</p>
                        <p class="text-blue-800/80">Dikonfirmasi pada
                            {{ $claim->confirmed_at?->translatedFormat('d F Y, H:i') }}.</p>
                    </div>
                </div>

                {{-- ===== BELUM ADA BUKTI ===== --}}
            @elseif(!$sudahSerah)
                @if ($claim->dispute_note)
                    <div class="mb-4 rounded-2xl bg-rose-50 ring-1 ring-rose-200 p-4 flex gap-3">
                        <span
                            class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 grid place-items-center shrink-0"><x-icon
                                name="triangle-alert" class="w-5 h-5" /></span>
                        <div class="text-sm">
                            <p class="font-bold text-rose-900">{{ $claim->claimant->name }} ({{ $peranKlaim }})
                                menyatakan: {{ $kata['tolak'] }}</p>
                            <p class="mt-0.5 text-rose-800/80">"{{ $claim->dispute_note }}"</p>
                            @if ($bolehReview)
                                <p class="mt-1 text-rose-800/80">Selesaikan dulu urusannya, lalu unggah bukti yang baru.
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($bolehReview)
                    <form method="POST" action="{{ route('handover.store', $claim) }}" enctype="multipart/form-data"
                        @submit="loading = true" x-data="{
                            files: [],
                            previews: [],
                            error: '',
                            drag: false,
                            max: 3,
                            maxSize: 5 * 1024 * 1024,
                            agreed: false,
                            add(list) {
                                this.error = '';
                                for (const f of Array.from(list)) {
                                    if (!['image/jpeg', 'image/png', 'image/webp'].includes(f.type)) { this.error = 'Format foto harus JPG, PNG, atau WEBP.'; continue; }
                                    if (f.size > this.maxSize) { this.error = 'Ukuran tiap foto maksimal 5 MB.'; continue; }
                                    if (this.files.length >= this.max) { this.error = 'Maksimal ' + this.max + ' foto.'; break; }
                                    this.files.push(f);
                                    this.previews.push(URL.createObjectURL(f));
                                }
                                this.sync();
                            },
                            remove(i) {
                                URL.revokeObjectURL(this.previews[i]);
                                this.files.splice(i, 1);
                                this.previews.splice(i, 1);
                                this.error = '';
                                this.sync();
                            },
                            sync() {
                                const dt = new DataTransfer();
                                this.files.forEach(f => dt.items.add(f));
                                this.$refs.input.files = dt.files;
                                if (!this.files.length) this.agreed = false;
                            }
                        }">
                        @csrf
                        <p class="text-sm text-slate-600">
                            Setelah bertemu dan barang diserahkan, unggah <b>foto bukti</b> (wajib, maksimal 3).
                            {{ $kata['ketUp'] }}
                        </p>

                        <input type="file" name="photos[]" x-ref="input" multiple
                            accept="image/jpeg,image/png,image/webp" class="hidden" @change="add($event.target.files)">

                        <button type="button" @click="$refs.input.click()" @dragover.prevent="drag = true"
                            @dragleave.prevent="drag = false"
                            @drop.prevent="drag = false; add($event.dataTransfer.files)" x-show="files.length < max"
                            :class="drag ? 'border-emerald-300 bg-emerald-50/60 scale-[1.01]' :
                                'border-slate-300 bg-slate-50 hover:bg-slate-100'"
                            class="mt-4 w-full rounded-2xl border-2 border-dashed px-4 py-8 text-center transition">
                            <span
                                class="mx-auto w-12 h-12 rounded-2xl bg-white ring-1 ring-slate-200 shadow-sm grid place-items-center text-slate-500"><x-icon
                                    name="cloud-upload" class="w-6 h-6" /></span>
                            <span class="block mt-3 text-sm font-bold text-slate-700">Klik untuk pilih foto, atau seret
                                ke sini</span>
                            <span class="block mt-1 text-xs text-slate-400">JPG, PNG, WEBP. Maksimal 3 foto, 5 MB per
                                foto.</span>
                        </button>

                        <div x-show="previews.length" x-cloak class="mt-4 grid grid-cols-3 gap-3">
                            <template x-for="(src, i) in previews" :key="src">
                                <div
                                    class="group relative aspect-square rounded-2xl overflow-hidden ring-1 ring-slate-200 bg-slate-100">
                                    <img :src="src" alt="Pratinjau" class="w-full h-full object-cover">
                                    <button type="button" @click="remove(i)" aria-label="Hapus foto"
                                        class="absolute top-1.5 right-1.5 w-7 h-7 rounded-full bg-white/95 text-rose-600 grid place-items-center shadow sm:opacity-0 group-hover:opacity-100 hover:bg-rose-600 hover:text-white transition">
                                        <x-icon name="trash-2" class="w-4 h-4" />
                                    </button>
                                </div>
                            </template>
                        </div>

                        <p x-show="error" x-cloak
                            class="mt-3 flex items-center gap-1.5 text-xs font-medium text-rose-600">
                            <x-icon name="alert-circle" class="w-4 h-4 shrink-0" /> <span x-text="error"></span>
                        </p>
                        @if ($errors->first('photos') || $errors->first('photos.*'))
                            <p class="mt-3 flex items-center gap-1.5 text-xs font-medium text-rose-600">
                                <x-icon name="alert-circle" class="w-4 h-4 shrink-0" />
                                {{ $errors->first('photos') ?: $errors->first('photos.*') }}
                            </p>
                        @endif

                        <div class="mt-4">
                            <label for="keterangan" class="block text-[13px] font-semibold text-slate-700">Catatan <span
                                    class="text-xs font-medium text-slate-400">(opsional)</span></label>
                            <input id="keterangan" name="keterangan" type="text" maxlength="150"
                                value="{{ old('keterangan') }}" placeholder="Contoh: Diserahkan di depan perpustakaan"
                                class="mt-1.5 w-full rounded-xl bg-slate-50 px-4 py-3 text-sm outline-none ring-1 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-emerald-500 transition">
                        </div>

                        <label x-show="files.length > 0" x-cloak
                            class="mt-4 flex items-start gap-3 cursor-pointer rounded-xl p-4 ring-1 transition select-none"
                            :class="agreed ? 'bg-emerald-50 ring-emerald-300' : 'bg-amber-50 ring-amber-300'">
                            <input type="checkbox" name="privacy_ack" value="1" x-model="agreed"
                                class="mt-0.5 w-5 h-5 shrink-0 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-sm font-bold" :class="agreed ? 'text-emerald-800' : 'text-amber-900'">Saya
                                sudah menutupi data pribadi pada foto</span>
                        </label>
                        @error('privacy_ack')
                            <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                        @enderror

                        <button type="submit" :disabled="loading || files.length === 0 || !agreed"
                            class="mt-5 w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3.5 font-semibold text-white shadow-lg shadow-emerald-600/30 hover:-translate-y-0.5 active:scale-[.98] disabled:opacity-50 disabled:grayscale disabled:pointer-events-none transition">
                            <x-icon name="package-check" class="w-5 h-5" />
                            <span
                                x-text="loading ? 'Mengirim...' : '{{ $kata['upload'] }}'">{{ $kata['upload'] }}</span>
                        </button>
                    </form>
                @else
                    <div class="rounded-2xl bg-amber-50 ring-1 ring-amber-200 p-4 flex gap-3">
                        <span
                            class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 grid place-items-center shrink-0"><x-icon
                                name="clock" class="w-5 h-5" /></span>
                        <div class="text-sm self-center">
                            <p class="font-bold text-amber-900">Silakan bertemu untuk serah terima</p>
                            <p class="text-amber-800/80">Setelah barang diserahkan, {{ $namaPosting }}
                                ({{ $peranPosting }}) mengunggah foto bukti, lalu kamu diminta mengonfirmasi di sini.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- ===== BUKTI SUDAH ADA, MENUNGGU KONFIRMASI ===== --}}
            @else
                @if ($bisaKonfirmasi)
                    <div class="mt-5 rounded-2xl bg-emerald-50 ring-1 ring-emerald-200 p-4">
                        <p class="text-sm font-bold text-emerald-900">Bukti serah terima sudah diunggah</p>
                        <p class="text-sm text-emerald-800/80 mt-0.5">Cocokkan dengan kenyataannya, lalu konfirmasi.
                        </p>
                    </div>

                    <div x-show="!tolak" class="mt-4 grid sm:grid-cols-2 gap-3">
                        <button type="button" @click="konfirmasi = true"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3.5 font-semibold text-white shadow-lg shadow-emerald-600/30 hover:-translate-y-0.5 active:scale-[.98] transition">
                            <x-icon name="circle-check" class="w-5 h-5" /> {{ $kata['konfirm'] }}
                        </button>
                        @if ($pengklaim)
                            <button type="button" @click="tolak = true"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-50 ring-1 ring-rose-200 px-6 py-3.5 font-semibold text-rose-600 hover:bg-rose-100 hover:-translate-y-0.5 active:scale-[.98] transition">
                                <x-icon name="circle-x" class="w-5 h-5" /> {{ $kata['tolak'] }}
                            </button>
                        @endif
                    </div>

                    @if ($pengklaim)
                        <form x-show="tolak" x-cloak method="POST" action="{{ route('handover.dispute', $claim) }}"
                            @submit="loading = true" class="mt-4">
                            @csrf
                            <label for="dispute_note" class="block text-[13px] font-semibold text-slate-700">Apa yang
                                belum sesuai?</label>
                            <textarea id="dispute_note" name="dispute_note" rows="3" maxlength="300"
                                placeholder="Contoh: Saya belum bertemu dan belum menerima barangnya."
                                class="mt-1.5 w-full rounded-xl px-4 py-3 text-sm outline-none resize-none ring-1 bg-slate-50 ring-slate-200 focus:bg-white focus:ring-2 focus:ring-rose-400 transition">{{ old('dispute_note') }}</textarea>
                            @error('dispute_note')
                                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                            <div class="mt-3 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                                <button type="button" @click="tolak = false"
                                    class="py-3 px-4 text-sm font-semibold text-slate-500 hover:text-slate-900 transition">Batal</button>
                                <button type="submit" :disabled="loading"
                                    class="rounded-xl bg-rose-500 px-6 py-3 font-semibold text-white shadow-lg shadow-rose-500/30 hover:bg-rose-600 active:scale-[.98] disabled:opacity-60 transition">Kirim
                                    laporan</button>
                            </div>
                        </form>
                    @endif
                @else
                    <div class="mt-5 rounded-2xl bg-amber-50 ring-1 ring-amber-200 p-4 flex gap-3">
                        <span
                            class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 grid place-items-center shrink-0"><x-icon
                                name="clock" class="w-5 h-5" /></span>
                        <div class="text-sm self-center">
                            <p class="font-bold text-amber-900">Menunggu konfirmasi dari {{ $claim->claimant->name }}
                                ({{ $peranKlaim }})
                            </p>
                            <p class="text-amber-800/80">Terkirim {{ $claim->handed_over_at->diffForHumans() }}.
                                @if (!$oto)
                                    Kalau tidak ada respons selama 3 hari, kamu bisa menyelesaikannya sendiri.
                                @endif
                            </p>
                        </div>
                    </div>
                    @if ($oto)
                        <button type="button" @click="konfirmasi = true"
                            class="mt-4 w-full inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-6 py-3.5 font-semibold text-white hover:bg-slate-700 active:scale-[.98] transition">
                            <x-icon name="circle-check" class="w-5 h-5" /> Selesaikan sendiri
                        </button>
                    @endif
                @endif
            @endif
        </div>

        {{-- MODAL KONFIRMASI --}}
        <template x-teleport="body">
            <div x-show="konfirmasi" x-cloak x-transition:enter="transition duration-200"
                x-transition:enter-start="opacity-0" x-transition:leave="transition duration-150"
                x-transition:leave-end="opacity-0" class="fixed inset-0 z-[80] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="konfirmasi = false"></div>
                <div x-show="konfirmasi" x-transition:enter="transition duration-300 ease-out"
                    x-transition:enter-start="opacity-0 translate-y-6 scale-95"
                    x-transition:leave="transition duration-150" x-transition:leave-end="opacity-0 scale-95"
                    role="dialog" aria-modal="true"
                    class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl shadow-slate-900/30 ring-1 ring-slate-200 overflow-hidden">
                    <div class="absolute -top-16 -right-16 w-48 h-48 bg-emerald-300/40 rounded-full blur-3xl"></div>
                    <div class="relative p-7">
                        <span
                            class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 text-white grid place-items-center shadow-lg shadow-emerald-500/30">
                            <x-icon name="package-check" class="w-7 h-7" />
                        </span>
                        <h3 class="mt-5 text-xl font-extrabold tracking-tight text-slate-900">Selesaikan serah terima?
                        </h3>
                        <p class="mt-1.5 text-sm text-slate-500">
                            <span class="font-bold text-slate-700">{{ $item->judul }}</span> akan ditandai
                            <b>sudah dikembalikan</b>, dan klaim lain yang masih menunggu otomatis ditolak.
                        </p>
                        <p
                            class="mt-3 text-xs text-amber-700 bg-amber-50 ring-1 ring-amber-200 rounded-xl px-3.5 py-2.5">
                            Tindakan ini tidak bisa dibatalkan.</p>

                        <form method="POST" action="{{ route('handover.confirm', $claim) }}"
                            @submit="loading = true"
                            class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                            @csrf
                            <button type="button" @click="konfirmasi = false"
                                class="rounded-xl px-5 py-3 text-sm font-semibold text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition">Kembali</button>
                            <button type="submit" :disabled="loading"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3 font-semibold text-white shadow-lg shadow-emerald-600/30 hover:-translate-y-0.5 active:scale-[.98] disabled:opacity-60 disabled:pointer-events-none transition">
                                <span x-text="loading ? 'Memproses...' : 'Ya, selesaikan'">Ya, selesaikan</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endif
