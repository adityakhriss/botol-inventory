<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $borrows = Borrow::with(['items.bottle', 'handledByUser'])
            ->when($q, function ($query) use ($q) {
                $query->where('borrower_name', 'like', "%$q%")
                    ->orWhereHas('items.bottle', fn($b) => $b->where('code', 'like', "%$q%"));
            })
            ->orderByDesc('borrowed_at')
            ->paginate(10);

        return view('histori.index', compact('borrows', 'q'));
    }
}