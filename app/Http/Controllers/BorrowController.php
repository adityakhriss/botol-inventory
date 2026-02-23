<?php

namespace App\Http\Controllers;

use App\Models\Bottle;
use App\Models\Borrow;
use App\Models\BorrowItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowController extends Controller
{
    public function index()
{
    $bottles = \App\Models\Bottle::with('activeBorrowItem.borrow')
        ->orderBy('code')
        ->get();

    return view('peminjaman.index', compact('bottles'));
}

    public function store(Request $request)
    {
        $request->validate([
    'borrower_name' => 'required|string|max:100',
    'borrowed_at'   => 'required|date',
    'bottle_ids'    => 'required|array|min:1',
    'bottle_ids.*'  => 'integer|exists:bottles,id',
]);

        DB::transaction(function () use ($request) {

            $bottles = Bottle::whereIn('id', $request->bottle_ids)
                ->lockForUpdate()
                ->get();

            foreach ($bottles as $bottle) {
                if ($bottle->status !== 'AVAILABLE') {
                    abort(422, "Botol {$bottle->code} tidak tersedia.");
                }
            }

            $borrow = Borrow::create([
    'borrower_name' => $request->borrower_name,
    'borrowed_at'   => $request->borrowed_at,
]);

            foreach ($bottles as $bottle) {
                BorrowItem::create([
                    'borrow_id' => $borrow->id,
                    'bottle_id' => $bottle->id,
                ]);

                $bottle->update([
                    'status' => 'BORROWED'
                ]);
            }
        });

        return redirect()->route('peminjaman.index')
            ->with('success', 'Peminjaman berhasil disimpan.');
    }
}