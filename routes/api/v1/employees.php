<?php

use App\Http\Controllers\Api\V1\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('employees', [EmployeeController::class, 'index'])->middleware('permission:view employees');
    Route::post('employees', [EmployeeController::class, 'store'])->middleware('permission:create employees');
    Route::get('employees/{employee}', [EmployeeController::class, 'show'])->middleware('permission:view employees');
    Route::post('employees/{employee}', [EmployeeController::class, 'update'])->middleware('permission:edit employees');
    Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->middleware('permission:delete employees');
});
