<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RegistrationTokenController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::middleware('permission:users.manage')->group(function () {
        Route::get('/registration-tokens', [RegistrationTokenController::class, 'index']);
        Route::post('/registration-tokens', [RegistrationTokenController::class, 'store']);
        Route::delete('/registration-tokens/{registrationToken}', [RegistrationTokenController::class, 'destroy']);
        // Route::put('/users/{user}', [UserController::class, 'update']); // Assuming an update method for users
    });

    Route::middleware('permission:roles.manage')->group(function () {
        // Roles
        Route::get('/roles', [RoleController::class, 'index']);
        Route::post('/roles', [RoleController::class, 'store']);
        Route::get('/roles/{role}', [RoleController::class, 'show']);
        Route::put('/roles/{role}', [RoleController::class, 'update']);
        Route::delete('/roles/{role}', [RoleController::class, 'destroy']);

        // Permissions
        Route::get('/permissions', [PermissionController::class, 'index']);

        // User Roles
        Route::get('/users/{user}/roles', [UserController::class, 'getRoles']);
        Route::put('/users/{user}/roles', [UserController::class, 'updateRoles']);
    });

    Route::middleware('permission:users.view')->group(function () {
        // Users (only view for now, update will be users.manage)
        // Route::get('/users', [UserController::class, 'index']); // Assuming an index method for users
    });

    Route::middleware('permission:templates.manage')->group(function () {
        // Email Templates
        Route::get('/templates', [EmailTemplateController::class, 'index']);
        Route::post('/templates', [EmailTemplateController::class, 'store']);
        Route::get('/templates/{emailTemplate}', [EmailTemplateController::class, 'show']);
        Route::put('/templates/{emailTemplate}', [EmailTemplateController::class, 'update']);
        Route::delete('/templates/{emailTemplate}', [EmailTemplateController::class, 'destroy']);
    });

    // Forms
    Route::apiResource('forms', FormController::class);
});
