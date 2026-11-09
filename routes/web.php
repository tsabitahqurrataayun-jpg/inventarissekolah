<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/lupa-sandi', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/lupa-sandi', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');
    Route::get('/reset-sandi/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-sandi', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/katalog', [InventoryController::class, 'catalog'])->middleware('role:siswa,guru')->name('inventories.catalog');
    Route::get('/inventaris/{inventory}/foto', [InventoryController::class, 'image'])->name('inventories.image');
    Route::get('/laporan', [LoanController::class, 'report'])->middleware('role:admin')->name('reports.index');
    Route::get('/laporan/cetak', [LoanController::class, 'printReport'])->middleware('role:admin')->name('reports.print');
    Route::get('/laporan/export', [LoanController::class, 'exportReport'])->middleware('role:admin')->name('reports.export');
    Route::get('/peminjaman', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/pengembalian', [LoanController::class, 'returnedIndex'])->middleware('role:admin')->name('loans.returned');
    Route::get('/peminjaman/baru', [LoanController::class, 'create'])->middleware('role:siswa,guru')->name('loans.create');
    Route::post('/peminjaman', [LoanController::class, 'store'])->middleware('role:siswa,guru')->name('loans.store');
    Route::post('/peminjaman/{loan}/pengembalian', [LoanController::class, 'requestReturn'])->middleware('role:siswa,guru')->name('loans.return');
    Route::patch('/peminjaman/{loan}/status', [LoanController::class, 'updateStatus'])->middleware('role:admin')->name('loans.status');
    Route::get('/peminjaman/{loan}/bukti/{type}', [LoanController::class, 'showEvidence'])->whereIn('type', ['borrowing', 'return'])->name('loans.evidence');
    Route::middleware('role:admin')->group(function () {
        Route::get('/inventaris', [InventoryController::class, 'index'])->name('inventories.index');
        Route::post('/inventaris', [InventoryController::class, 'store'])->name('inventories.store');
        Route::patch('/inventaris/{inventory}', [InventoryController::class, 'update'])->name('inventories.update');
        Route::delete('/inventaris/{inventory}', [InventoryController::class, 'destroy'])->name('inventories.destroy');
    });
});
