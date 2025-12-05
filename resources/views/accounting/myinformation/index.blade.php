@extends('layouts.accounting')

@section('title', 'Thông tin cá nhân')

@php
function formatPayrollPeriod($period) {
    if (!$period) return '-';
    try {
        return \Carbon\Carbon::createFromFormat('Y-m', $period)->format('m-Y');
    } catch (\Exception $e) {
        return $period;
    }
}
@endphp

@section('content')
<!-- Error Notification Modal -->
@if (session('error'))
    <div class="modal fade notification-modal modal-error" id="errorNotificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-circle me-2"></i>Đã có lỗi xảy ra
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="mb-0">{{ session('error') }}</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Thông tin cá nhân</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('accounting.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Thông tin cá nhân</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="profile-header mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="profile-avatar">
                                <img class="rounded-circle" alt="User Image" 
                                     src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : asset('assets/img/cropped_circle_image.png') }}" 
                                     width="80" height="80">
                            </div>
                        </div>
                        <div class="col ms-md-n2">
                            <h4 class="user-name mb-0">{{ $teacher->fullname ?? 'Chưa cập nhật' }}</h4>
                            <h6 class="text-muted">{{ $teacher->jobTitle->jobtitlename ?? '-' }} - {{ $teacher->unit->unitname ?? '-' }}</h6>
                            <div class="user-info">
                                <span class="me-3">
                                    <i class="fas fa-id-card me-1"></i> Mã giáo viên: <strong>{{ $teacher->teacherid ?? '-' }}</strong>
                                </span>
                                @if($teacher->currentcoefficient)
                                    <span class="me-3">
                                        <i class="fas fa-percent me-1"></i> Hệ số lương: <strong>{{ number_format((float)$teacher->currentcoefficient, 2, '.', ',') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-menu">
            <ul class="nav nav-tabs nav-tabs-solid">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#teacher_tab">
                        <i class="fas fa-user me-1"></i> Thông tin giáo viên
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#payrollcomponent_tab">
                        <i class="fas fa-money-bill-wave me-1"></i> Thành phần lương
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#salaryincrease_tab">
                        <i class="fas fa-arrow-up me-1"></i> Quyết định tăng lương
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#payrollrun_tab">
                        <i class="fas fa-calendar-alt me-1"></i> Chi tiết bảng lương
                    </a>
                </li>
            </ul>
        </div>

        <div class="tab-content profile-tab-cont">
            <!-- Tab 1: Thông tin giáo viên -->
            <div class="tab-pane fade show active" id="teacher_tab">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Thông tin giáo viên</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="text-muted small">Mã giáo viên</label>
                                    <p class="mb-0 fw-bold">{{ $teacher->teacherid ?? '-' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="text-muted small">Họ và tên</label>
                                    <p class="mb-0">{{ $teacher->fullname ?? '-' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="text-muted small">Ngày sinh</label>
                                    <p class="mb-0">{{ $teacher->birthdate ? \Carbon\Carbon::parse($teacher->birthdate)->format('d/m/Y') : '-' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="text-muted small">Giới tính</label>
                                    <p class="mb-0">
                                        @if($teacher->gender == 'male')
                                            Nam
                                        @elseif($teacher->gender == 'female')
                                            Nữ
                                        @elseif($teacher->gender == 'other')
                                            Khác
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="text-muted small">Chức danh</label>
                                    <p class="mb-0">{{ $teacher->jobTitle->jobtitlename ?? '-' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="text-muted small">Đơn vị</label>
                                    <p class="mb-0">{{ $teacher->unit->unitname ?? '-' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="text-muted small">Hệ số lương hiện tại</label>
                                    <p class="mb-0 fw-bold text-primary">
                                        {{ $teacher->currentcoefficient ? number_format((float)$teacher->currentcoefficient, 2, '.', ',') : '-' }}
                                    </p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="text-muted small">Ngày bắt đầu làm việc</label>
                                    <p class="mb-0">{{ $teacher->startdate ? \Carbon\Carbon::parse($teacher->startdate)->format('d/m/Y') : '-' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="text-muted small">Trạng thái</label>
                                    <p class="mb-0">
                                        @if($teacher->status == 'active')
                                            <span class="badge bg-success">Đang làm việc</span>
                                        @elseif($teacher->status == 'suspended')
                                            <span class="badge bg-danger">Tạm đình chỉ</span>
                                        @elseif($teacher->status == 'onleave')
                                            <span class="badge bg-warning">Nghỉ phép</span>
                                        @elseif($teacher->status == 'contractended')
                                            <span class="badge bg-secondary">Hết hợp đồng</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $teacher->status }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Thành phần lương -->
            <div class="tab-pane fade" id="payrollcomponent_tab">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Thành phần lương</h5>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Mã cấu hình</th>
                                        <th>Thành phần lương</th>
                                        <th>Ngày hiệu lực</th>
                                        <th>Ngày hết hạn</th>
                                        <th>Hệ số điều chỉnh</th>
                                        <th>Phần trăm điều chỉnh</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teacherPayrollComponents as $component)
                                        <tr>
                                            <td>{{ $component->teachercomponentid }}</td>
                                            <td>{{ $component->component->componentname ?? '-' }}</td>
                                            <td>{{ $component->effectivedate ? $component->effectivedate->format('d/m/Y') : '-' }}</td>
                                            <td>
                                                @if($component->expirationdate)
                                                    {{ $component->expirationdate->format('d/m/Y') }}
                                                @else
                                                    <span class="badge bg-success">Đang hiệu lực</span>
                                                @endif
                                            </td>
                                            <td>{{ $component->adjustcustomcoefficient ? number_format((float)$component->adjustcustomcoefficient, 4, '.', ',') : '-' }}</td>
                                            <td>{{ $component->adjustcustompercentage ? number_format((float)$component->adjustcustompercentage, 4, '.', ',') . '%' : '-' }}</td>
                                            <td>{{ $component->note ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Chưa có dữ liệu thành phần lương.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Quyết định tăng lương -->
            <div class="tab-pane fade" id="salaryincrease_tab">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Quyết định tăng lương</h5>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Mã quyết định</th>
                                        <th>Ngày quyết định</th>
                                        <th>Hệ số cũ</th>
                                        <th>Hệ số mới</th>
                                        <th>Ngày áp dụng</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salaryIncreaseDecisions as $decision)
                                        <tr>
                                            <td>{{ $decision->decisionid }}</td>
                                            <td>{{ $decision->decisiondate ? $decision->decisiondate->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $decision->oldcoefficient ? number_format((float)$decision->oldcoefficient, 4, '.', ',') : '-' }}</td>
                                            <td>
                                                <strong class="text-success">
                                                    {{ $decision->newcoefficient ? number_format((float)$decision->newcoefficient, 4, '.', ',') : '-' }}
                                                </strong>
                                            </td>
                                            <td>{{ $decision->applydate ? $decision->applydate->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $decision->note ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Chưa có dữ liệu quyết định tăng lương.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Chi tiết bảng lương -->
            <div class="tab-pane fade" id="payrollrun_tab">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Chi tiết bảng lương</h5>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Bảng lương</th>
                                        <th>Kỳ lương</th>
                                        <th>Tổng thu nhập</th>
                                        <th>Tổng khoản trừ</th>
                                        <th>Tổng đơn vị đóng</th>
                                        <th>Thực lĩnh</th>
                                        <th>Tổng chi phí</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payrollRunDetails as $detail)
                                        <tr>
                                            <td>
                                                @if($detail->payrollRun)
                                                    {{ $detail->payrollRun->unit ? $detail->payrollRun->unit->unitname : '-' }} - {{ formatPayrollPeriod($detail->payrollRun->payrollperiod) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($detail->payrollRun)
                                                    {{ formatPayrollPeriod($detail->payrollRun->payrollperiod) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ number_format($detail->totalincome, 0, '.', ',') }} đ</td>
                                            <td>{{ number_format($detail->totalemployeedeductions, 0, '.', ',') }} đ</td>
                                            <td>{{ number_format($detail->totalemployercontributions, 0, '.', ',') }} đ</td>
                                            <td>
                                                <strong class="text-success">{{ number_format($detail->netpay, 0, '.', ',') }} đ</strong>
                                            </td>
                                            <td>{{ number_format($detail->totalcost, 0, '.', ',') }} đ</td>
                                            <td>{{ $detail->note ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">Chưa có dữ liệu chi tiết bảng lương.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .profile-header .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .profile-avatar img {
        border: 3px solid #2563eb;
        padding: 3px;
    }
    
    .user-name {
        font-size: 1.5rem;
        color: #1f2937;
    }
    
    .info-item {
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-item label {
        display: block;
        margin-bottom: 5px;
        font-size: 0.875rem;
    }
    
    .info-item p {
        font-size: 1rem;
        color: #374151;
    }
    
    .profile-menu {
        margin-bottom: 20px;
    }
    
    .nav-tabs-solid .nav-link {
        border: none;
        color: #6b7280;
        padding: 12px 24px;
    }
    
    .nav-tabs-solid .nav-link:hover {
        color: #2563eb;
    }
    
    .nav-tabs-solid .nav-link.active {
        color: #2563eb;
        background: linear-gradient(135deg, #2563eb, #1e40af);
        color: white;
        border-radius: 8px 8px 0 0;
    }
    
    .table th {
        background-color: #f9fafb;
        font-weight: 600;
        color: #374151;
        border-bottom: 2px solid #e5e7eb;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Hiển thị error modal nếu có
        @if(session('error'))
            $('#errorNotificationModal').modal('show');
        @endif
    });
</script>
@endpush

