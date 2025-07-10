<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Plan\PlanController;
use App\Http\Controllers\Api\V1\Company\CompanyController;

Route::prefix('v1')->group(function () {
    // Auth routes (sin restricciones)
    Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
    
    // Rutas protegidas que requieren autenticación
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('auth/logout-all', [AuthController::class, 'logoutAll'])->name('auth.logout-all');
        Route::post('auth/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
        Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');
        
        // User routes - Solo super-admin y admin
        Route::middleware('role:super-admin|admin')->group(function () {
            Route::apiResource('users', UserController::class);
            Route::patch('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
        });
        
        // Plan routes - Solo super-admin
        Route::middleware('role:super-admin')->group(function () {
            Route::apiResource('plans', PlanController::class);
            Route::patch('plans/{plan}/restore', [PlanController::class, 'restore'])->name('plans.restore');
        });
        
        // Company routes - Solo super-admin para listar todas las empresas
        Route::get('companies', [CompanyController::class, 'index'])->middleware('role:super-admin')->name('companies.index');
        Route::get('companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
        Route::put('companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
        Route::delete('companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
        Route::post('companies/{company}/change-plan', [CompanyController::class, 'changePlan'])->name('companies.change-plan');
    });

    // Crear empresa (sin autenticación)
    Route::post('companies', [CompanyController::class, 'store'])->name('companies.store');
});

