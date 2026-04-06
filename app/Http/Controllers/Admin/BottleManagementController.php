<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bottle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BottleManagementController extends Controller
{
    /**
     * @var array<string>
     */
    private array $types = [
        Bottle::TYPE_PLASTIK_BESAR,
        Bottle::TYPE_PLASTIK_KECIL,
        Bottle::TYPE_KACA_BESAR,
        Bottle::TYPE_KACA_KECIL,
    ];

    public function index(Request $request): View
    {
        $type = $request->query('type');
        $q = $request->query('q');

        $bottles = Bottle::query()
            ->when($type, fn ($query) => $query->where('type', $type))
            ->when($q, fn ($query) => $query->where('code', 'like', "%{$q}%"))
            ->orderBy('code')
            ->paginate(20)
            ->withQueryString();

        $stockSummary = Bottle::stockSummaryByType();

        return view('admin.botol.index', [
            'bottles' => $bottles,
            'types' => $this->types,
            'type' => $type,
            'q' => $q,
            'stockSummary' => $stockSummary,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $normalizedCode = trim((string) $request->input('code'));
        $request->merge(['code' => $normalizedCode]);

        $typeRule = 'required|in:' . implode(',', $this->types);

        $data = $request->validate([
            'code' => 'required|string|max:100|unique:bottles,code',
            'type' => $typeRule,
        ]);

        $this->validateCodePatternForType($normalizedCode, $data['type']);

        Bottle::create([
            'code' => $normalizedCode,
            'type' => $data['type'],
            'status' => Bottle::STATUS_AVAILABLE,
        ]);

        return redirect()->route('admin.bottles.index')->with('success', 'Botol berhasil ditambahkan.');
    }

    public function storeBulk(Request $request): RedirectResponse
    {
        $typeRule = 'required|in:' . implode(',', $this->types);

        $data = $request->validate([
            'type' => $typeRule,
            'quantity' => 'required|integer|min:1|max:500',
        ]);

        $type = $data['type'];
        $quantity = (int) $data['quantity'];
        $prefix = $this->prefixForType($type);

        DB::transaction(function () use ($type, $quantity, $prefix): void {
            $lastNumber = Bottle::where('code', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->get()
                ->map(function (Bottle $bottle) use ($prefix): int {
                    $suffix = str_replace($prefix, '', $bottle->code);

                    return ctype_digit($suffix) ? (int) $suffix : 0;
                })
                ->max() ?? 0;

            for ($i = 1; $i <= $quantity; $i++) {
                $nextNumber = $lastNumber + $i;

                Bottle::create([
                    'code' => sprintf('%s%03d', $prefix, $nextNumber),
                    'type' => $type,
                    'status' => Bottle::STATUS_AVAILABLE,
                ]);
            }
        });

        return redirect()->route('admin.bottles.index')->with('success', 'Penambahan massal botol berhasil.');
    }

    public function update(Request $request, Bottle $bottle): RedirectResponse
    {
        $normalizedCode = trim((string) $request->input('code'));
        $request->merge(['code' => $normalizedCode]);

        $typeRule = 'required|in:' . implode(',', $this->types);

        $data = $request->validate([
            'code' => 'required|string|max:100|unique:bottles,code,' . $bottle->id,
            'type' => $typeRule,
        ]);

        $this->validateCodePatternForType($normalizedCode, $data['type']);

        if ($bottle->status === Bottle::STATUS_BORROWED) {
            return back()->withErrors([
                'update' => 'Botol sedang dipinjam, tidak boleh diubah dari menu admin.',
            ]);
        }

        $hasHistory = $bottle->borrowItems()->exists();
        $changingIdentity = $bottle->code !== $normalizedCode || $bottle->type !== $data['type'];

        if ($hasHistory && $changingIdentity) {
            return back()->withErrors([
                'update' => 'Botol dengan histori peminjaman tidak boleh ubah kode/jenis.',
            ]);
        }

        $bottle->update([
            'code' => $normalizedCode,
            'type' => $data['type'],
            'status' => Bottle::STATUS_AVAILABLE,
        ]);

        return redirect()->route('admin.bottles.index')->with('success', 'Data botol berhasil diperbarui.');
    }

    public function destroy(Bottle $bottle): RedirectResponse
    {
        if ($bottle->status === Bottle::STATUS_BORROWED) {
            return back()->withErrors([
                'delete' => 'Botol sedang dipinjam, tidak bisa dihapus.',
            ]);
        }

        if ($bottle->borrowItems()->exists()) {
            return back()->withErrors([
                'delete' => 'Botol sudah memiliki histori peminjaman, tidak bisa dihapus.',
            ]);
        }

        if ($bottle->checkbotItems()->exists()) {
            return back()->withErrors([
                'delete' => 'Botol sudah memiliki histori checkbot, tidak bisa dihapus.',
            ]);
        }

        $bottle->delete();

        return redirect()->route('admin.bottles.index')->with('success', 'Botol berhasil dihapus.');
    }

    private function prefixForType(string $type): string
    {
        return match ($type) {
            Bottle::TYPE_PLASTIK_BESAR => 'PB-',
            Bottle::TYPE_PLASTIK_KECIL => 'PK-',
            Bottle::TYPE_KACA_BESAR => 'KB-',
            Bottle::TYPE_KACA_KECIL => 'KK-',
            default => 'XX-',
        };
    }

    private function validateCodePatternForType(string $code, string $type): void
    {
        $prefix = $this->prefixForType($type);
        $pattern = '/^' . preg_quote($prefix, '/') . '\\d{3,}$/';

        if (!preg_match($pattern, $code)) {
            throw ValidationException::withMessages([
                'code' => "Format kode untuk jenis {$type} harus {$prefix}*** (contoh: {$prefix}001).",
            ]);
        }
    }
}
