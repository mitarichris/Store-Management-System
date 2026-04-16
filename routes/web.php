<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesHistoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'role'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:super_admin,shop_owner')->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);

    // Sales + receipt
    Route::resource('sales', SaleController::class);
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');

    // Sales History
    Route::get('/history',        [SalesHistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{date}', [SalesHistoryController::class, 'byDate'])->name('history.byDate');

    // Accounting
    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::get('/',                 [AccountingController::class, 'index'])->name('index');
        Route::get('/report',           [AccountingController::class, 'report'])->name('report');
        Route::post('/opening-balance', [AccountingController::class, 'setOpeningBalance'])->name('opening-balance');
        Route::post('/inventory',       [AccountingController::class, 'recordInventory'])->name('inventory');
        Route::post('/revenue',         [AccountingController::class, 'recordRevenue'])->name('revenue');
        Route::delete('/{transaction}', [AccountingController::class, 'destroy'])->name('destroy');
    });
});
