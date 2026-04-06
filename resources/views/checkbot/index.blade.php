<x-app-layout>
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Checkbot 5%</h1>
            <p class="text-slate-600">Hasil Uji Kualitas Botol (sampling 5% per jenis botol AVAILABLE).</p>
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <h2 class="text-base font-semibold text-slate-900">Buat Run Uji Kualitas</h2>
                <p class="mt-1 text-sm text-slate-600">Klik tombol random 5% untuk mengacak botol otomatis per jenis, lalu sistem menampilkan nomor botol hasil random.</p>

                <form method="POST" action="{{ route('checkbot.runs.store') }}" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label class="text-sm font-medium text-slate-700">Nama Analis</label>
                        <input type="text" name="analyst_name" value="{{ old('analyst_name') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Contoh: Sinta" required>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700">Tanggal Uji Kualitas Botol</label>
                        <input type="date" name="tested_at" value="{{ old('tested_at', now()->toDateString()) }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
                    </div>

                    <button type="submit" class="rounded-xl px-4 py-2.5 bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                        Random 5% Botol
                    </button>
                </form>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <h2 class="text-base font-semibold text-slate-900">Rencana Sampling</h2>

                <div class="mt-4 overflow-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="text-left p-3">Jenis</th>
                                <th class="text-left p-3">Populasi AVAILABLE</th>
                                <th class="text-left p-3">Sampel 5%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($samplingPlan as $type => $plan)
                                <tr>
                                    <td class="p-3 font-medium text-slate-900">{{ $typeLabels[$type] ?? str_replace('_', ' ', $type) }}</td>
                                    <td class="p-3 text-slate-700">{{ $plan['population'] }}</td>
                                    <td class="p-3 text-slate-700">{{ $plan['sample'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-base font-semibold text-slate-900">Histori Run Checkbot</h2>

            <div class="mt-4 overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="text-left p-3">Tanggal Uji</th>
                            <th class="text-left p-3">Analis</th>
                            <th class="text-left p-3">Jumlah Sampel</th>
                            <th class="text-left p-3">Dibuat Oleh</th>
                            <th class="text-left p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($runs as $run)
                            <tr>
                                <td class="p-3 text-slate-700">{{ $run->tested_at->format('d M Y') }}</td>
                                <td class="p-3 font-medium text-slate-900">{{ $run->analyst_name }}</td>
                                <td class="p-3 text-slate-700">{{ $run->items_count }}</td>
                                <td class="p-3 text-slate-700">{{ $run->creator?->name ?? '-' }}</td>
                                <td class="p-3">
                                    <a href="{{ route('checkbot.runs.show', $run) }}" class="rounded-lg px-3 py-1 bg-slate-900 text-white text-xs font-semibold">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-500">Belum ada run checkbot.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $runs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
