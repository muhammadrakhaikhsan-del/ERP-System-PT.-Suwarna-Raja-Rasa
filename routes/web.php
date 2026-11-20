<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\KeuanganHarianController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\LaporanBarangController;
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\LaporanRekapitulasiController;
use App\Http\Controllers\PenggunaController;

// Auth routes (public)
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes - semua login required
Route::middleware('role')->group(function () {

    // Dashboard - semua role
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // API Barang by Tenant
    Route::get('/api/barang-by-tenant/{id_tenant}', [BarangController::class, 'getByTenant'])->middleware('role:crew,purchasing,manager');

    // Manager only: Master Data Outlet & Tenant
    Route::middleware('role:manager')->group(function () {
        Route::resource('outlet', OutletController::class);
        Route::resource('tenant', TenantController::class);
    });

    // Purchasing only: Master Data Barang
    Route::middleware('role:purchasing')->group(function () {
        Route::resource('barang', BarangController::class);
    });

    // Crew ONLY: Transaksi Barang Masuk, Keluar, Keuangan Harian
    Route::middleware('role:crew')->group(function () {
        Route::resource('barang_masuk', BarangMasukController::class);
        Route::resource('barang_keluar', BarangKeluarController::class);
        Route::resource('keuangan_harian', KeuanganHarianController::class);
    });

    // Purchasing ONLY: Stock Opname
    Route::middleware('role:purchasing')->group(function () {
        Route::resource('stock_opname', StockOpnameController::class);
    });

    // Purchasing & Manager: Laporan Barang
    Route::middleware('role:purchasing,manager')->group(function () {
        Route::get('/laporan_barang', [LaporanBarangController::class, 'index']);
    });

    // Accounting & Manager: Laporan Keuangan
    Route::middleware('role:accounting,manager')->group(function () {
        Route::get('/laporan_keuangan', [LaporanKeuanganController::class, 'index']);
    });

    // Manager only: Laporan Rekapitulasi & Pengguna
    Route::middleware('role:manager')->group(function () {
        Route::get('/laporan_rekapitulasi', [LaporanRekapitulasiController::class, 'index']);
        
        Route::get('/pengguna', [PenggunaController::class, 'index']);
        Route::post('/pengguna', [PenggunaController::class, 'store']);
        Route::post('/pengguna/{id}/reset-password', [PenggunaController::class, 'resetPassword']);
        Route::delete('/pengguna/{id}', [PenggunaController::class, 'destroy']);
    });
});