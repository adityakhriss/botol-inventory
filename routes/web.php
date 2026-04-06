<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckbotController;
use App\Http\Controllers\Admin\BottleManagementController;

/**
 * Root: kalau belum login -> /login
 * kalau sudah login -> sesuai role
 */
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role === User::ROLE_ADMIN) {
        return redirect()->route('admin.bottles.index');
    }

    if (auth()->user()->role === User::ROLE_ANALIS) {
        return redirect()->route('checkbot.index');
    }

    if (auth()->user()->role === User::ROLE_PENANGGUNG_JAWAB) {
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

    if (auth()->user()->role === User::ROLE_ADMIN) {
        return redirect()->route('admin.bottles.index');
    }

    if (auth()->user()->role === User::ROLE_ANALIS) {
        return redirect()->route('checkbot.index');
    }

    if (auth()->user()->role === User::ROLE_PENANGGUNG_JAWAB) {
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
Route::middleware(['auth', 'role:PEMINJAM,ADMIN'])->group(function () {
    Route::get('/peminjaman', [BorrowController::class, 'index'])->name('peminjaman.index');
    Route::post('/peminjaman', [BorrowController::class, 'store'])->name('peminjaman.store');
});

/**
 * PIC / PENANGGUNG JAWAB
 */
Route::middleware(['auth', 'role:PENANGGUNG_JAWAB,ADMIN'])->group(function () {
    Route::get('/pengembalian', [ReturnController::class, 'index'])->name('pengembalian.index');
    Route::post('/pengembalian', [ReturnController::class, 'returnMany'])->name('pengembalian.return');

    Route::get('/histori', [HistoryController::class, 'index'])->name('histori.index');
});

/**
 * ANALIS + ADMIN
 */
Route::middleware(['auth', 'role:ANALIS,ADMIN'])->group(function () {
    Route::get('/checkbot', [CheckbotController::class, 'index'])->name('checkbot.index');
    Route::post('/checkbot/runs', [CheckbotController::class, 'storeRun'])->name('checkbot.runs.store');
    Route::get('/checkbot/runs/{run}', [CheckbotController::class, 'showRun'])->name('checkbot.runs.show');
    Route::post('/checkbot/runs/{run}/results', [CheckbotController::class, 'saveResults'])->name('checkbot.runs.results');
});

/**
 * ADMIN ONLY
 */
Route::middleware(['auth', 'role:ADMIN'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/botol', [BottleManagementController::class, 'index'])->name('bottles.index');
    Route::post('/botol', [BottleManagementController::class, 'store'])->name('bottles.store');
    Route::patch('/botol/{bottle}', [BottleManagementController::class, 'update'])->name('bottles.update');
    Route::delete('/botol/{bottle}', [BottleManagementController::class, 'destroy'])->name('bottles.destroy');
    Route::post('/botol/bulk', [BottleManagementController::class, 'storeBulk'])->name('bottles.bulk');
});

require __DIR__.'/auth.php';
