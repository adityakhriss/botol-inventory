<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;

/**
 * Root: kalau belum login -> /login
 * kalau sudah login -> sesuai role
 */
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role === 'PENANGGUNG_JAWAB') {
        return redirect()->route('pengembalian.index');
    }

    return redirect()->route('peminjaman.index');
});

/**
 * Matikan dashboard Breeze (biar tidak nyasar).
 * Kalau ada yang akses /dashboard, arahkan sesuai role juga.
 */
Route::get('/dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role === 'PENANGGUNG_JAWAB') {
        return redirect()->route('pengembalian.index');
    }

    return redirect()->route('peminjaman.index');
})->middleware('auth')->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
/**
 * PEMINJAM
 */
Route::middleware(['auth', 'role:PEMINJAM'])->group(function () {
    Route::get('/peminjaman', [BorrowController::class, 'index'])->name('peminjaman.index');
    Route::post('/peminjaman', [BorrowController::class, 'store'])->name('peminjaman.store');
});

/**
 * PIC / PENANGGUNG JAWAB
 */
Route::middleware(['auth', 'role:PENANGGUNG_JAWAB'])->group(function () {
    Route::get('/pengembalian', [ReturnController::class, 'index'])->name('pengembalian.index');
    Route::post('/pengembalian', [ReturnController::class, 'returnMany'])->name('pengembalian.return');

    Route::get('/histori', [HistoryController::class, 'index'])->name('histori.index');
});

require __DIR__.'/auth.php';