<?php

use App\Http\Controllers\Api\V1\BalanceTransferController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('balance-transfer')->group(function () {
    Route::post('/company-to-employee', [BalanceTransferController::class, 'companyToEmployee'])->middleware('permission:balance-transfer');
    Route::post('/employee-to-company', [BalanceTransferController::class, 'employeeToCompany'])->middleware('permission:balance-transfer');
    Route::post('/company-to-admin', [BalanceTransferController::class, 'companyToAdmin'])->middleware('permission:balance-transfer');
    Route::post('/admin-to-company', [BalanceTransferController::class, 'adminToCompany'])->middleware('permission:balance-transfer');
});
