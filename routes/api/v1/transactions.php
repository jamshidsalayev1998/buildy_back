<?php

use App\Http\Controllers\Api\V1\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('transactions', [TransactionController::class, 'index'])->middleware('permission:view transactions');
    Route::post('transactions', [TransactionController::class, 'store'])->middleware('permission:create transactions');
    Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->middleware('permission:view transactions');
    Route::put('transactions/{transaction}', [TransactionController::class, 'update'])->middleware('permission:edit transactions');
    Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])->middleware('permission:delete transactions');
});
