<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PayableAccountController;
use App\Http\Controllers\Api\PayableAccountPaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::apiResource('payable-accounts', PayableAccountController::class);
    Route::post('payable-accounts/{payable_account}/payments', [PayableAccountPaymentController::class, 'store'])
        ->name('payable-accounts.payments.store');
});
