<?php

use App\Http\Controllers\Api\V1\ManagerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('managers', [ManagerController::class, 'index'])->middleware('permission:view managers');
    Route::post('managers', [ManagerController::class, 'store'])->middleware('permission:create managers');
    Route::get('managers/{manager}', [ManagerController::class, 'show'])->middleware('permission:view managers');
    Route::patch('managers/{manager}', [ManagerController::class, 'update'])->middleware('permission:edit managers');
    Route::delete('managers/{manager}', [ManagerController::class, 'destroy'])->middleware('permission:delete managers');
});
