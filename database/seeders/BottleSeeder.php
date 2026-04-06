<?php

namespace Database\Seeders;

use App\Models\Bottle;
use Illuminate\Database\Seeder;

class BottleSeeder extends Seeder
{
    public function run(): void
    {
        $plastikBesar = 5;
        $plastikKecil = 5;
        $kacaBesar = 5;
        $kacaKecil = 5;

        for ($i=1; $i <= $plastikBesar; $i++) {
            Bottle::updateOrCreate(
                ['code' => sprintf('PB-%03d', $i)],
                ['type' => Bottle::TYPE_PLASTIK_BESAR, 'status' => Bottle::STATUS_AVAILABLE]
            );
        }

        for ($i=1; $i <= $plastikKecil; $i++) {
            Bottle::updateOrCreate(
                ['code' => sprintf('PK-%03d', $i)],
                ['type' => Bottle::TYPE_PLASTIK_KECIL, 'status' => Bottle::STATUS_AVAILABLE]
            );
        }

        for ($i=1; $i <= $kacaBesar; $i++) {
            Bottle::updateOrCreate(
                ['code' => sprintf('KB-%03d', $i)],
                ['type' => Bottle::TYPE_KACA_BESAR, 'status' => Bottle::STATUS_AVAILABLE]
            );
        }

        for ($i=1; $i <= $kacaKecil; $i++) {
            Bottle::updateOrCreate(
                ['code' => sprintf('KK-%03d', $i)],
                ['type' => Bottle::TYPE_KACA_KECIL, 'status' => Bottle::STATUS_AVAILABLE]
            );
        }
    }
}
