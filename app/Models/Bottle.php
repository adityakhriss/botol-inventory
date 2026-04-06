<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bottle extends Model
{
    public const TYPE_PLASTIK_BESAR = 'PLASTIK_BESAR';
    public const TYPE_PLASTIK_KECIL = 'PLASTIK_KECIL';
    public const TYPE_KACA_BESAR = 'KACA_BESAR';
    public const TYPE_KACA_KECIL = 'KACA_KECIL';

    public const STATUS_AVAILABLE = 'AVAILABLE';
    public const STATUS_BORROWED = 'BORROWED';

    protected $fillable = ['code','type','status'];

    public function borrowItems() {
        return $this->hasMany(BorrowItem::class);
    }

    public function activeBorrowItem() {
        return $this->hasOne(BorrowItem::class)->whereNull('returned_at');
    }

    public function checkbotItems()
    {
        return $this->hasMany(CheckbotItem::class);
    }

    public function markAsBorrowed(): void
    {
        $this->update([
            'status' => self::STATUS_BORROWED,
        ]);
    }

    public function markAsAvailable(): void
    {
        $this->update([
            'status' => self::STATUS_AVAILABLE,
        ]);
    }

    /**
     * @return array<string, array<string, int>>
     */
    public static function stockSummaryByType(): array
    {
        $summary = [
            self::TYPE_PLASTIK_BESAR => ['total' => 0, 'available' => 0, 'borrowed' => 0],
            self::TYPE_PLASTIK_KECIL => ['total' => 0, 'available' => 0, 'borrowed' => 0],
            self::TYPE_KACA_BESAR => ['total' => 0, 'available' => 0, 'borrowed' => 0],
            self::TYPE_KACA_KECIL => ['total' => 0, 'available' => 0, 'borrowed' => 0],
        ];

        $rows = self::query()
            ->select('type', 'status', DB::raw('count(*) as total'))
            ->groupBy('type', 'status')
            ->get();

        foreach ($rows as $row) {
            $type = (string) $row->type;
            $status = (string) $row->status;
            $count = (int) $row->total;

            if (!isset($summary[$type])) {
                continue;
            }

            $summary[$type]['total'] += $count;

            if ($status === self::STATUS_AVAILABLE) {
                $summary[$type]['available'] += $count;
            }

            if ($status === self::STATUS_BORROWED) {
                $summary[$type]['borrowed'] += $count;
            }
        }

        return $summary;
    }
}
