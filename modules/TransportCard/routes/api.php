<?php

use Illuminate\Support\Facades\Route;
use Modules\TransportCard\Http\Controllers\TransportCardController;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('transport-card/balance', [TransportCardController::class, 'balance'])
        ->name('transport-card.balance');
    Route::post('transport-card/refresh', [TransportCardController::class, 'refresh'])
        ->name('transport-card.refresh');
});
