<?php

namespace App\Http\Controllers;

use App\Models\Bottle;
use App\Models\BorrowItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index()
{
    $bottles = \App\Models\Bottle::with('activeBorrowItem.borrow')
        ->orderBy('code')
        ->get(); // <-- JANGAN groupBy

    return view('pengembalian.index', compact('bottles'));
}

    public function returnMany(Request $request)
    {
        $data = $request->validate([
            'bottle_ids'   => 'required|array|min:1',
            'bottle_ids.*' => 'integer|exists:bottles,id',
        ]);

        DB::transaction(function () use ($request, $data) {

            // Lock botol yang dipilih untuk menghindari race condition
            $bottles = Bottle::whereIn('id', $data['bottle_ids'])
                ->lockForUpdate()
                ->get();

            foreach ($bottles as $bottle) {

                // hanya proses yang sedang BORROWED
                if ($bottle->status !== 'BORROWED') {
                    continue;
                }

                // Ambil borrow item aktif untuk botol ini
                $borrowItem = BorrowItem::where('bottle_id', $bottle->id)
                    ->whereNull('returned_at')
                    ->lockForUpdate()
                    ->first();

                if (!$borrowItem) {
                    // status botol BORROWED tapi item aktif tidak ketemu (edge case),
                    // biarkan saja atau bisa set available. Kita biarkan aman:
                    continue;
                }

                // Tandai item sudah kembali
                $borrowItem->update([
                    'returned_at' => now(),
                    'handled_by'  => $request->user()->id,
                ]);

                // Update botol jadi available
                $bottle->update([
                    'status' => 'AVAILABLE',
                ]);

                // --- Update borrows jika semua item dalam transaksi sudah kembali ---
                // Lock parent borrow (transaksi) lalu cek masih ada item yang belum returned
                $borrow = $borrowItem->borrow()->lockForUpdate()->first();

                if ($borrow) {
                    $stillActive = $borrow->items()->whereNull('returned_at')->exists();

                    if (!$stillActive) {
                        $borrow->update([
                            'returned_at' => now(),
                            'handled_by'  => $request->user()->id,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('pengembalian.index')
            ->with('success', 'Botol terpilih berhasil ditandai dikembalikan.');
    }
}