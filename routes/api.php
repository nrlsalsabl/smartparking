<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ApiVehicleTypeController;
use App\Http\Controllers\Api\ApiParkingTransactionController;
use App\Http\Controllers\Api\ApiBookingController;
use App\Http\Controllers\Api\ApiVehicleController;
use App\Http\Controllers\Api\ApiNotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. ROUTE PUBLIC (Bisa diakses tanpa login/token)
// =========================================================================
Route::post('parking/scan/{token}', [ApiParkingTransactionController::class, 'scan']);
Route::post('/login', [AuthController::class, 'login']);


// =========================================================================
// 2. ROUTE PROTECTED (Wajib masukkan Bearer Token JWT)
// =========================================================================
Route::middleware('auth:api')->group(function () {

    Route::get('/notifications', [ApiNotificationController::class, 'index']);
    Route::get('/notifications/count', [ApiNotificationController::class, 'count']);
    Route::get('/notifications/read/{id}', [ApiNotificationController::class, 'read']);

    Route::apiResource('vehicles', ApiVehicleController::class);
    Route::apiResource('bookings', ApiBookingController::class);

    // Fitur Pembayaran & Cek Status (Semua Role setelah Login)
    Route::prefix('payments')->group(function () {
        Route::post('/process/{id}', [ApiParkingTransactionController::class, 'processPayment']);
        Route::get('/status/{id}', [ApiParkingTransactionController::class, 'getPaymentStatus']);
    });

    // ---------------------------------------------------------------------
    // KHUSUS ROLE: ADMIN
    // ---------------------------------------------------------------------
    Route::middleware('role:admin')->group(function () {
        // CRUD Tipe Kendaraan (Murni JSON)
        Route::apiResource('vehicle-types', ApiVehicleTypeController::class);
        
        // Tempat menaruh API Resource Admin lainnya nanti:
        Route::apiResource('parking-areas', ApiParkingAreaController::class);
    });

    // ---------------------------------------------------------------------
    // KHUSUS ROLE: ADMIN & OFFICER
    // ---------------------------------------------------------------------
    Route::middleware('role:admin,officer')->group(function () {
        // Tempat menaruh API Officer jika dibutuhkan nanti:
        // Route::get('/transactions', [ApiParkingTransactionController::class, 'index']);
    });

    // ---------------------------------------------------------------------
    // KHUSUS ROLE: CUSTOMER
    // ---------------------------------------------------------------------
    Route::middleware('role:customer')->group(function () {
        // Tempat menaruh API Customer jika dibutuhkan nanti:
        Route::apiResource('bookings', ApiBookingController::class);
    });

    // Logout Session JWT
    Route::post('/logout', [AuthController::class, 'logout']);

});