<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\AdminMenuItem;
use App\Models\AccountingMenuItem;
use App\Models\TeacherMenuItem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Chia sẻ menu items cho layout admin
        View::composer('layouts.admin', function ($view) {
            $view->with('adminMenuItems', AdminMenuItem::getMenuTree());
        });

        // Chia sẻ menu items cho layout accounting
        View::composer('layouts.accounting', function ($view) {
            $view->with('accountingMenuItems', AccountingMenuItem::getMenuTree());
        });

        // Chia sẻ menu items cho layout teacher
        View::composer('layouts.teacher', function ($view) {
            $view->with('teacherMenuItems', TeacherMenuItem::getMenuTree());
        });
    }
}
