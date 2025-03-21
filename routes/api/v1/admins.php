<?php

use App\Http\Controllers\Api\V1\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('admins')->group(function () {
        // Get all admins (with pagination)
        Route::get('/', [AdminController::class, 'index'])
            ->middleware('permission:view admins')
            ->name('admins.index');

        // Get single admin
        Route::get('/{admin}', [AdminController::class, 'show'])
            ->middleware('permission:view admins')
            ->name('admins.show');

        // Create new admin
        Route::post('/', [AdminController::class, 'store'])
            ->middleware('permission:create admins')
            ->name('admins.store');

        // Update admin
        Route::patch('/{admin}', [AdminController::class, 'update'])
            ->middleware('permission:edit admins')
            ->name('admins.update');

        // Delete admin
        Route::delete('/{admin}', [AdminController::class, 'destroy'])
            ->middleware('permission:delete admins')
            ->name('admins.destroy');
    });
});
