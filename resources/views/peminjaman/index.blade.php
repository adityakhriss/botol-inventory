<x-app-layout>
    <div class="max-w-6xl mx-auto p-6 space-y-6">

        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Form Peminjaman</h1>
            <p class="text-slate-600">Isi data, pilih botol tersedia, lalu simpan.</p>
        </div>

        @if(session('success'))
            <div class="rounded-xl bg-emerald-50 p-3 text-emerald-800 ring-1 ring-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-xl bg-rose-50 p-3 text-rose-800 ring-1 ring-rose-200">
                <div class="font-semibold">Terjadi kesalahan:</div>
                <ul class="list-disc ml-5 text-sm">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('peminjaman.store') }}" class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            @csrf

            {{-- Card kiri --}}
            <div class="lg:col-span-2 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <h2 class="text-base font-semibold text-slate-900">Data Peminjaman</h2>

                <div class="mt-4 space-y-4">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Nama Peminjam</label>
                        <input
                            type="text"
                            name="borrower_name"
                            value="{{ old('borrower_name') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 outline-none focus:border-slate-400"
                            placeholder="Contoh: Andi"
                            required
                        />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700">Tanggal Pinjam</label>
                        <input
                            type="date"
                            name="borrowed_at"
                            value="{{ old('borrowed_at', now()->toDateString()) }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 outline-none focus:border-slate-400"
                            required
                        />
                    </div>

                    <div class="rounded-xl bg-slate-50 p-3 ring-1 ring-slate-200">
                        <div class="text-sm text-slate-700">
                            <span class="font-medium">Jumlah dipilih:</span>
                            <span id="selectedCount">0</span> botol
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700">Botol Dipilih</label>
                        <textarea
                            id="selectedCodes"
                            rows="4"
                            readonly
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 outline-none"
                            placeholder="Klik botol hijau untuk memilih…"
                        ></textarea>
                        <p class="mt-1 text-xs text-slate-500">Terisi otomatis dari botol yang kamu klik.</p>
                    </div>

                    <button
                        id="submitBtn"
                        type="submit"
                        disabled
                        class="w-full rounded-xl px-4 py-2.5 text-sm font-semibold transition
                               bg-slate-900 text-white hover:bg-slate-800
                               disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-500"
                    >
                        Simpan Peminjaman
                    </button>

                    <p class="text-xs text-slate-500">
                        Tips: gunakan tab di kanan untuk filter jenis botol.
                    </p>
                </div>
            </div>

            {{-- Card kanan --}}
            <div class="lg:col-span-3 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">

                {{-- Tabs --}}
                <div class="flex flex-wrap gap-2">
                    <button type="button"
                            class="type-tab px-3 py-1.5 rounded-full text-sm font-semibold bg-slate-900 text-white"
                            data-type="ALL">Semua</button>

                    <button type="button"
                            class="type-tab px-3 py-1.5 rounded-full text-sm font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200"
                            data-type="PLASTIK">Plastik</button>

                    <button type="button"
                            class="type-tab px-3 py-1.5 rounded-full text-sm font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200"
                            data-type="KACA">Kaca</button>

                    <button type="button"
                            class="type-tab px-3 py-1.5 rounded-full text-sm font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200"
                            data-type="KACA_KECIL">Kaca Kecil</button>
                </div>

                {{-- Legend --}}
                <div class="mt-4 flex flex-wrap gap-4 text-sm text-slate-600">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Tersedia
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span> Dipinjam
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-slate-400"></span> Dipilih
                    </div>
                </div>

                {{-- Grid (lebih rapat + 10 kolom di desktop) --}}
                <div class="mt-4 grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-8 gap-2" id="bottleGrid">
                    @foreach($bottles as $bottle)
                        @php
                            $borrowed = $bottle->status === 'BORROWED';
                            $typeLabel = $bottle->type === 'PLASTIK' ? 'Plastik' : ($bottle->type === 'KACA' ? 'Kaca' : 'Kaca Kecil');

                            // amankan dari whitespace tersembunyi agar tidak pecah baris
                            $safeCode = preg_replace('/\s+/u', '', (string) $bottle->code);
                        @endphp

                        <label class="relative bottle-card" data-type="{{ $bottle->type }}">
                            <input
                                type="checkbox"
                                name="bottle_ids[]"
                                value="{{ $bottle->id }}"
                                data-code="{{ $safeCode }}"
                                class="peer sr-only bottle-check"
                                {{ $borrowed ? 'disabled' : '' }}
                            />

                            <div class="
                                rounded-lg p-1.5 ring-1 transition text-left select-none
                                {{ $borrowed
                                    ? 'bg-rose-50 ring-rose-200 opacity-60 cursor-not-allowed'
                                    : 'bg-white ring-emerald-300 hover:shadow-sm cursor-pointer'
                                }}
                                peer-checked:bg-slate-100 peer-checked:ring-slate-300
                            ">
                                <div class="text-[9px] text-slate-500 leading-tight">Kode</div>

<div class="mt-0.5 text-[11px] font-semibold text-slate-900 leading-tight whitespace-nowrap">
    {{ $safeCode }}
</div>

<div class="mt-1 text-[9px] text-slate-600 leading-tight">
    {{ $typeLabel }}
</div>

                                @if($borrowed)
                                    <div class="mt-1 text-[10px] text-slate-700 leading-tight line-clamp-2">
                                        Peminjam: {{ optional(optional($bottle->activeBorrowItem)->borrow)->borrower_name ?? '-' }}
                                    </div>
                                @endif
                            </div>

                            <span class="
                                absolute left-1.5 top-1.5 rounded-full px-1.5 py-0.5 text-[10px] font-semibold leading-none
                                {{ $borrowed
                                    ? 'bg-rose-100 text-rose-700 ring-1 ring-rose-200'
                                    : 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200 peer-checked:bg-slate-200 peer-checked:text-slate-700 peer-checked:ring-slate-300'
                                }}
                            ">
                                {{ $borrowed ? 'Dipinjam' : 'Tersedia' }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </form>
    </div>

    <script>
        (function () {
            // --- Selected bottles textarea + button state ---
            const checks = document.querySelectorAll('.bottle-check');
            const countEl = document.getElementById('selectedCount');
            const codesEl = document.getElementById('selectedCodes');
            const submitBtn = document.getElementById('submitBtn');

            function updateSelected() {
                const selected = [...checks].filter(c => c.checked);
                const codes = selected.map(c => c.dataset.code);

                countEl.textContent = selected.length;
                codesEl.value = codes.join(', ');
                submitBtn.disabled = selected.length === 0;
            }

            checks.forEach(c => c.addEventListener('change', updateSelected));
            updateSelected();

            // --- Tabs filter ---
            const tabs = document.querySelectorAll('.type-tab');
            const cards = document.querySelectorAll('.bottle-card');

            function setActive(tab) {
                tabs.forEach(t => {
                    t.classList.remove('bg-slate-900','text-white');
                    t.classList.add('bg-slate-100','text-slate-700','hover:bg-slate-200');
                });
                tab.classList.add('bg-slate-900','text-white');
                tab.classList.remove('bg-slate-100','text-slate-700','hover:bg-slate-200');
            }

            function applyFilter(type) {
                cards.forEach(card => {
                    const match = (type === 'ALL') || (card.dataset.type === type);
                    card.classList.toggle('hidden', !match);
                });
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    setActive(tab);
                    applyFilter(tab.dataset.type);
                });
            });

            applyFilter('ALL');
        })();
    </script>
</x-app-layout>