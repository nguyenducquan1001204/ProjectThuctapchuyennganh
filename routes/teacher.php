<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Teacher\MyInformationController;
use App\Http\Controllers\Teacher\TeacherPayrollComponentController;
use App\Http\Controllers\Teacher\SalaryIncreaseDecisionController;
use App\Http\Controllers\Teacher\PayrollRunDetailController;

/*
|--------------------------------------------------------------------------
| Teacher Routes
|--------------------------------------------------------------------------
|
| Routes dành cho khu vực Giáo viên
| Prefix: /teacher
|
*/

Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher', 'prevent-back'])->group(function () {
    Route::get('/', [TeacherController::class, 'index'])->name('dashboard');
    
    // Route xem thông tin cá nhân
    Route::get('/myinformation', [MyInformationController::class, 'index'])->name('myinformation.index');
    
    // Route xem thành phần lương của mình
    Route::get('/teacherpayrollcomponent', [TeacherPayrollComponentController::class, 'index'])->name('teacherpayrollcomponent.index');
    
    // Route xem quyết định nâng lương của mình
    Route::get('/salaryincreasedecision', [SalaryIncreaseDecisionController::class, 'index'])->name('salaryincreasedecision.index');
    
    // Route xem chi tiết bảng lương của mình
    Route::get('/payrollrundetail', [PayrollRunDetailController::class, 'index'])->name('payrollrundetail.index');
    Route::get('/payrollrundetail/{payrollrundetail}/calculation-details', [PayrollRunDetailController::class, 'getCalculationDetails'])->name('payrollrundetail.getCalculationDetails');
    
    // Thêm các routes teacher khác ở đây
});

