@extends('layouts.accounting')

@section('title', 'Quản lý bảng lương theo tháng')

@section('content')
<!-- Success Notification Modal -->
@if (session('success'))
    <div class="modal fade notification-modal modal-success" id="successNotificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i>Thành công
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                    </div>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                        <i class="fas fa-check me-1"></i>Đồng ý
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Error Notification Modal -->
@if ($errors->any() || session('error'))
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
                    <ul class="mb-0 text-start">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        @if(session('error'))
                            <li>{{ session('error') }}</li>
                        @endif
                    </ul>
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
                <h3 class="page-title">Quản lý bảng lương theo tháng</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('accounting.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý bảng lương theo tháng</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('accounting.payrollrun.index') }}">
        <div class="row">
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã bảng lương</label>
                    <input type="text" name="search_id" class="form-control" placeholder="Tìm kiếm theo mã ..." value="{{ request('search_id') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Đơn vị</label>
                    <select name="search_unitid" class="form-control">
                        <option value="">-- Tất cả --</option>
                        @foreach($allUnits as $unit)
                            <option value="{{ $unit->unitid }}" {{ (string)request('search_unitid') === (string)$unit->unitid ? 'selected' : '' }}>
                                {{ $unit->unitname }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Tháng lương</label>
                    <input type="month" name="search_payrollperiod" class="form-control" value="{{ request('search_payrollperiod') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Trạng thái</label>
                    <select name="search_status" class="form-control">
                        <option value="">-- Tất cả --</option>
                        <option value="draft" {{ request('search_status') === 'draft' ? 'selected' : '' }}>Khởi tạo</option>
                        <option value="calculating" {{ request('search_status') === 'calculating' ? 'selected' : '' }}>Đang tính toán</option>
                        <option value="approved" {{ request('search_status') === 'approved' ? 'selected' : '' }}>Đã chốt</option>
                        <option value="paid" {{ request('search_status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Ngày tạo</label>
                    <input type="date" name="search_createdat" class="form-control" value="{{ request('search_createdat') }}">
                </div>
            </div>
            <div class="col-lg-1 col-md-4 d-flex align-items-end">
                <div class="form-group">
                    <div class="search-student-btn">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card card-table comman-shadow">
            <div class="card-body">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Danh sách bảng lương theo tháng</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPayrollRunModal" onclick="resetCreateForm()">
                                <i class="fas fa-plus me-1"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped payrollrun-table">
                        <thead class="student-thread">
                            <tr>
                                <th class="text-center" style="padding-right: 50px !important;">Mã bảng lương</th>
                                <th>Đơn vị</th>
                                <th>Mức lương cơ bản</th>
                                <th>Tháng lương</th>
                                <th class="text-center">Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Ngày chốt</th>
                                <th class="description-column">Ghi chú</th>
                                <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrollRuns as $payrollRun)
                                <tr>
                                    <td style="padding-left: 70px !important;">{{ $payrollRun->payrollrunid }}</td>
                                    <td>{{ $payrollRun->unit ? $payrollRun->unit->unitname : '-' }}</td>
                                    <td>
                                        @if($payrollRun->baseSalary && $payrollRun->baseSalary->basesalaryamount !== null)
                                            {{ number_format($payrollRun->baseSalary->basesalaryamount, 0, '.', ',') }} đ
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $payrollRun->payrollperiod ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $payrollRun->status_badge_class }}">
                                            {{ $payrollRun->status_label }}
                                        </span>
                                    </td>
                                    <td>{{ $payrollRun->createdat ? $payrollRun->createdat->format('d/m/Y H:i') : '-' }}</td>
                                    <td>{{ $payrollRun->approvedat ? $payrollRun->approvedat->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="description-column" title="{{ $payrollRun->note ?? '-' }}">
                                        @if($payrollRun->note)
                                            {{ Str::limit($payrollRun->note, 50, '...') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group payrollrun-actions" role="group">
                                            <a href="#"
                                               class="btn btn-warning btn-sm rounded-pill me-1 text-white view-payrollrun-btn"
                                               data-bs-toggle="modal" data-bs-target="#view_payrollrun"
                                               title="Xem chi tiết"
                                               data-payrollrun-id="{{ $payrollRun->payrollrunid }}"
                                               data-unit-name="{{ htmlspecialchars($payrollRun->unit ? $payrollRun->unit->unitname : '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-basesalary-id="{{ $payrollRun->basesalaryid ?? '' }}"
                                               data-basesalary-amount="{{ $payrollRun->baseSalary && $payrollRun->baseSalary->basesalaryamount !== null ? number_format((float)$payrollRun->baseSalary->basesalaryamount, 0, '.', '') : '' }}"
                                               data-payroll-period="{{ $payrollRun->payrollperiod ?? '' }}"
                                               data-status="{{ $payrollRun->status ?? '' }}"
                                               data-created-at="{{ $payrollRun->createdat ? $payrollRun->createdat->format('Y-m-d\TH:i') : '' }}"
                                               data-approved-at="{{ $payrollRun->approvedat ? $payrollRun->approvedat->format('Y-m-d\TH:i') : '' }}"
                                               data-note="{{ htmlspecialchars($payrollRun->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(in_array($payrollRun->status, ['draft', 'calculating']))
                                            <button type="button" 
                                                    class="btn btn-info btn-sm rounded-pill me-1 text-white preview-calculate-btn" 
                                                    title="Xem trước và tính lương tự động"
                                                    data-payrollrun-id="{{ $payrollRun->payrollrunid }}">
                                                <i class="fas fa-calculator"></i>
                                            </button>
                                            @endif
                                            <a href="#"
                                               class="btn btn-success btn-sm rounded-pill me-1 text-white edit-payrollrun-btn"
                                               data-bs-toggle="modal" data-bs-target="#edit_payrollrun"
                                               title="Chỉnh sửa"
                                               data-payrollrun-id="{{ $payrollRun->payrollrunid }}"
                                               data-unit-id="{{ $payrollRun->unitid ?? '' }}"
                                               data-basesalary-id="{{ $payrollRun->basesalaryid ?? '' }}"
                                               data-payroll-period="{{ $payrollRun->payrollperiod ?? '' }}"
                                               data-status="{{ $payrollRun->status ?? '' }}"
                                               data-note="{{ htmlspecialchars($payrollRun->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="#"
                                               class="btn btn-danger btn-sm rounded-pill text-white delete-payrollrun-btn"
                                               data-bs-toggle="modal" data-bs-target="#delete_payrollrun"
                                               title="Xóa"
                                               data-payrollrun-id="{{ $payrollRun->payrollrunid }}"
                                               data-unit-name="{{ htmlspecialchars($payrollRun->unit ? $payrollRun->unit->unitname : '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-payroll-period="{{ $payrollRun->payrollperiod ?? '-' }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Chưa có dữ liệu bảng lương theo tháng.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade payrollrun-modal" id="createPayrollRunModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Thêm bảng lương theo tháng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('accounting.payrollrun.store') }}" method="post" id="createPayrollRunForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Đơn vị <span class="text-danger">*</span></label>
                        <select name="unitid" id="create_unitid" class="form-control" required onchange="loadBaseSalaries(this.value, 'create')">
                            <option value="">-- Chọn đơn vị --</option>
                            @foreach($allUnits as $unit)
                                <option value="{{ $unit->unitid }}" {{ old('unitid') == $unit->unitid ? 'selected' : '' }}>
                                    {{ $unit->unitname }}
                                </option>
                            @endforeach
                        </select>
                        @error('unitid')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mức lương cơ bản <span class="text-danger">*</span></label>
                        <select name="basesalaryid" id="create_basesalaryid" class="form-control" required>
                            <option value="">-- Chọn đơn vị trước --</option>
                        </select>
                        <small class="text-muted">Chọn mức lương cơ bản đang hiệu lực cho đơn vị này</small>
                        @error('basesalaryid')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tháng lương <span class="text-danger">*</span></label>
                        <input type="month" name="payrollperiod" id="create_payrollperiod" class="form-control" required value="{{ old('payrollperiod', date('Y-m')) }}">
                        <small class="text-muted">Định dạng: YYYY-MM (ví dụ: 2025-11)</small>
                        @error('payrollperiod')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select name="status" id="create_status" class="form-control" required>
                            <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Khởi tạo</option>
                            <option value="calculating" {{ old('status') === 'calculating' ? 'selected' : '' }}>Đang tính toán</option>
                            <option value="approved" {{ old('status') === 'approved' ? 'selected' : '' }}>Đã chốt</option>
                            <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                        </select>
                        @error('status')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="note" id="create_note" rows="3" class="form-control" placeholder="Nhập ghi chú (không bắt buộc)">{{ old('note') }}</textarea>
                        @error('note')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade payrollrun-modal" id="view_payrollrun" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết bảng lương theo tháng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Mã bảng lương</label>
                    <input type="text" id="view_payrollrunid" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Đơn vị</label>
                    <input type="text" id="view_unitname" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mức lương cơ bản</label>
                    <input type="text" id="view_basesalaryamount" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tháng lương</label>
                    <input type="text" id="view_payrollperiod" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <input type="text" id="view_status" class="form-control" readonly>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Ngày tạo</label>
                            <input type="text" id="view_createdat" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Ngày chốt</label>
                            <input type="text" id="view_approvedat" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea id="view_note" rows="3" class="form-control" readonly></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade payrollrun-modal" id="edit_payrollrun" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chỉnh sửa bảng lương theo tháng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPayrollRunForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Đơn vị <span class="text-danger">*</span></label>
                        <select name="unitid" id="edit_unitid" class="form-control" required onchange="loadBaseSalaries(this.value, 'edit')">
                            <option value="">-- Chọn đơn vị --</option>
                            @foreach($allUnits as $unit)
                                <option value="{{ $unit->unitid }}">
                                    {{ $unit->unitname }}
                                </option>
                            @endforeach
                        </select>
                        @error('unitid')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mức lương cơ bản <span class="text-danger">*</span></label>
                        <select name="basesalaryid" id="edit_basesalaryid" class="form-control" required>
                            <option value="">-- Chọn đơn vị trước --</option>
                        </select>
                        <small class="text-muted">Chọn mức lương cơ bản đang hiệu lực cho đơn vị này</small>
                        @error('basesalaryid')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tháng lương <span class="text-danger">*</span></label>
                        <input type="month" name="payrollperiod" id="edit_payrollperiod" class="form-control" required>
                        <small class="text-muted">Định dạng: YYYY-MM (ví dụ: 2025-11)</small>
                        @error('payrollperiod')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select name="status" id="edit_status" class="form-control" required>
                            <option value="draft">Khởi tạo</option>
                            <option value="calculating">Đang tính toán</option>
                            <option value="approved">Đã chốt</option>
                            <option value="paid">Đã thanh toán</option>
                        </select>
                        @error('status')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="note" id="edit_note" rows="3" class="form-control"></textarea>
                        @error('note')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade payrollrun-modal modal-delete" id="delete_payrollrun" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xóa bảng lương theo tháng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deletePayrollRunForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa bảng lương theo tháng <strong id="delete_unit_name">này</strong> (tháng: <strong id="delete_payroll_period">-</strong>) không?</p>
                    <p class="text-danger small mb-0">Lưu ý: Chỉ có thể xóa bảng lương ở trạng thái "Khởi tạo"</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables/datatables.min.css') }}">
<style>
    .payrollrun-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        padding: 0.75rem;
    }

    .payrollrun-table tbody td {
        font-size: 1rem;
    }

    .payrollrun-table thead th {
        cursor: default !important;
    }

    .payrollrun-table thead th.sorting::before,
    .payrollrun-table thead th.sorting::after,
    .payrollrun-table thead th.sorting_asc::before,
    .payrollrun-table thead th.sorting_asc::after,
    .payrollrun-table thead th.sorting_desc::before,
    .payrollrun-table thead th.sorting_desc::after,
    .payrollrun-table thead th.sorting_asc_disabled::before,
    .payrollrun-table thead th.sorting_asc_disabled::after,
    .payrollrun-table thead th.sorting_desc_disabled::before,
    .payrollrun-table thead th.sorting_desc_disabled::after,
    .payrollrun-table thead th::before,
    .payrollrun-table thead th::after {
        display: none !important;
        content: none !important;
    }

    .payrollrun-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .payrollrun-table td {
        vertical-align: middle;
    }

    .payrollrun-table th.description-column,
    .payrollrun-table td.description-column {
        max-width: 200px;
        width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .payrollrun-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .payrollrun-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .payrollrun-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .payrollrun-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .payrollrun-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }

    .payrollrun-modal .form-control,
    .payrollrun-modal .form-select {
        border-radius: 0.65rem;
        border-color: #dbe3f2;
        padding: 0.6rem 0.85rem;
        box-shadow: none;
    }

    .payrollrun-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    .payrollrun-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }

    .payrollrun-modal.modal-delete .modal-footer {
        background: #fef2f2;
    }

    /* Style cho notification modal (thông báo thành công/lỗi) */
    .notification-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .notification-modal .modal-header {
        border-bottom: none;
        padding: 1.25rem 1.5rem;
    }

    .notification-modal.modal-success .modal-header {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .notification-modal.modal-error .modal-header {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .notification-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
        display: flex;
        align-items: center;
    }

    .notification-modal .modal-title i {
        font-size: 1.25rem;
    }

    .notification-modal .modal-body {
        background: #f8fafc;
        padding: 2rem 1.75rem;
    }

    .notification-modal .modal-body ul {
        max-width: 500px;
        margin: 0 auto;
    }

    .notification-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
        padding: 1rem 1.5rem;
    }

    .notification-modal.modal-success .modal-footer {
        background: #ecfdf5;
    }

    .notification-modal.modal-error .modal-footer {
        background: #fef2f2;
    }

    .notification-modal .btn {
        border-radius: 999px;
        padding-inline: 1.5rem;
        font-weight: 500;
        min-width: 120px;
    }

    .notification-modal.modal-success .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
    }

    .notification-modal.modal-success .btn-success:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .notification-modal.modal-error .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border: none;
    }

    .notification-modal.modal-error .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
<script>
    // Hàm load mức lương cơ bản theo đơn vị
    function loadBaseSalaries(unitId, formType) {
        const select = document.getElementById(formType + '_basesalaryid');
        select.innerHTML = '<option value="">Đang tải...</option>';
        select.disabled = true;

        if (!unitId) {
            select.innerHTML = '<option value="">-- Chọn đơn vị trước --</option>';
            select.disabled = false;
            return;
        }

        fetch(`{{ route('accounting.payrollrun.getBaseSalariesByUnit', ':unitId') }}`.replace(':unitId', unitId))
            .then(response => response.json())
            .then(data => {
                select.innerHTML = '<option value="">-- Chọn mức lương cơ bản --</option>';
                if (data.length === 0) {
                    select.innerHTML += '<option value="">Không có mức lương cơ bản nào</option>';
                } else {
                    data.forEach(baseSalary => {
                        const option = document.createElement('option');
                        option.value = baseSalary.basesalaryid;
                        const amount = baseSalary.basesalaryamount ? parseFloat(baseSalary.basesalaryamount).toLocaleString('vi-VN') : '0';
                        const effectivedate = baseSalary.effectivedate ? new Date(baseSalary.effectivedate).toLocaleDateString('vi-VN') : '';
                        option.textContent = `${amount} đ - Từ ${effectivedate}`;
                        select.appendChild(option);
                    });
                }
                select.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                select.innerHTML = '<option value="">Lỗi khi tải dữ liệu</option>';
                select.disabled = false;
            });
    }

    // Hàm reset form create
    function resetCreateForm() {
        document.getElementById('create_unitid').value = '';
        document.getElementById('create_basesalaryid').innerHTML = '<option value="">-- Chọn đơn vị trước --</option>';
        document.getElementById('create_payrollperiod').value = '{{ date("Y-m") }}';
        document.getElementById('create_status').value = 'draft';
        document.getElementById('create_note').value = '';
    }

    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('.datatable')) {
            $('.datatable').DataTable().destroy();
        }
        $('.datatable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json',
                paginate: {
                    previous: 'Trước',
                    next: 'Sau'
                }
            },
            pageLength: 10,
            searching: false,
            lengthChange: false,
            info: false,
            columnDefs: [
                { orderable: false, targets: '_all' }
            ],
            order: []
        });

        // Auto show success notification modal
        const successModal = document.getElementById('successNotificationModal');
        if (successModal) {
            const modal = new bootstrap.Modal(successModal);
            modal.show();
        }

        // Auto show error notification modal
        const errorModal = document.getElementById('errorNotificationModal');
        if (errorModal) {
            const modal = new bootstrap.Modal(errorModal);
            modal.show();
        }

        // View button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.view-payrollrun-btn')) {
                const btn = e.target.closest('.view-payrollrun-btn');
                document.getElementById('view_payrollrunid').value = btn.dataset.payrollrunId || '';
                document.getElementById('view_unitname').value = btn.dataset.unitName || '';
                
                const amount = btn.dataset.basesalaryAmount || '';
                if (amount) {
                    const numAmount = parseFloat(amount);
                    document.getElementById('view_basesalaryamount').value = !isNaN(numAmount) ? numAmount.toLocaleString('vi-VN') + ' đ' : amount + ' đ';
                } else {
                    document.getElementById('view_basesalaryamount').value = '-';
                }

                document.getElementById('view_payrollperiod').value = btn.dataset.payrollPeriod || '-';
                
                const status = btn.dataset.status || '';
                const statusLabels = {
                    'draft': 'Khởi tạo',
                    'calculating': 'Đang tính toán',
                    'approved': 'Đã chốt',
                    'paid': 'Đã thanh toán'
                };
                document.getElementById('view_status').value = statusLabels[status] || status;

                const createdAt = btn.dataset.createdAt || '';
                if (createdAt) {
                    const date = new Date(createdAt);
                    document.getElementById('view_createdat').value = date.toLocaleString('vi-VN', { 
                        day: '2-digit', 
                        month: '2-digit', 
                        year: 'numeric', 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });
                } else {
                    document.getElementById('view_createdat').value = '-';
                }

                const approvedAt = btn.dataset.approvedAt || '';
                if (approvedAt) {
                    const date = new Date(approvedAt);
                    document.getElementById('view_approvedat').value = date.toLocaleString('vi-VN', { 
                        day: '2-digit', 
                        month: '2-digit', 
                        year: 'numeric', 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });
                } else {
                    document.getElementById('view_approvedat').value = '-';
                }

                document.getElementById('view_note').value = btn.dataset.note || '';
            }
        });

        // Edit button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-payrollrun-btn')) {
                const btn = e.target.closest('.edit-payrollrun-btn');
                const payrollRunId = btn.dataset.payrollrunId;
                const unitId = btn.dataset.unitId || '';
                const baseSalaryId = btn.dataset.basesalaryId || '';
                
                const form = document.getElementById('editPayrollRunForm');
                form.action = '{{ route("accounting.payrollrun.update", ":id") }}'.replace(':id', payrollRunId);

                document.getElementById('edit_unitid').value = unitId;
                if (unitId) {
                    // Load base salaries và sau đó set giá trị
                    const select = document.getElementById('edit_basesalaryid');
                    const originalOnChange = select.onchange;
                    select.onchange = function() {
                        if (originalOnChange) originalOnChange();
                        if (baseSalaryId && select.value === '') {
                            // Chờ một chút để options được load xong
                            setTimeout(() => {
                                if (select.querySelector(`option[value="${baseSalaryId}"]`)) {
                                    select.value = baseSalaryId;
                                }
                            }, 500);
                        }
                    };
                    loadBaseSalaries(unitId, 'edit');
                    // Set giá trị sau khi load xong
                    setTimeout(() => {
                        if (baseSalaryId) {
                            select.value = baseSalaryId;
                        }
                    }, 500);
                }
                
                document.getElementById('edit_payrollperiod').value = btn.dataset.payrollPeriod || '';
                document.getElementById('edit_status').value = btn.dataset.status || 'draft';
                document.getElementById('edit_note').value = btn.dataset.note || '';
            }
        });

        // Delete button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-payrollrun-btn')) {
                const btn = e.target.closest('.delete-payrollrun-btn');
                const payrollRunId = btn.dataset.payrollrunId;
                const form = document.getElementById('deletePayrollRunForm');
                form.action = '{{ route("accounting.payrollrun.destroy", ":id") }}'.replace(':id', payrollRunId);
                document.getElementById('delete_unit_name').textContent = btn.dataset.unitName || 'này';
                document.getElementById('delete_payroll_period').textContent = btn.dataset.payrollPeriod || '-';
            }
        });
    });

    // Preview và tính lương
    document.addEventListener('click', function(e) {
        if (e.target.closest('.preview-calculate-btn')) {
            const btn = e.target.closest('.preview-calculate-btn');
            const payrollRunId = btn.dataset.payrollrunId;
            
            // Hiển thị loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Gọi API preview
            fetch(`{{ url('admin/payrollrun') }}/${payrollRunId}/preview`)
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.error || `HTTP error! status: ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-calculator"></i>';
                    
                    if (data.success) {
                        // Hiển thị modal preview
                        showPreviewModal(data);
                    } else {
                        let errorMsg = 'Lỗi khi lấy dữ liệu preview: ' + (data.error || 'Unknown error');
                        if (data.file && data.line) {
                            errorMsg += '\nFile: ' + data.file + ':' + data.line;
                        }
                        alert(errorMsg);
                        console.error('Preview error:', data);
                    }
                })
                .catch(error => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-calculator"></i>';
                    console.error('Error:', error);
                    alert('Lỗi khi lấy dữ liệu preview: ' + error.message);
                });
        }
    });

    function showPreviewModal(data) {
        const modal = document.getElementById('preview_calculate_modal');
        const payrollRun = data.payroll_run;
        const teachers = data.teachers;
        
        // Điền thông tin bảng lương
        document.getElementById('preview_unit_name').textContent = payrollRun.unit_name;
        document.getElementById('preview_payroll_period').textContent = payrollRun.payroll_period;
        document.getElementById('preview_base_salary').textContent = new Intl.NumberFormat('vi-VN').format(payrollRun.base_salary) + ' đ';
        
        // Điền danh sách giáo viên
        const tbody = document.getElementById('preview_teachers_tbody');
        tbody.innerHTML = '';
        
        teachers.forEach((teacher, index) => {
            const row = document.createElement('tr');
            
            // Tìm các thành phần quan trọng
            const phuCapChucVu = teacher.components.find(c => c.component_name.toLowerCase().includes('phụ cấp chức vụ'));
            const phuCapVuotKhung = teacher.components.find(c => c.component_name.toLowerCase().includes('phụ cấp vượt khung'));
            const phuCapTrachNhiem = teacher.components.find(c => c.component_name.toLowerCase().includes('phụ cấp trách nhiệm'));
            const phuCapDocHai = teacher.components.find(c => c.component_name.toLowerCase().includes('phụ cấp độc hại'));
            const phuCapUuDai = teacher.components.find(c => c.component_name.toLowerCase().includes('phụ cấp ưu đãi'));
            const phuCapThamNien = teacher.components.find(c => c.component_name.toLowerCase().includes('phụ cấp thâm niên'));
            
            // Helper function để format số an toàn
            const formatNumber = (value, decimals = 4) => {
                if (value === null || value === undefined || isNaN(value)) {
                    return '0.0000';
                }
                return parseFloat(value).toFixed(decimals);
            };
            
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${teacher.teacher_name}</td>
                <td class="text-end">${formatNumber(teacher.teacher_coefficient)}</td>
                <td class="text-end">${formatNumber(phuCapChucVu ? phuCapChucVu.coefficient : 0)}</td>
                <td class="text-end">${formatNumber(phuCapVuotKhung ? phuCapVuotKhung.coefficient : 0)}</td>
                <td class="text-end">${formatNumber(phuCapTrachNhiem ? phuCapTrachNhiem.coefficient : 0)}</td>
                <td class="text-end">${formatNumber(phuCapDocHai ? phuCapDocHai.coefficient : 0)}</td>
                <td class="text-end">${formatNumber(phuCapUuDai ? phuCapUuDai.percentage : 0)}</td>
                <td class="text-end">${formatNumber(phuCapThamNien ? phuCapThamNien.percentage : 0)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-info view-teacher-components-btn" 
                            data-teacher-id="${teacher.teacher_id}" 
                            data-teacher-name="${teacher.teacher_name}"
                            data-components='${JSON.stringify(teacher.components)}'>
                        <i class="fas fa-eye"></i> Xem chi tiết
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
        
        // Lưu payrollRunId để dùng khi xác nhận
        document.getElementById('preview_confirm_form').action = `{{ url('admin/payrollrun') }}/${payrollRun.id}/calculate`;
        
        // Hiển thị modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }

    // Xem chi tiết thành phần của giáo viên
    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-teacher-components-btn')) {
            const btn = e.target.closest('.view-teacher-components-btn');
            const teacherName = btn.dataset.teacherName;
            const components = JSON.parse(btn.dataset.components);
            
            const modal = document.getElementById('view_teacher_components_modal');
            document.getElementById('view_teacher_name').textContent = teacherName;
            
            const tbody = document.getElementById('view_teacher_components_tbody');
            tbody.innerHTML = '';
            
            // Helper function để format số an toàn
            const formatNumber = (value, decimals = 4) => {
                if (value === null || value === undefined || isNaN(value)) {
                    return '-';
                }
                return parseFloat(value).toFixed(decimals);
            };
            
            components.forEach((comp, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${comp.component_name}</td>
                    <td>${comp.calculation_method || '-'}</td>
                    <td class="text-end">${formatNumber(comp.coefficient)}</td>
                    <td class="text-end">${formatNumber(comp.percentage)}</td>
                    <td class="text-end">${comp.fixed !== null && comp.fixed !== undefined && !isNaN(comp.fixed) ? new Intl.NumberFormat('vi-VN').format(comp.fixed) + ' đ' : '-'}</td>
                    <td class="text-end">${formatNumber(comp.adjustcustomcoefficient)}</td>
                    <td class="text-end">${formatNumber(comp.adjustcustompercentage)}</td>
                `;
                tbody.appendChild(row);
            });
            
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    });
</script>

