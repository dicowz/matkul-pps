<?php

use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\LogChatbotController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Kasir\KasirController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes for Cafe Resto Kopi Kutho
|--------------------------------------------------------------------------
*/

// Root redirection
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('kasir.index');
    }

    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Kasir & Admin)
Route::middleware(['auth'])->group(function () {

    // Kasir Module (POS & Struk) - Accessible by both kasir and admin
    Route::prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/', [KasirController::class, 'index'])->name('index');
        Route::post('/transaksi', [KasirController::class, 'store'])->name('store');
        Route::get('/riwayat', [KasirController::class, 'riwayat'])->name('riwayat');
        Route::get('/struk/{transaksi}', [KasirController::class, 'struk'])->name('struk');
    });

    // Admin Module (Only accessible by role:admin)
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Kategori Menu
        Route::resource('kategori', KategoriController::class)->except(['create', 'edit', 'show']);

        // Menu Management
        Route::resource('menu', MenuController::class)->except(['show']);
        Route::post('/menu/{menu}/toggle', [MenuController::class, 'toggleStatus'])->name('menu.toggle');

        // Karyawan & Fingerprint Management
        Route::resource('karyawan', KaryawanController::class)->except(['show', 'create', 'edit']);
        Route::post('/karyawan/{karyawan}/toggle', [KaryawanController::class, 'toggleStatus'])->name('karyawan.toggle');

        // Absensi Management & Live Device Status
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::delete('/absensi/{absensi}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');

        // Laporan Terintegrasi
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/penjualan', [LaporanController::class, 'penjualan'])->name('penjualan');
            Route::get('/menu-terjual', [LaporanController::class, 'menu_terjual'])->name('menu_terjual');
            Route::get('/absensi', [LaporanController::class, 'absensi'])->name('absensi');
        });

        // Log Chatbot AI & Monitoring
        Route::get('/chatbot', [LogChatbotController::class, 'index'])->name('chatbot.index');
        Route::delete('/chatbot/{log}', [LogChatbotController::class, 'destroy'])->name('chatbot.destroy');
        Route::post('/chatbot/clear', [LogChatbotController::class, 'clear'])->name('chatbot.clear');
    });
});
