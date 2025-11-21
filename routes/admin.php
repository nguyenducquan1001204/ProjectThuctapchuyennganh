<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\JobTitleController;
use App\Http\Controllers\Admin\BudgetSpendingUnitController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TeacherJobTitleHistoryController;
use App\Http\Controllers\Admin\EmploymentContractController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SystemUserController;
use App\Http\Controllers\Admin\PayrollComponentController;
use App\Http\Controllers\Admin\PayrollComponentConfigController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes dành cho khu vực Admin
| Prefix: /admin
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin', 'prevent-back'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    
    // Routes quản lý chức danh
    Route::get('/jobtitle', [JobTitleController::class, 'index'])->name('jobtitle.index');
    Route::post('/jobtitle', [JobTitleController::class, 'store'])->name('jobtitle.store');
    Route::put('/jobtitle/{jobtitle}', [JobTitleController::class, 'update'])->name('jobtitle.update');
    Route::delete('/jobtitle/{jobtitle}', [JobTitleController::class, 'destroy'])->name('jobtitle.destroy');
    
    // Routes quản lý đơn vị
    Route::get('/budgetspendingunit', [BudgetSpendingUnitController::class, 'index'])->name('budgetspendingunit.index');
    Route::post('/budgetspendingunit', [BudgetSpendingUnitController::class, 'store'])->name('budgetspendingunit.store');
    Route::put('/budgetspendingunit/{budgetspendingunit}', [BudgetSpendingUnitController::class, 'update'])->name('budgetspendingunit.update');
    Route::delete('/budgetspendingunit/{budgetspendingunit}', [BudgetSpendingUnitController::class, 'destroy'])->name('budgetspendingunit.destroy');
    
    // Routes quản lý giáo viên
    Route::get('/teacher', [TeacherController::class, 'index'])->name('teacher.index');
    Route::post('/teacher', [TeacherController::class, 'store'])->name('teacher.store');
    Route::put('/teacher/{teacher}', [TeacherController::class, 'update'])->name('teacher.update');
    Route::delete('/teacher/{teacher}', [TeacherController::class, 'destroy'])->name('teacher.destroy');
    
    // Routes quản lý lịch sử chức danh giáo viên
    Route::get('/teacherjobtitlehistory', [TeacherJobTitleHistoryController::class, 'index'])->name('teacherjobtitlehistory.index');
    Route::post('/teacherjobtitlehistory', [TeacherJobTitleHistoryController::class, 'store'])->name('teacherjobtitlehistory.store');
    Route::put('/teacherjobtitlehistory/{teacherjobtitlehistory}', [TeacherJobTitleHistoryController::class, 'update'])->name('teacherjobtitlehistory.update');
    Route::delete('/teacherjobtitlehistory/{teacherjobtitlehistory}', [TeacherJobTitleHistoryController::class, 'destroy'])->name('teacherjobtitlehistory.destroy');
    
    // Routes quản lý hợp đồng lao động
    Route::get('/employmentcontract', [EmploymentContractController::class, 'index'])->name('employmentcontract.index');
    Route::post('/employmentcontract', [EmploymentContractController::class, 'store'])->name('employmentcontract.store');
    Route::put('/employmentcontract/{employmentcontract}', [EmploymentContractController::class, 'update'])->name('employmentcontract.update');
    Route::delete('/employmentcontract/{employmentcontract}', [EmploymentContractController::class, 'destroy'])->name('employmentcontract.destroy');
    
    // Routes quản lý vai trò
    Route::get('/role', [RoleController::class, 'index'])->name('role.index');
    Route::post('/role', [RoleController::class, 'store'])->name('role.store');
    Route::put('/role/{role}', [RoleController::class, 'update'])->name('role.update');
    Route::delete('/role/{role}', [RoleController::class, 'destroy'])->name('role.destroy');

    // Routes quản lý người dùng hệ thống
    Route::get('/systemuser', [SystemUserController::class, 'index'])->name('systemuser.index');
    Route::post('/systemuser', [SystemUserController::class, 'store'])->name('systemuser.store');
    Route::post('/systemuser/check-email', [SystemUserController::class, 'checkEmail'])->name('systemuser.checkEmail');
    Route::put('/systemuser/{systemuser}', [SystemUserController::class, 'update'])->name('systemuser.update');
    Route::delete('/systemuser/{systemuser}', [SystemUserController::class, 'destroy'])->name('systemuser.destroy');

    // Routes quản lý thành phần lương
    Route::get('/payrollcomponent', [PayrollComponentController::class, 'index'])->name('payrollcomponent.index');
    Route::post('/payrollcomponent', [PayrollComponentController::class, 'store'])->name('payrollcomponent.store');
    Route::put('/payrollcomponent/{payrollcomponent}', [PayrollComponentController::class, 'update'])->name('payrollcomponent.update');
    Route::delete('/payrollcomponent/{payrollcomponent}', [PayrollComponentController::class, 'destroy'])->name('payrollcomponent.destroy');

    // Routes quản lý cấu hình thành phần lương
    Route::get('/payrollcomponentconfig', [PayrollComponentConfigController::class, 'index'])->name('payrollcomponentconfig.index');
    Route::post('/payrollcomponentconfig', [PayrollComponentConfigController::class, 'store'])->name('payrollcomponentconfig.store');
    Route::get('/payrollcomponentconfig/get-component/{id}', [PayrollComponentConfigController::class, 'getComponent'])->name('payrollcomponentconfig.getComponent');
    Route::put('/payrollcomponentconfig/{payrollcomponentconfig}', [PayrollComponentConfigController::class, 'update'])->name('payrollcomponentconfig.update');
    Route::delete('/payrollcomponentconfig/{payrollcomponentconfig}', [PayrollComponentConfigController::class, 'destroy'])->name('payrollcomponentconfig.destroy');

    // Thêm các routes admin khác ở đây
});

