@extends('layouts.accounting')

@section('title', 'Dashboard - Kế toán')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Chào mừng Kế toán!</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('accounting.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Kế toán</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="card bg-comman w-100">
            <div class="card-body">
                <div class="db-widgets d-flex justify-content-between align-items-center">
                    <div class="db-info">
                        <h6>Giáo viên</h6>
                        <h3>{{ \App\Models\Teacher::count() }}</h3>
                    </div>
                    <div class="db-icon">
                        <img src="{{ asset('assets/img/icons/dash-icon-01.svg') }}" alt="Dashboard Icon">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="card bg-comman w-100">
            <div class="card-body">
                <div class="db-widgets d-flex justify-content-between align-items-center">
                    <div class="db-info">
                        <h6>Đơn vị</h6>
                        <h3>{{ \App\Models\BudgetSpendingUnit::count() }}</h3>
                    </div>
                    <div class="db-icon">
                        <img src="{{ asset('assets/img/icons/dash-icon-02.svg') }}" alt="Dashboard Icon">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="card bg-comman w-100">
            <div class="card-body">
                <div class="db-widgets d-flex justify-content-between align-items-center">
                    <div class="db-info">
                        <h6>Chức danh</h6>
                        <h3>{{ \App\Models\JobTitle::count() }}</h3>
                    </div>
                    <div class="db-icon">
                        <img src="{{ asset('assets/img/icons/dash-icon-03.svg') }}" alt="Dashboard Icon">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="card bg-comman w-100">
            <div class="card-body">
                <div class="db-widgets d-flex justify-content-between align-items-center">
                    <div class="db-info">
                        <h6>Kỳ lương</h6>
                        <h3>{{ \App\Models\PayrollRun::count() }}</h3>
                    </div>
                    <div class="db-icon">
                        <img src="{{ asset('assets/img/icons/dash-icon-04.svg') }}" alt="Dashboard Icon">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

