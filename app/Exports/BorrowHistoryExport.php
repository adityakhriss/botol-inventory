<?php

namespace App\Exports;

use App\Models\Bottle;
use App\Models\Borrow;
use App\Models\BorrowItem;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BorrowHistoryExport
{
    public function __construct(private readonly ?string $query)
    {
    }

    public function download(): StreamedResponse
    {
        $q = $this->query;

        $borrows = Borrow::with(['items.bottle', 'handledByUser'])
            ->when($q, function (Builder $query) use ($q): void {
                $query->where(function (Builder $scoped) use ($q): void {
                    $scoped->where('borrower_name', 'like', "%{$q}%")
                        ->orWhereHas('items.bottle', fn (Builder $bottleQuery) => $bottleQuery->where('code', 'like', "%{$q}%"));
                });
            })
            ->orderByDesc('borrowed_at')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Histori Peminjaman');

        $headings = [
            'Borrow ID',
            'Tanggal Pinjam',
            'Nama Peminjam',
            'Kode Botol',
            'Jenis Botol',
            'Status',
            'Tanggal Kembali Item',
            'Terakhir Dikembalikan (Transaksi)',
            'PIC',
        ];

        $sheet->fromArray($headings, null, 'A1');

        $row = 2;
        foreach ($borrows as $borrow) {
            $total = $borrow->items->count();
            $returned = $borrow->items->whereNotNull('returned_at')->count();
            $done = ($total > 0 && $returned === $total);
            $status = $done ? 'Selesai' : 'Masih dipinjam';
            $lastReturned = $borrow->items->max('returned_at');

            foreach ($borrow->items as $item) {
                $sheet->fromArray([
                    $borrow->id,
                    optional($borrow->borrowed_at)->format('Y-m-d H:i:s'),
                    $this->sanitize((string) $borrow->borrower_name),
                    $this->sanitize((string) ($item->bottle?->code ?? '-')),
                    $this->sanitize($this->typeLabel($item)),
                    $status,
                    optional($item->returned_at)->format('Y-m-d H:i:s') ?? '-',
                    $lastReturned ? (string) $lastReturned : '-',
                    $this->sanitize((string) ($borrow->handledByUser?->name ?? '-')),
                ], null, 'A' . $row);

                $row++;
            }
        }

        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $fileName = sprintf('histori-peminjaman-%s.xlsx', now()->format('Ymd_His'));

        return response()->streamDownload(function () use ($spreadsheet): void {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function typeLabel(BorrowItem $item): string
    {
        return match ($item->bottle?->type) {
            Bottle::TYPE_PLASTIK_BESAR => 'Plastik besar',
            Bottle::TYPE_PLASTIK_KECIL => 'Plastik kecil',
            Bottle::TYPE_KACA_BESAR => 'Kaca besar',
            Bottle::TYPE_KACA_KECIL => 'Kaca kecil',
            default => '-',
        };
    }

    private function sanitize(string $value): string
    {
        if (str_starts_with($value, '=') || str_starts_with($value, '+') || str_starts_with($value, '-') || str_starts_with($value, '@')) {
            return "'" . $value;
        }

        return $value;
    }
}
