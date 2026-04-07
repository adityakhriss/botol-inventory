<?php

namespace App\Exports;

use App\Models\Bottle;
use App\Models\CheckbotItem;
use App\Models\CheckbotRun;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CheckbotRunExport
{
    public function __construct(private readonly CheckbotRun $run)
    {
    }

    public function download(): StreamedResponse
    {
        $run = $this->run->load(['items.bottle', 'creator']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Checkbot Run');

        $headings = [
            'Run ID',
            'Tanggal Uji',
            'Nama Analis',
            'Sampling Rate',
            'Dibuat Oleh',
            'Kode Botol',
            'Jenis Botol',
            'Parameter',
            'Hasil Uji',
            'Status Uji',
        ];

        $sheet->fromArray($headings, null, 'A1');

        $row = 2;
        foreach ($run->items as $item) {
            $sheet->fromArray([
                $run->id,
                optional($run->tested_at)->format('Y-m-d'),
                $this->sanitize((string) $run->analyst_name),
                $this->sanitize((string) $run->sampling_rate),
                $this->sanitize((string) ($run->creator?->name ?? '-')),
                $this->sanitize((string) $item->bottle_code_snapshot),
                $this->sanitize($this->typeLabel($item)),
                $this->sanitize((string) ($item->parameter ?? '-')),
                $this->sanitize((string) ($item->test_result ?? '-')),
                $this->sanitize((string) ($item->status ?? '-')),
            ], null, 'A' . $row);

            $row++;
        }

        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $fileName = sprintf('checkbot-run-%d-%s.xlsx', $run->id, now()->format('Ymd_His'));

        return response()->streamDownload(function () use ($spreadsheet): void {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function typeLabel(CheckbotItem $item): string
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