<!-- Preview Calculate Modal -->
<div class="modal fade" id="preview_calculate_modal" tabindex="-1" aria-labelledby="preview_calculate_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="preview_calculate_modalLabel">
                    <i class="fas fa-eye me-2"></i>Xem trước dữ liệu tính lương
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Đơn vị:</strong> <span id="preview_unit_name"></span>
                    </div>
                    <div class="col-md-4">
                        <strong>Tháng lương:</strong> <span id="preview_payroll_period"></span>
                    </div>
                    <div class="col-md-4">
                        <strong>Mức lương cơ bản:</strong> <span id="preview_base_salary"></span>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>STT</th>
                                <th>Giáo viên</th>
                                <th class="text-end">Hệ số lương</th>
                                <th class="text-end">Phụ cấp chức vụ (Hệ số)</th>
                                <th class="text-end">Phụ cấp vượt khung (Hệ số)</th>
                                <th class="text-end">Phụ cấp trách nhiệm (Hệ số)</th>
                                <th class="text-end">Phụ cấp độc hại (Hệ số)</th>
                                <th class="text-end">Phụ cấp ưu đãi (Tỷ lệ)</th>
                                <th class="text-end">Phụ cấp thâm niên (Tỷ lệ)</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="preview_teachers_tbody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <form id="preview_confirm_form" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i>Xác nhận và tính lương
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Teacher Components Modal -->
<div class="modal fade" id="view_teacher_components_modal" tabindex="-1" aria-labelledby="view_teacher_components_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="view_teacher_components_modalLabel">
                    <i class="fas fa-list me-2"></i>Chi tiết thành phần lương - <span id="view_teacher_name"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>STT</th>
                                <th>Tên thành phần</th>
                                <th>Phương thức tính</th>
                                <th class="text-end">Hệ số</th>
                                <th class="text-end">Tỷ lệ (%)</th>
                                <th class="text-end">Số tiền cố định</th>
                                <th class="text-end">Điều chỉnh hệ số</th>
                                <th class="text-end">Điều chỉnh tỷ lệ</th>
                            </tr>
                        </thead>
                        <tbody id="view_teacher_components_tbody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endpush

