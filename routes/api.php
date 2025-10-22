<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Interfaces\Http\Controllers\Auth\AuthController;
use App\Interfaces\Http\Controllers\RegistrationTokenController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/registration-tokens', [RegistrationTokenController::class, 'index']);
    Route::post('/registration-tokens', [RegistrationTokenController::class, 'store']);
    Route::delete('/registration-tokens/{id}', [RegistrationTokenController::class, 'destroy']);
});
