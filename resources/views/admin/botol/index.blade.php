<x-app-layout>
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Manajemen Botol</h1>
            <p class="text-slate-600">Kelola jenis, kode, dan jumlah botol.</p>
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
                <h2 class="text-base font-semibold text-slate-900">Tambah Botol</h2>

                <form method="POST" action="{{ route('admin.bottles.store') }}" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label class="text-sm font-medium text-slate-700">Kode Botol</label>
                        <input type="text" name="code" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Contoh: PB-001" required>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700">Jenis</label>
                        <select name="type" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
                            @foreach($types as $itemType)
                                <option value="{{ $itemType }}">{{ $typeLabels[$itemType] ?? str_replace('_', ' ', $itemType) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="rounded-xl px-4 py-2.5 bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                        Simpan Botol
                    </button>
                </form>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <h2 class="text-base font-semibold text-slate-900">Tambah Massal</h2>

                <form method="POST" action="{{ route('admin.bottles.bulk') }}" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label class="text-sm font-medium text-slate-700">Jenis</label>
                        <select name="type" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" required>
                            @foreach($types as $itemType)
                                <option value="{{ $itemType }}">{{ $typeLabels[$itemType] ?? str_replace('_', ' ', $itemType) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-700">Jumlah</label>
                        <input type="number" name="quantity" min="1" max="500" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Contoh: 10" required>
                    </div>

                    <button type="submit" class="rounded-xl px-4 py-2.5 bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
                        Tambah Massal
                    </button>
                </form>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                <div>
                    <label class="text-sm font-medium text-slate-700">Cari Kode</label>
                    <input name="q" value="{{ $q }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="PB-001">
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-700">Filter Jenis</label>
                    <select name="type" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
                        <option value="">Semua</option>
                        @foreach($types as $itemType)
                            <option value="{{ $itemType }}" @selected($type === $itemType)>{{ $typeLabels[$itemType] ?? str_replace('_', ' ', $itemType) }}</option>
                        @endforeach
                    </select>
                </div>

                <button class="rounded-xl px-4 py-2.5 bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800" type="submit">
                    Terapkan
                </button>
            </form>

            <div class="mt-4 overflow-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="text-left p-3">Kode</th>
                            <th class="text-left p-3">Jenis</th>
                            <th class="text-left p-3">Status</th>
                            <th class="text-left p-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($bottles as $bottle)
                            <tr>
                                <td class="p-3 font-medium text-slate-900">{{ $bottle->code }}</td>
                                <td class="p-3 text-slate-700">{{ $typeLabels[$bottle->type] ?? str_replace('_', ' ', $bottle->type) }}</td>
                                <td class="p-3">
                                    @if($bottle->status === 'AVAILABLE')
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">AVAILABLE</span>
                                    @else
                                        <span class="rounded-full bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-700 ring-1 ring-rose-200">BORROWED</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <div class="flex flex-wrap gap-2">
                                        <form method="POST" action="{{ route('admin.bottles.update', $bottle) }}" class="flex flex-wrap gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="code" value="{{ $bottle->code }}" class="w-36 rounded-lg border border-slate-200 px-2 py-1">
                                            <select name="type" class="rounded-lg border border-slate-200 px-2 py-1">
                                                @foreach($types as $itemType)
                                                    <option value="{{ $itemType }}" @selected($bottle->type === $itemType)>{{ $typeLabels[$itemType] ?? str_replace('_', ' ', $itemType) }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="rounded-lg px-3 py-1 bg-slate-900 text-white text-xs font-semibold">Update</button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.bottles.destroy', $bottle) }}" onsubmit="return confirm('Hapus botol ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg px-3 py-1 bg-rose-600 text-white text-xs font-semibold">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-slate-500">Belum ada data botol.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $bottles->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
