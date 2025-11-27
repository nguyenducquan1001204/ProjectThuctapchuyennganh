<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;

class AccountingController extends Controller
{
    /**
     * Trang chủ khu vực Kế toán
     */
    public function index()
    {
        return view('accounting.dashboard');
    }
}


