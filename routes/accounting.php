<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\AccountingController;

/*
|--------------------------------------------------------------------------
| Accounting Routes
|--------------------------------------------------------------------------
|
| Routes dành cho khu vực Kế toán
| Prefix: /accounting
|
*/

Route::prefix('accounting')->name('accounting.')->middleware(['auth', 'role:accounting', 'prevent-back'])->group(function () {
    Route::get('/', [AccountingController::class, 'index'])->name('dashboard');
    // Thêm các routes accounting khác ở đây
});

