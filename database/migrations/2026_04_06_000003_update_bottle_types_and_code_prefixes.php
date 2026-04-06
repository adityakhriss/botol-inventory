<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE bottles MODIFY type ENUM('PLASTIK','KACA','KACA_KECIL','PLASTIK_BESAR','PLASTIK_KECIL','KACA_BESAR') NOT NULL");

        DB::table('bottles')->where('type', 'PLASTIK')->update(['type' => 'PLASTIK_BESAR']);
        DB::table('bottles')->where('type', 'KACA')->update(['type' => 'KACA_BESAR']);

        DB::statement("UPDATE bottles SET code = CONCAT('PB-', LPAD(CAST(SUBSTRING(code, 6) AS UNSIGNED), 3, '0')) WHERE code LIKE 'BTL-P%'");
        DB::statement("UPDATE bottles SET code = CONCAT('KB-', LPAD(CAST(SUBSTRING(code, 6) AS UNSIGNED), 3, '0')) WHERE code LIKE 'BTL-K%' AND code NOT LIKE 'BTL-KC%'");
        DB::statement("UPDATE bottles SET code = CONCAT('KK-', LPAD(CAST(SUBSTRING(code, 7) AS UNSIGNED), 3, '0')) WHERE code LIKE 'BTL-KC%'");

        DB::statement("ALTER TABLE bottles MODIFY type ENUM('PLASTIK_BESAR','PLASTIK_KECIL','KACA_BESAR','KACA_KECIL') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE bottles MODIFY type ENUM('PLASTIK','KACA','KACA_KECIL','PLASTIK_BESAR','PLASTIK_KECIL','KACA_BESAR') NOT NULL");

        DB::table('bottles')->where('type', 'PLASTIK_BESAR')->update(['type' => 'PLASTIK']);
        DB::table('bottles')->where('type', 'KACA_BESAR')->update(['type' => 'KACA']);

        DB::statement("UPDATE bottles SET code = CONCAT('BTL-P', LPAD(CAST(SUBSTRING(code, 4) AS UNSIGNED), 3, '0')) WHERE code LIKE 'PB-%'");
        DB::statement("UPDATE bottles SET code = CONCAT('BTL-K', LPAD(CAST(SUBSTRING(code, 4) AS UNSIGNED), 3, '0')) WHERE code LIKE 'KB-%'");
        DB::statement("UPDATE bottles SET code = CONCAT('BTL-KC', LPAD(CAST(SUBSTRING(code, 4) AS UNSIGNED), 3, '0')) WHERE code LIKE 'KK-%'");

        DB::statement("ALTER TABLE bottles MODIFY type ENUM('PLASTIK','KACA','KACA_KECIL') NOT NULL");
    }
};
