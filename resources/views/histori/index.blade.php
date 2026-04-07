<x-app-layout>
<div class="max-w-6xl mx-auto p-6 space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Histori Peminjaman</h1>
            <p class="text-slate-600">Hanya bisa diakses Penanggung Jawab.</p>
        </div>

        <div class="w-full sm:w-auto flex flex-col sm:flex-row gap-2 sm:items-center">
            <form method="GET" class="w-full sm:w-80">
                <input name="q" value="{{ $q ?? '' }}"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2"
                    placeholder="Cari nama peminjam / kode botol..." />
            </form>

            <a href="{{ route('histori.export', ['q' => $q]) }}"
               class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                Export XLSX
            </a>
        </div>
    </div>

    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-left p-3">Tanggal Pinjam</th>
                    <th class="text-left p-3">Peminjam</th>
                    <th class="text-left p-3">Botol</th>
                    <th class="text-left p-3">Status</th>
                    <th class="text-left p-3">Terakhir Dikembalikan</th>
                    <th class="text-left p-3">PIC</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach($borrows as $borrow)
                    @php
                        $total = $borrow->items->count();
                        $returned = $borrow->items->whereNotNull('returned_at')->count();
                        $done = ($total > 0 && $returned === $total);
                        $lastReturned = $borrow->items->max('returned_at');
                    @endphp
                    <tr>
                        <td class="p-3 text-slate-700">
                            {{ $borrow->borrowed_at->format('d M Y') }}
                        </td>

                        <td class="p-3 font-medium text-slate-900">
                            {{ $borrow->borrower_name }}
                        </td>

                        <td class="p-3 text-slate-700">
                            <div class="flex flex-wrap gap-2">
                                @foreach($borrow->items as $it)
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700 ring-1 ring-slate-200">
                                        {{ $it->bottle->code }}
                                    </span>
                                @endforeach
                            </div>
                        </td>

                        <td class="p-3">
                            @if($done)
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                                    Selesai
                                </span>
                            @else
                                <span class="rounded-full bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-700 ring-1 ring-rose-200">
                                    Masih dipinjam
                                </span>
                            @endif
                        </td>

                        <td class="p-3 text-slate-700">
                            {{ $lastReturned ? \Carbon\Carbon::parse($lastReturned)->format('d M Y H:i') : '-' }}
                        </td>

                        <td class="p-3 text-slate-700">
                            {{ optional($borrow->handledByUser)->name ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        {{ $borrows->links() }}
    </div>

</div>
</x-app-layout>
