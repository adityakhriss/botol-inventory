<?php

namespace Database\Seeders;

use App\Models\Bottle;
use Illuminate\Database\Seeder;

class BottleSeeder extends Seeder
{
    public function run(): void
    {
        // Ubah angka ini sesuai kebutuhan
        $plastik = 5;     // P001 - P005
        $kaca = 5;        // K001 - K005
        $kacaKecil = 5;   // KC001 - KC005

        for ($i=1; $i <= $plastik; $i++) {
            Bottle::updateOrCreate(
                ['code' => sprintf('BTL-P%03d', $i)],
                ['type' => 'PLASTIK', 'status' => 'AVAILABLE']
            );
        }

        for ($i=1; $i <= $kaca; $i++) {
            Bottle::updateOrCreate(
                ['code' => sprintf('BTL-K%03d', $i)],
                ['type' => 'KACA', 'status' => 'AVAILABLE']
            );
        }

        for ($i=1; $i <= $kacaKecil; $i++) {
            Bottle::updateOrCreate(
                ['code' => sprintf('BTL-KC%03d', $i)],
                ['type' => 'KACA_KECIL', 'status' => 'AVAILABLE']
            );
        }
    }
}