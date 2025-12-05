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
use App\Http\Controllers\Admin\PayrollComponentUnitConfigController;
use App\Http\Controllers\Admin\BaseSalaryController;
use App\Http\Controllers\Admin\TeacherPayrollComponentController;
use App\Http\Controllers\Admin\SalaryIncreaseDecisionController;
use App\Http\Controllers\Admin\PayrollRunController;
use App\Http\Controllers\Admin\PayrollRunDetailController;
use App\Http\Controllers\Admin\PayrollRunDetailComponentController;
use App\Http\Controllers\Admin\AccountingMenuItemController;
use App\Http\Controllers\Admin\TeacherMenuItemController;
use App\Http\Controllers\Admin\ChatController;

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
    Route::get('/teacher/{teacher}/coefficient-history', [TeacherController::class, 'getCoefficientHistory'])->name('teacher.coefficient-history');
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

    // Routes quản lý cấu hình thành phần lương theo đơn vị
    Route::get('/payrollcomponentunitconfig', [PayrollComponentUnitConfigController::class, 'index'])->name('payrollcomponentunitconfig.index');
    Route::post('/payrollcomponentunitconfig', [PayrollComponentUnitConfigController::class, 'store'])->name('payrollcomponentunitconfig.store');
    Route::get('/payrollcomponentunitconfig/get-component/{id}', [PayrollComponentUnitConfigController::class, 'getComponent'])->name('payrollcomponentunitconfig.getComponent');
    Route::get('/payrollcomponentunitconfig/get-used-components/{unitId}', [PayrollComponentUnitConfigController::class, 'getUsedComponents'])->name('payrollcomponentunitconfig.getUsedComponents');
    Route::put('/payrollcomponentunitconfig/{payrollcomponentunitconfig}', [PayrollComponentUnitConfigController::class, 'update'])->name('payrollcomponentunitconfig.update');
    Route::delete('/payrollcomponentunitconfig/{payrollcomponentunitconfig}', [PayrollComponentUnitConfigController::class, 'destroy'])->name('payrollcomponentunitconfig.destroy');

    // Routes quản lý mức lương cơ bản
    Route::get('/basesalary', [BaseSalaryController::class, 'index'])->name('basesalary.index');
    Route::post('/basesalary', [BaseSalaryController::class, 'store'])->name('basesalary.store');
    Route::put('/basesalary/{basesalary}', [BaseSalaryController::class, 'update'])->name('basesalary.update');
    Route::post('/basesalary/{basesalary}/terminate', [BaseSalaryController::class, 'terminate'])->name('basesalary.terminate');
    Route::delete('/basesalary/{basesalary}', [BaseSalaryController::class, 'destroy'])->name('basesalary.destroy');

    // Routes quản lý cấu hình thành phần lương theo giáo viên
    Route::get('/teacherpayrollcomponent', [TeacherPayrollComponentController::class, 'index'])->name('teacherpayrollcomponent.index');
    Route::post('/teacherpayrollcomponent', [TeacherPayrollComponentController::class, 'store'])->name('teacherpayrollcomponent.store');
    Route::get('/teacherpayrollcomponent/get-component/{id}', [TeacherPayrollComponentController::class, 'getComponent'])->name('teacherpayrollcomponent.getComponent');
    Route::get('/teacherpayrollcomponent/get-used-components/{teacherId}', [TeacherPayrollComponentController::class, 'getUsedComponents'])->name('teacherpayrollcomponent.getUsedComponents');
    Route::get('/teacherpayrollcomponent/get-base-values', [TeacherPayrollComponentController::class, 'getBaseValues'])->name('teacherpayrollcomponent.getBaseValues');
    Route::put('/teacherpayrollcomponent/{teacherpayrollcomponent}', [TeacherPayrollComponentController::class, 'update'])->name('teacherpayrollcomponent.update');
    Route::delete('/teacherpayrollcomponent/{teacherpayrollcomponent}', [TeacherPayrollComponentController::class, 'destroy'])->name('teacherpayrollcomponent.destroy');

    // Routes quản lý quyết định nâng lương
    Route::get('/salaryincreasedecision', [SalaryIncreaseDecisionController::class, 'index'])->name('salaryincreasedecision.index');
    Route::post('/salaryincreasedecision', [SalaryIncreaseDecisionController::class, 'store'])->name('salaryincreasedecision.store');
    Route::get('/salaryincreasedecision/get-current-coefficient/{teacherId}', [SalaryIncreaseDecisionController::class, 'getCurrentCoefficient'])->name('salaryincreasedecision.getCurrentCoefficient');
    Route::put('/salaryincreasedecision/{salaryincreasedecision}', [SalaryIncreaseDecisionController::class, 'update'])->name('salaryincreasedecision.update');
    Route::delete('/salaryincreasedecision/{salaryincreasedecision}', [SalaryIncreaseDecisionController::class, 'destroy'])->name('salaryincreasedecision.destroy');

    // Routes quản lý bảng lương theo kỳ
    Route::get('/payrollrun', [PayrollRunController::class, 'index'])->name('payrollrun.index');
    Route::post('/payrollrun', [PayrollRunController::class, 'store'])->name('payrollrun.store');
    Route::get('/payrollrun/get-base-salaries/{unitId}', [PayrollRunController::class, 'getBaseSalariesByUnit'])->name('payrollrun.getBaseSalariesByUnit');
    Route::put('/payrollrun/{payrollrun}', [PayrollRunController::class, 'update'])->name('payrollrun.update');
    Route::delete('/payrollrun/{payrollrun}', [PayrollRunController::class, 'destroy'])->name('payrollrun.destroy');
    Route::get('/payrollrun/{payrollrun}/preview', [PayrollRunController::class, 'preview'])->name('payrollrun.preview');
    Route::post('/payrollrun/{payrollrun}/calculate', [PayrollRunController::class, 'calculate'])->name('payrollrun.calculate');

    // Routes quản lý chi tiết bảng lương từng giáo viên
    Route::get('/payrollrundetail', [PayrollRunDetailController::class, 'index'])->name('payrollrundetail.index');
    Route::post('/payrollrundetail', [PayrollRunDetailController::class, 'store'])->name('payrollrundetail.store');
    Route::put('/payrollrundetail/{payrollrundetail}', [PayrollRunDetailController::class, 'update'])->name('payrollrundetail.update');
    Route::delete('/payrollrundetail/{payrollrundetail}', [PayrollRunDetailController::class, 'destroy'])->name('payrollrundetail.destroy');
    Route::get('/payrollrundetail/{payrollrundetail}/calculation-details', [PayrollRunDetailController::class, 'getCalculationDetails'])->name('payrollrundetail.getCalculationDetails');
    Route::get('/payrollrundetail/export', [PayrollRunDetailController::class, 'export'])->name('payrollrundetail.export');

    // Routes quản lý chi tiết thành phần trong bảng lương
    Route::get('/payrollrundetailcomponent', [PayrollRunDetailComponentController::class, 'index'])->name('payrollrundetailcomponent.index');
    Route::post('/payrollrundetailcomponent', [PayrollRunDetailComponentController::class, 'store'])->name('payrollrundetailcomponent.store');
    Route::put('/payrollrundetailcomponent/{payrollrundetailcomponent}', [PayrollRunDetailComponentController::class, 'update'])->name('payrollrundetailcomponent.update');
    Route::delete('/payrollrundetailcomponent/{payrollrundetailcomponent}', [PayrollRunDetailComponentController::class, 'destroy'])->name('payrollrundetailcomponent.destroy');

    // Routes quản lý menu kế toán
    Route::get('/accountingmenuitem', [AccountingMenuItemController::class, 'index'])->name('accountingmenuitem.index');
    Route::post('/accountingmenuitem', [AccountingMenuItemController::class, 'store'])->name('accountingmenuitem.store');
    Route::put('/accountingmenuitem/{accountingmenuitem}', [AccountingMenuItemController::class, 'update'])->name('accountingmenuitem.update');
    Route::delete('/accountingmenuitem/{accountingmenuitem}', [AccountingMenuItemController::class, 'destroy'])->name('accountingmenuitem.destroy');

    // Routes quản lý menu giáo viên
    Route::get('/teachermenuitem', [TeacherMenuItemController::class, 'index'])->name('teachermenuitem.index');
    Route::post('/teachermenuitem', [TeacherMenuItemController::class, 'store'])->name('teachermenuitem.store');
    Route::put('/teachermenuitem/{teachermenuitem}', [TeacherMenuItemController::class, 'update'])->name('teachermenuitem.update');
    Route::delete('/teachermenuitem/{teachermenuitem}', [TeacherMenuItemController::class, 'destroy'])->name('teachermenuitem.destroy');

    // Routes chatbot hỗ trợ
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');

    // Thêm các routes admin khác ở đây
});

