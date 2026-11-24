<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is running',
    ]);
});

Route::prefix('v1')->group(function () {
    
    // Public Auth Routes (Rate limited against SMS spam and brute-force attacks)
    Route::prefix('auth')->group(function () {
        Route::post('send-otp', [\App\Http\Controllers\Api\V1\AuthController::class, 'sendOtp'])->middleware('throttle:5,1');
        Route::post('verify-otp', [\App\Http\Controllers\Api\V1\AuthController::class, 'verifyOtp'])->middleware('throttle:10,1');
    });

    // Authenticated API Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
            Route::post('logout-all', [\App\Http\Controllers\Api\V1\AuthController::class, 'logoutAll']);
            Route::get('me', [\App\Http\Controllers\Api\V1\AuthController::class, 'me']);
        });
    });
});
