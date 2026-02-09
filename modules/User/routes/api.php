<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('users', UserController::class)->only(['index']);
});
