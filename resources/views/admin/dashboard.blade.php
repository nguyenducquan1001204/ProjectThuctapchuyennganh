@extends('layouts.admin')

@section('title', 'Dashboard - Admin')

@push('styles')
<style>
    /* Reset và cải thiện spacing */
    .page-content {
        padding: 1.5rem;
    }
    
    /* Welcome Section - Cải thiện */
    .welcome-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 2rem 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
    }
    .welcome-title {
        font-size: 2.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .welcome-subtitle {
        font-size: 1.25rem;
        opacity: 0.95;
        margin: 0;
    }
    .welcome-date {
        text-align: right;
    }
    .welcome-date-label {
        font-size: 1rem;
        opacity: 0.8;
        margin-bottom: 0.25rem;
    }
    .welcome-date-value {
        font-size: 1.875rem;
        font-weight: 700;
    }

    /* Stat Cards - Cải thiện bố cục */
    .stat-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
        height: 100%;
        background: white;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .stat-card-body {
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .stat-content {
        flex: 1;
        min-width: 0;
    }
    .stat-label {
        font-size: 1rem;
        color: #6b7280;
        margin: 0 0 0.5rem 0;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 0.75rem 0;
        line-height: 1.2;
    }
    .stat-footer {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
    }
    .stat-icon-wrapper {
        flex-shrink: 0;
        margin-left: 1rem;
    }
    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: white;
    }
    
    /* Icon colors */
    .stat-icon.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-icon.success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .stat-icon.warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-icon.info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stat-icon.danger { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .stat-icon.purple { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
    .stat-icon.teal { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }
    .stat-icon.orange { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); }

    /* Badge styles */
    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.375rem 0.625rem;
        border-radius: 6px;
        font-size: 0.8125rem;
        font-weight: 600;
    }
    .badge-success { background-color: #d1fae5; color: #065f46; }
    .badge-warning { background-color: #fef3c7; color: #92400e; }
    .badge-info { background-color: #dbeafe; color: #1e40af; }

    /* Chart Cards */
    .chart-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        background: white;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .chart-header {
        padding: 1.5rem 1.5rem 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        flex-shrink: 0;
    }
    .chart-title {
        font-size: 1.375rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .chart-title i {
        color: #667eea;
    }
    .chart-body {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 300px;
    }
    .chart-body canvas {
        max-height: 100%;
    }

    /* Table Cards */
    .table-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        background: white;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .table-header {
        padding: 1.5rem 1.5rem 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }
    .table-title {
        font-size: 1.375rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .table-title i {
        color: #667eea;
    }
    .table-body {
        padding: 0;
        flex: 1;
        overflow: hidden;
    }
    .table-responsive {
        max-height: 400px;
        overflow-y: auto;
    }
    .table {
        margin: 0;
    }
    .table thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .table thead th {
        background-color: #f9fafb;
        color: #374151;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e5e7eb;
        padding: 1rem;
        white-space: nowrap;
    }
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
        font-size: 1rem;
    }
    .table tbody tr:hover {
        background-color: #f9fafb;
    }
    .table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Info Cards */
    .info-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        background: white;
        height: 100%;
    }
    .info-card-header {
        padding: 1.5rem 1.5rem 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
    }
    .info-card-title {
        font-size: 1.375rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .info-card-title i {
        color: #667eea;
    }
    .info-card-body {
        padding: 1.5rem;
    }
    .info-stat-box {
        text-align: center;
        padding: 1.25rem;
        border-radius: 12px;
        transition: transform 0.2s;
    }
    .info-stat-box:hover {
        transform: scale(1.02);
    }
    .info-stat-value {
        font-size: 2.25rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        line-height: 1;
    }
    .info-stat-label {
        font-size: 1rem;
        color: #6b7280;
        margin: 0;
        font-weight: 500;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #9ca3af;
    }
    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.4;
    }
    .empty-state p {
        margin: 0;
        font-size: 1rem;
    }

    /* Responsive improvements */
    @media (max-width: 1200px) {
        .stat-value {
            font-size: 2rem;
        }
        .stat-icon {
            width: 56px;
            height: 56px;
            font-size: 24px;
        }
    }
    
    @media (max-width: 768px) {
        .welcome-section {
            padding: 1.5rem;
        }
        .welcome-title {
            font-size: 1.75rem;
        }
        .welcome-subtitle {
            font-size: 1rem;
        }
        .stat-card-body {
            padding: 1.25rem;
        }
        .stat-value {
            font-size: 1.875rem;
        }
        .stat-label {
            font-size: 0.9375rem;
        }
        .chart-body {
            min-height: 250px;
            padding: 1rem;
        }
        .chart-title, .table-title, .info-card-title {
            font-size: 1.125rem;
        }
    }

    /* Button improvements */
    .btn-view-all {
        font-size: 1rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 500;
    }

    /* Grid improvements */
    .stats-row {
        margin-bottom: 1.5rem;
    }
    .stats-row .col-xl-3,
    .stats-row .col-sm-6 {
        margin-bottom: 1rem;
    }
    .content-row {
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="page-content">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="welcome-title">Chào mừng trở lại, Admin!</h2>
                <p class="welcome-subtitle">Hệ thống quản lý lương giáo viên - Tổng quan thống kê và báo cáo</p>
            </div>
            <div class="col-md-4 welcome-date">
                <div class="welcome-date-label">Hôm nay</div>
                <div class="welcome-date-value">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row 1 -->
    <div class="row stats-row">
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card stat-card">
                <div class="stat-card-body">
                    <div class="stat-content">
                        <p class="stat-label">Tổng Giáo viên</p>
                        <h3 class="stat-value">{{ number_format($stats['total_teachers']) }}</h3>
                        <div class="stat-footer">
                            <i class="fas fa-user-check text-success"></i>
                            <span>{{ number_format($stats['active_teachers']) }} đang hoạt động</span>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper">
                        <div class="stat-icon primary">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card stat-card">
                <div class="stat-card-body">
                    <div class="stat-content">
                        <p class="stat-label">Đơn vị</p>
                        <h3 class="stat-value">{{ number_format($stats['total_units']) }}</h3>
                        <div class="stat-footer">
                            <i class="fas fa-building text-info"></i>
                            <span>Đơn vị chi tiêu</span>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper">
                        <div class="stat-icon success">
                            <i class="fas fa-sitemap"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card stat-card">
                <div class="stat-card-body">
                    <div class="stat-content">
                        <p class="stat-label">Kỳ Lương</p>
                        <h3 class="stat-value">{{ number_format($stats['total_payroll_runs']) }}</h3>
                        <div class="stat-footer">
                            <span class="stat-badge badge-success">{{ $stats['approved_payroll_runs'] }} đã chốt</span>
                            <span class="stat-badge badge-warning">{{ $stats['draft_payroll_runs'] }} nháp</span>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper">
                        <div class="stat-icon warning">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
            </div>
        </div>
    </div>
</div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card stat-card">
                <div class="stat-card-body">
                    <div class="stat-content">
                        <p class="stat-label">Người dùng</p>
                        <h3 class="stat-value">{{ number_format($stats['total_users']) }}</h3>
                        <div class="stat-footer">
                            <i class="fas fa-users text-info"></i>
                            <span>Hệ thống</span>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper">
                        <div class="stat-icon info">
                            <i class="fas fa-user-friends"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row 2 -->
    <div class="row stats-row">
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card stat-card">
                <div class="stat-card-body">
                    <div class="stat-content">
                        <p class="stat-label">Tổng Thu Nhập</p>
                        <h3 class="stat-value">{{ number_format($payrollStats['total_income'] / 1000000, 1) }}M</h3>
                        <div class="stat-footer">
                            <i class="fas fa-arrow-up text-success"></i>
                            <span>VNĐ</span>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper">
                        <div class="stat-icon purple">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card stat-card">
                <div class="stat-card-body">
                    <div class="stat-content">
                        <p class="stat-label">Lương Thực Lĩnh</p>
                        <h3 class="stat-value">{{ number_format($payrollStats['total_net_pay'] / 1000000, 1) }}M</h3>
                        <div class="stat-footer">
                            <i class="fas fa-hand-holding-usd text-success"></i>
                            <span>VNĐ</span>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper">
                        <div class="stat-icon teal">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card stat-card">
                <div class="stat-card-body">
                    <div class="stat-content">
                        <p class="stat-label">Tổng Chi Phí</p>
                        <h3 class="stat-value">{{ number_format($payrollStats['total_cost'] / 1000000, 1) }}M</h3>
                        <div class="stat-footer">
                            <i class="fas fa-chart-line text-warning"></i>
                            <span>VNĐ</span>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper">
                        <div class="stat-icon orange">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card stat-card">
                <div class="stat-card-body">
                    <div class="stat-content">
                        <p class="stat-label">Khấu Trừ</p>
                        <h3 class="stat-value">{{ number_format($payrollStats['total_deductions'] / 1000000, 2) }}M</h3>
                        <div class="stat-footer">
                            <i class="fas fa-minus-circle text-danger"></i>
                            <span>VNĐ</span>
                        </div>
                    </div>
                    <div class="stat-icon-wrapper">
                        <div class="stat-icon danger">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row content-row">
        <div class="col-xl-8 col-lg-7">
            <div class="card chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="fas fa-chart-line"></i>
                        Thống kê Bảng lương theo tháng (6 tháng gần nhất)
                    </h5>
                </div>
                <div class="chart-body">
                    <canvas id="monthlyChart" data-stats="{{ json_encode($monthlyStats) }}"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="fas fa-chart-pie"></i>
                        Giáo viên theo Đơn vị
                    </h5>
                </div>
                <div class="chart-body">
                    <canvas id="unitChart" data-stats="{{ json_encode($teachersByUnit) }}"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row content-row">
        <div class="col-xl-8 col-lg-7">
            <div class="card table-card">
                <div class="table-header">
                    <h5 class="table-title">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Bảng lương gần đây
                    </h5>
                    <a href="{{ route('admin.payrollrun.index') }}" class="btn btn-sm btn-primary btn-view-all">
                        Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="table-body">
                    @if($recentPayrollRuns->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Kỳ lương</th>
                                    <th>Đơn vị</th>
                                    <th>Mức lương cơ bản</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPayrollRuns as $run)
                                <tr>
                                    <td><strong>{{ $run->payrollperiod }}</strong></td>
                                    <td>{{ $run->unit ? $run->unit->unitname : 'N/A' }}</td>
                                    <td>{{ $run->baseSalary ? number_format($run->baseSalary->basesalaryamount, 0) . ' VNĐ' : 'N/A' }}</td>
                                    <td>
                                        @if($run->status == 'approved')
                                            <span class="stat-badge badge-success">Đã chốt</span>
                                        @else
                                            <span class="stat-badge badge-warning">Nháp</span>
                                        @endif
                                    </td>
                                    <td>{{ $run->createdat ? \Carbon\Carbon::parse($run->createdat)->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <p>Chưa có bảng lương nào</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card table-card">
                <div class="table-header">
                    <h5 class="table-title">
                        <i class="fas fa-user-plus"></i>
                        Giáo viên mới
                    </h5>
                    <a href="{{ route('admin.teacher.index') }}" class="btn btn-sm btn-primary btn-view-all">
                        Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="table-body">
                    @if($recentTeachers->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th>Đơn vị</th>
                                    <th>Ngày bắt đầu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTeachers as $teacher)
                                <tr>
                                    <td><strong>{{ $teacher->fullname }}</strong></td>
                                    <td>{{ $teacher->unit ? $teacher->unit->unitname : 'N/A' }}</td>
                                    <td>{{ $teacher->startdate ? \Carbon\Carbon::parse($teacher->startdate)->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <p>Chưa có giáo viên nào</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards Row -->
    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card info-card">
                <div class="info-card-header">
                    <h5 class="info-card-title">
                        <i class="fas fa-briefcase"></i>
                        Thống kê Hợp đồng
                    </h5>
                </div>
                <div class="info-card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="info-stat-box" style="background: #f0f9ff;">
                                <h3 class="info-stat-value" style="color: #0369a1;">{{ $contractStats['active_contracts'] }}</h3>
                                <p class="info-stat-label">Hợp đồng đang hoạt động</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-stat-box" style="background: #fef3c7;">
                                <h3 class="info-stat-value" style="color: #92400e;">{{ $contractStats['expiring_soon'] }}</h3>
                                <p class="info-stat-label">Sắp hết hạn (3 tháng)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card info-card">
                <div class="info-card-header">
                    <h5 class="info-card-title">
                        <i class="fas fa-tags"></i>
                        Thống kê khác
                    </h5>
                </div>
                <div class="info-card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="info-stat-box" style="background: #f3e8ff;">
                                <h3 class="info-stat-value" style="color: #7c3aed;">{{ $stats['total_job_titles'] }}</h3>
                                <p class="info-stat-label">Chức danh</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-stat-box" style="background: #d1fae5;">
                                <h3 class="info-stat-value" style="color: #065f46;">{{ $stats['total_payroll_components'] }}</h3>
                                <p class="info-stat-label">Thành phần lương</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Monthly Statistics Chart
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        const monthlyData = JSON.parse(monthlyCtx.getAttribute('data-stats') || '[]');
        if (monthlyData && monthlyData.length > 0) {
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(item => item.month),
                    datasets: [{
                        label: 'Số kỳ lương',
                        data: monthlyData.map(item => item.payroll_runs),
                        borderColor: 'rgb(99, 102, 241)',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y'
                    }, {
                        label: 'Tổng lương thực lĩnh (triệu VNĐ)',
                        data: monthlyData.map(item => Math.round(item.total_net_pay / 1000000)),
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    }
                }
            });
        } else {
            monthlyCtx.parentElement.innerHTML = '<div class="empty-state"><div class="empty-state-icon"><i class="fas fa-chart-line"></i></div><p>Chưa có dữ liệu</p></div>';
        }
    }

    // Teachers by Unit Chart
    const unitCtx = document.getElementById('unitChart');
    if (unitCtx) {
        const unitData = JSON.parse(unitCtx.getAttribute('data-stats') || '[]');
        if (unitData && unitData.length > 0) {
            new Chart(unitCtx, {
                type: 'doughnut',
                data: {
                    labels: unitData.map(item => item.unit_name),
                    datasets: [{
                        data: unitData.map(item => item.count),
                        backgroundColor: [
                            'rgba(99, 102, 241, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(20, 184, 166, 0.8)',
                            'rgba(249, 115, 22, 0.8)',
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        } else {
            unitCtx.parentElement.innerHTML = '<div class="empty-state"><div class="empty-state-icon"><i class="fas fa-chart-pie"></i></div><p>Chưa có dữ liệu</p></div>';
        }
    }
</script>
@endpush
