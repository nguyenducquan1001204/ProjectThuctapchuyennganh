@extends('layouts.teacher')

@section('title', 'Dashboard - Giáo viên')

@section('content')
@php
    $user = auth()->user();
    $teacher = $user && $user->teacherid ? \App\Models\Teacher::with(['jobTitle', 'unit'])->find($user->teacherid) : null;
@endphp

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Chào mừng Giáo viên!</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Giáo viên</li>
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
                        <h6>Thành phần lương</h6>
                        <h3>
                            @if($teacher)
                                {{ \App\Models\TeacherPayrollComponent::where('teacherid', $teacher->teacherid)->count() }}
                            @else
                                0
                            @endif
                        </h3>
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
                        <h6>Quyết định tăng lương</h6>
                        <h3>
                            @if($teacher)
                                {{ \App\Models\SalaryIncreaseDecision::where('teacherid', $teacher->teacherid)->count() }}
                            @else
                                0
                            @endif
                        </h3>
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
                        <h6>Kỳ lương</h6>
                        <h3>
                            @if($teacher)
                                {{ \App\Models\PayrollRunDetail::where('teacherid', $teacher->teacherid)->select('payrollrunid')->distinct()->count() }}
                            @else
                                0
                            @endif
                        </h3>
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
                        <h6>Chi tiết bảng lương</h6>
                        <h3>
                            @if($teacher)
                                {{ \App\Models\PayrollRunDetail::where('teacherid', $teacher->teacherid)->count() }}
                            @else
                                0
                            @endif
                        </h3>
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

