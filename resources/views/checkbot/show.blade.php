<x-app-layout>
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Detail Run Checkbot</h1>
                <p class="text-slate-600">
                    Analis: <span class="font-semibold">{{ $run->analyst_name }}</span>
                    • Tanggal: <span class="font-semibold">{{ $run->tested_at->format('d M Y') }}</span>
                </p>
            </div>

            <a href="{{ route('checkbot.index') }}" class="rounded-xl px-4 py-2.5 bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                Kembali ke Checkbot
            </a>
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
        @endphp

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h2 class="text-base font-semibold text-slate-900">Nomor Botol Hasil Random 5%</h2>
                <span class="text-sm text-slate-600">Total Sampel: <span class="font-semibold">{{ $run->items->count() }}</span></span>
            </div>

            <div class="mt-4 space-y-4">
                @foreach($groupedItems as $type => $items)
                    <div>
                        <div class="text-sm font-semibold text-slate-800">{{ $typeLabels[$type] ?? str_replace('_', ' ', $type) }} ({{ $items->count() }})</div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($items as $item)
                                <span class="rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                    {{ $item->bottle_code_snapshot }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ route('checkbot.runs.results', $run) }}" class="space-y-6">
            @csrf

            @foreach($groupedItems as $type => $items)
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-base font-semibold text-slate-900">Jenis: {{ $typeLabels[$type] ?? str_replace('_', ' ', $type) }}</h2>

                    <div class="mt-4 overflow-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="text-left p-3">Nomor/Kode Botol</th>
                                    <th class="text-left p-3">Parameter</th>
                                    <th class="text-left p-3">Hasil Uji</th>
                                    <th class="text-left p-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($items as $idx => $item)
                                    <tr>
                                        <td class="p-3 font-medium text-slate-900">
                                            {{ $item->bottle_code_snapshot }}
                                            <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                        </td>
                                        <td class="p-3">
                                            <input
                                                type="text"
                                                name="items[{{ $item->id }}][parameter]"
                                                value="{{ old("items.{$item->id}.parameter", $item->parameter) }}"
                                                class="w-full rounded-lg border border-slate-200 px-2 py-1"
                                                placeholder="Contoh: TSS, Minyak Lemak, E.coli"
                                                required
                                            >
                                        </td>
                                        <td class="p-3">
                                            <input
                                                type="text"
                                                name="items[{{ $item->id }}][test_result]"
                                                value="{{ old("items.{$item->id}.test_result", $item->test_result) }}"
                                                class="w-full rounded-lg border border-slate-200 px-2 py-1"
                                                placeholder="Contoh: <1 mg/L"
                                                required
                                            >
                                        </td>
                                        <td class="p-3">
                                            <select name="items[{{ $item->id }}][status]" class="rounded-lg border border-slate-200 px-2 py-1" required>
                                                <option value="">Pilih</option>
                                                <option value="LULUS" @selected(old("items.{$item->id}.status", $item->status) === 'LULUS')>LULUS UJI</option>
                                                <option value="BELUM_LULUS" @selected(old("items.{$item->id}.status", $item->status) === 'BELUM_LULUS')>BELUM LULUS UJI</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <button type="submit" class="rounded-xl px-4 py-2.5 bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                Simpan Hasil Uji
            </button>
        </form>
    </div>
</x-app-layout>
