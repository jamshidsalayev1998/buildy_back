<?php

use App\Http\Controllers\Api\V1\CompanyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/companies', [CompanyController::class, 'index'])->middleware('permission:view companies');
    Route::post('/companies', [CompanyController::class, 'store'])->middleware('permission:create companies');
    Route::get('/companies/{company}', [CompanyController::class, 'show'])->middleware('permission:view companies');
    Route::put('/companies/{company}', [CompanyController::class, 'update'])->middleware('permission:edit companies');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->middleware('permission:delete companies');
    Route::post('/companies/{id}/restore', [CompanyController::class, 'restore'])->middleware('permission:restore companies');
});
