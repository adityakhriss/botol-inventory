<x-app-layout>
    <div class="max-w-6xl mx-auto p-6 space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Reset Pengembalian</h1>
                <p class="text-slate-600">Pilih botol merah untuk ditandai sudah dikembalikan.</p>
            </div>
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

        @php
            $typeLabels = [
                'PLASTIK_BESAR' => 'Plastik besar',
                'PLASTIK_KECIL' => 'Plastik kecil',
                'KACA_BESAR' => 'Kaca besar',
                'KACA_KECIL' => 'Kaca kecil',
            ];
            $dynamicTypes = $bottles->pluck('type')->filter()->unique()->values();
            $formatTypeLabel = fn (string $type): string => $typeLabels[$type] ?? ucwords(strtolower(str_replace('_', ' ', $type)));
            $totalBotol = $bottles->count();
            $borrowedBotol = $bottles->where('status', 'BORROWED')->count();
            $availableBotol = $bottles->where('status', 'AVAILABLE')->count();
        @endphp

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 space-y-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Ringkasan Reset Pengembalian</h2>
                <p class="mt-1 text-sm text-slate-600">Pantau status botol dan lompat cepat ke area pemilihan pengembalian.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-slate-200">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Total Botol</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $totalBotol }}</p>
                </div>

                <div class="rounded-xl bg-rose-50 p-4 ring-1 ring-rose-200">
                    <p class="text-xs font-medium uppercase tracking-wide text-rose-700">Sedang Dipinjam</p>
                    <p class="mt-1 text-2xl font-semibold text-rose-800">{{ $borrowedBotol }}</p>
                </div>

                <div class="rounded-xl bg-emerald-50 p-4 ring-1 ring-emerald-200">
                    <p class="text-xs font-medium uppercase tracking-wide text-emerald-700">Sudah Tersedia</p>
                    <p class="mt-1 text-2xl font-semibold text-emerald-800">{{ $availableBotol }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="#bottleGrid" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Pilih Botol</a>
                <a href="#selectedCodes" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 hover:bg-slate-200">Lihat Pilihan</a>
                <a href="#submitBtn" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 hover:bg-slate-200">Tombol Konfirmasi</a>
            </div>
        </div>

        <form method="POST" action="{{ route('pengembalian.return') }}" class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            @csrf

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex flex-wrap gap-4 text-sm text-slate-600">
                    <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Tersedia</div>
                    <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span> Dipinjam</div>
                    <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-slate-400"></span> Dipilih</div>
                </div>

                <button id="submitBtn" type="submit"
                        disabled
                        class="rounded-xl px-4 py-2.5 bg-rose-600 text-white font-semibold
                               disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-500">
                    Tandai Dikembalikan (<span id="selectedCount">0</span>)
                </button>
            </div>

            <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2">
                    {{-- Tabs --}}
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="type-tab px-3 py-1.5 rounded-full text-sm font-semibold bg-slate-900 text-white"
                                data-type="ALL">Semua</button>

                        @foreach($dynamicTypes as $type)
                            <button type="button" class="type-tab px-3 py-1.5 rounded-full text-sm font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200"
                                    data-type="{{ $type }}">
                                {{ $formatTypeLabel((string) $type) }}
                                <span class="ml-1 text-xs text-slate-500">({{ $stockSummary[$type]['total'] ?? 0 }})</span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Grid (Compact + 8 kolom di desktop) --}}
                    <div class="mt-4 max-h-[27rem] overflow-y-auto pr-1 grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-8 gap-2" id="bottleGrid">
                        @foreach($bottles as $bottle)
                            @php
                                $borrowed = $bottle->status === 'BORROWED';
                                $typeLabel = $formatTypeLabel((string) $bottle->type);

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
                                    {{ $borrowed ? '' : 'disabled' }}
                                />

                                <div class="
                                    rounded-lg p-1.5 ring-1 transition text-left select-none
                                    {{ $borrowed
                                        ? 'bg-rose-50 ring-rose-200 cursor-pointer hover:shadow-sm'
                                        : 'bg-white ring-emerald-300 opacity-60 cursor-not-allowed'
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
                                        <div class="mt-1 text-[9px] text-slate-700 leading-tight line-clamp-2">
                                            Peminjam: {{ optional(optional($bottle->activeBorrowItem)->borrow)->borrower_name ?? '-' }}
                                        </div>
                                    @endif
                                </div>

                                <span class="
                                    absolute left-1 top-1 rounded-full px-1 py-[2px] text-[9px] font-semibold leading-none
                                    {{ $borrowed
                                        ? 'bg-rose-100 text-rose-700 ring-1 ring-rose-200 peer-checked:bg-slate-200 peer-checked:text-slate-700 peer-checked:ring-slate-300'
                                        : 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200'
                                    }}
                                ">
                                    {{ $borrowed ? 'Dipinjam' : 'Tersedia' }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <label class="text-sm font-medium text-slate-700">Botol Dipilih</label>
                    <textarea id="selectedCodes"
                              rows="8"
                              readonly
                              class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-900 outline-none"
                              placeholder="Pilih botol merah…"></textarea>

                    <p class="mt-2 text-xs text-slate-500">
                        Hanya botol berstatus <span class="font-semibold text-rose-700">Dipinjam</span> yang bisa dipilih.
                    </p>
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
