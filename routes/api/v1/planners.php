<?php

use App\Http\Controllers\Api\V1\PlannerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('planners', [PlannerController::class, 'index'])->middleware('permission:view planners');
    Route::post('planners', [PlannerController::class, 'store'])->middleware('permission:create planners');
    Route::get('planners/{planner}', [PlannerController::class, 'show'])->middleware('permission:view planners');
    Route::put('planners/{planner}', [PlannerController::class, 'update'])->middleware('permission:edit planners');
    Route::delete('planners/{planner}', [PlannerController::class, 'destroy'])->middleware('permission:delete planners');
});
