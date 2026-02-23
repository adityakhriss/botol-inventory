<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'pic@local.test'],
            [
                'name' => 'PIC',
                'password' => Hash::make('pic12345'),
                'role' => 'PENANGGUNG_JAWAB',
            ]
        );

        User::updateOrCreate(
            ['email' => 'peminjam@local.test'],
            [
                'name' => 'Peminjam',
                'password' => Hash::make('pinjam12345'),
                'role' => 'PEMINJAM',
            ]
        );
    }
}