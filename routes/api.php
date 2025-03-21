<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// V1 API Routes
Route::prefix('v1')->group(function () {
    require __DIR__ . '/api/v1/auth.php';
    require __DIR__ . '/api/v1/admins.php';
    require __DIR__ . '/api/v1/users.php';
    require __DIR__ . '/api/v1/companies.php';
    require __DIR__ . '/api/v1/managers.php';
    require __DIR__ . '/api/v1/planners.php';
    require __DIR__ . '/api/v1/transaction-categories.php';
    require __DIR__ . '/api/v1/transactions.php';
});


