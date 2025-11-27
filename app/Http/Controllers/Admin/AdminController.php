<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminMenuItem;

class AdminController extends Controller
{
    /**
     * Trang chủ khu vực Admin
     */
    public function index()
    {
        return view('admin.dashboard');
    }
}

