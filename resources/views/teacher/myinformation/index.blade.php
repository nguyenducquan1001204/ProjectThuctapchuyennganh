@extends('layouts.teacher')

@section('title', 'Thông tin cá nhân')

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
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Thông tin cá nhân</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <!-- Phần header với avatar và thông tin tổng quan -->
                <div class="row align-items-center mb-4 pb-4 border-bottom">
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

                <!-- Phần thông tin chi tiết -->
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
</div>
@endsection

@push('styles')
<style>
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

