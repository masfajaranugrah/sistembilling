<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerTagihanController;
use Illuminate\Support\Facades\Route;

// Login API (tidak perlu auth)
Route::prefix('pelanggan/jernihnet')->group(function () {
    Route::post('/login', [AuthController::class, 'loginMem']);
});

// Semua route berikut butuh auth:sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Ambil tagihan aktif (proses_verifikasi, belum bayar)
    Route::get('/tagihan', [CustomerTagihanController::class, 'getTagihanJson']);

    // Ambil tagihan selesai (status lunas)
    Route::get('/tagihan/selesai', [CustomerTagihanController::class, 'getTagihanSelesaiJson']);

    // Detail tagihan tertentu
    Route::get('/tagihan/{id}', [CustomerTagihanController::class, 'showJson']);

    // Upload bukti pembayaran
    Route::post('/tagihan/{id}/upload', [CustomerTagihanController::class, 'uploadJson']);
});
