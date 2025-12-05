@extends('layouts.accounting')

@section('title', 'Quản lý chi tiết bảng lương từng giáo viên')

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
                <h3 class="page-title">Quản lý chi tiết bảng lương từng giáo viên</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('accounting.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý chi tiết bảng lương từng giáo viên</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('accounting.payrollrundetail.index') }}">
        <div class="row">
            <div class="col-lg-3 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Bảng lương</label>
                    <select name="search_payrollrunid" class="form-control">
                        <option value="">-- Tất cả --</option>
                        @foreach($allPayrollRuns as $payrollRun)
                            <option value="{{ $payrollRun->payrollrunid }}" {{ (string)request('search_payrollrunid') === (string)$payrollRun->payrollrunid ? 'selected' : '' }}>
                                {{ $payrollRun->unit ? $payrollRun->unit->unitname : '-' }} - {{ formatPayrollPeriod($payrollRun->payrollperiod) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Giáo viên</label>
                    <select name="search_teacherid" class="form-control">
                        <option value="">-- Tất cả --</option>
                        @foreach($allTeachers as $teacher)
                            <option value="{{ $teacher->teacherid }}" {{ (string)request('search_teacherid') === (string)$teacher->teacherid ? 'selected' : '' }}>
                                {{ $teacher->fullname }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Tổng thu nhập từ</label>
                    <input type="number" name="search_totalincome_from" class="form-control" step="0.01" min="0" placeholder="Từ" value="{{ request('search_totalincome_from') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Tổng thu nhập đến</label>
                    <input type="number" name="search_totalincome_to" class="form-control" step="0.01" min="0" placeholder="Đến" value="{{ request('search_totalincome_to') }}">
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
                            <h3 class="page-title">Danh sách chi tiết bảng lương từng giáo viên</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportExcelModal">
                                <i class="fas fa-file-excel me-1"></i> Xuất bảng lương chi tiết
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped payrollrundetail-table">
                        <thead class="student-thread">
                            <tr>
                                <th>Bảng lương</th>
                                <th>Giáo viên</th>
                                <th>Tổng thu nhập</th>
                                <th>Tổng khoản trừ</th>
                                <th>Tổng đơn vị đóng</th>
                                <th>Thực lĩnh</th>
                                <th>Tổng chi phí</th>
                                <th class="description-column">Ghi chú</th>
                                <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
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
                                    <td>{{ $detail->teacher ? $detail->teacher->fullname : '-' }}</td>
                                    <td>{{ number_format($detail->totalincome, 0, '.', ',') }} đ</td>
                                    <td>{{ number_format($detail->totalemployeedeductions, 0, '.', ',') }} đ</td>
                                    <td>{{ number_format($detail->totalemployercontributions, 0, '.', ',') }} đ</td>
                                    <td>
                                        <strong class="text-success">{{ number_format($detail->netpay, 0, '.', ',') }} đ</strong>
                                    </td>
                                    <td class="text-end">{{ number_format($detail->totalcost, 0, '.', ',') }} đ</td>
                                    <td class="description-column" title="{{ $detail->note ?? '-' }}">
                                        @if($detail->note)
                                            {{ Str::limit($detail->note, 50, '...') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-start">
                                        <div class="btn-group payrollrundetail-actions" role="group">
                                            <a href="#"
                                               class="btn btn-warning btn-sm rounded-pill me-1 text-white view-payrollrundetail-btn"
                                               data-bs-toggle="modal" data-bs-target="#view_payrollrundetail"
                                               title="Xem chi tiết"
                                               data-detail-id="{{ $detail->detailid }}"
                                               data-payroll-run="{{ $detail->payrollRun ? ($detail->payrollRun->unit ? $detail->payrollRun->unit->unitname : '-').' - '.formatPayrollPeriod($detail->payrollRun->payrollperiod) : '-' }}"
                                               data-teacher-name="{{ htmlspecialchars($detail->teacher ? $detail->teacher->fullname : '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-total-income="{{ number_format($detail->totalincome, 2, '.', '') }}"
                                               data-total-employee-deductions="{{ number_format($detail->totalemployeedeductions, 2, '.', '') }}"
                                               data-total-employer-contributions="{{ number_format($detail->totalemployercontributions, 2, '.', '') }}"
                                               data-net-pay="{{ number_format($detail->netpay, 2, '.', '') }}"
                                               data-total-cost="{{ number_format($detail->totalcost, 2, '.', '') }}"
                                               data-note="{{ htmlspecialchars($detail->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#"
                                               class="btn btn-danger btn-sm rounded-pill text-white delete-payrollrundetail-btn"
                                               data-bs-toggle="modal" data-bs-target="#delete_payrollrundetail"
                                               title="Xóa"
                                               data-detail-id="{{ $detail->detailid }}"
                                               data-teacher-name="{{ htmlspecialchars($detail->teacher ? $detail->teacher->fullname : '-', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Chưa có dữ liệu chi tiết bảng lương từng giáo viên.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade payrollrundetail-modal" id="view_payrollrundetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết bảng lương từng giáo viên</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Bảng lương</label>
                    <input type="text" id="view_payrollrun" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giáo viên</label>
                    <input type="text" id="view_teachername" class="form-control" readonly>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tổng thu nhập</label>
                            <input type="text" id="view_totalincome" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tổng khoản trừ nhân viên</label>
                            <input type="text" id="view_totalemployeedeductions" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tổng khoản đóng đơn vị</label>
                            <input type="text" id="view_totalemployercontributions" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Thực lĩnh</label>
                            <input type="text" id="view_netpay" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tổng chi phí</label>
                    <input type="text" id="view_totalcost" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea id="view_note" rows="3" class="form-control" readonly></textarea>
                </div>

                <hr class="my-4">

                <!-- Chi tiết các thành phần lương -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Chi tiết các thành phần lương</label>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm table-bordered table-hover">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width: 5%;">STT</th>
                                    <th style="width: 25%;">Tên thành phần</th>
                                    <th style="width: 15%;">Phương thức tính</th>
                                    <th style="width: 12%;" class="text-end">Hệ số</th>
                                    <th style="width: 12%;" class="text-end">Tỷ lệ (%)</th>
                                    <th style="width: 15%;" class="text-end">Số tiền</th>
                                    <th style="width: 16%;">Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody id="view_components_tbody">
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Đang tải...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Cách tính chi tiết -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Cách tính chi tiết</label>
                    <div id="view_calculation_details" class="calculation-details" style="max-height: 400px; overflow-y: auto; padding: 1rem; background-color: #f8f9fa; border-radius: 0.5rem; font-size: 0.9rem;">
                        <div class="text-center text-muted">
                            <i class="fas fa-spinner fa-spin me-2"></i>Đang tải...
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade payrollrundetail-modal modal-delete" id="delete_payrollrundetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xóa chi tiết bảng lương từng giáo viên</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deletePayrollRunDetailForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa chi tiết bảng lương của giáo viên <strong id="delete_teacher_name">này</strong> không?</p>
                    <p class="text-danger small mb-0">Lưu ý: Tất cả các chi tiết thành phần lương liên quan cũng sẽ bị xóa.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Export Excel Modal -->
<div class="modal fade payrollrundetail-modal" id="exportExcelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xuất bảng lương chi tiết ra Excel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('accounting.payrollrundetail.export') }}" method="GET" id="exportExcelForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Chọn tháng lương cần xuất <span class="text-danger">*</span></label>
                        <select name="search_payrollrunid" id="export_payrollrunid" class="form-control" required>
                            <option value="">-- Chọn tháng lương --</option>
                            @foreach($allPayrollRuns as $payrollRun)
                                <option value="{{ $payrollRun->payrollrunid }}">
                                    {{ $payrollRun->unit ? $payrollRun->unit->unitname : '-' }} - {{ formatPayrollPeriod($payrollRun->payrollperiod) }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Vui lòng chọn tháng lương cần xuất ra file Excel</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-excel me-1"></i> Xuất Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables/datatables.min.css') }}">
<style>
    .payrollrundetail-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        padding: 0.75rem;
    }

    .payrollrundetail-table tbody td {
        font-size: 1rem;
    }

    .payrollrundetail-table thead th {
        cursor: default !important;
    }

    .payrollrundetail-table thead th.sorting::before,
    .payrollrundetail-table thead th.sorting::after,
    .payrollrundetail-table thead th.sorting_asc::before,
    .payrollrundetail-table thead th.sorting_asc::after,
    .payrollrundetail-table thead th.sorting_desc::before,
    .payrollrundetail-table thead th.sorting_desc::after,
    .payrollrundetail-table thead th.sorting_asc_disabled::before,
    .payrollrundetail-table thead th.sorting_asc_disabled::after,
    .payrollrundetail-table thead th.sorting_desc_disabled::before,
    .payrollrundetail-table thead th.sorting_desc_disabled::after,
    .payrollrundetail-table thead th::before,
    .payrollrundetail-table thead th::after {
        display: none !important;
        content: none !important;
    }

    .payrollrundetail-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .payrollrundetail-table td {
        vertical-align: middle;
    }

    .payrollrundetail-table th.description-column,
    .payrollrundetail-table td.description-column {
        max-width: 200px;
        width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .calculation-details {
        line-height: 2;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 0.95rem;
    }

    .calculation-details strong {
        color: #1e40af;
        font-weight: 600;
        font-size: 1rem;
    }

    .calculation-details > div {
        margin-bottom: 1.25rem;
        padding: 0.75rem;
        background-color: #ffffff;
        border-left: 3px solid #3b82f6;
        border-radius: 0.25rem;
    }

    .calculation-details > div > strong:first-child {
        display: block;
        margin-bottom: 0.5rem;
        color: #1e3a8a;
        font-size: 1.05rem;
    }
    
    .calculation-details > div > strong:not(:first-child) {
        display: inline;
        color: #1e40af;
    }

    .payrollrundetail-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .payrollrundetail-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .payrollrundetail-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .payrollrundetail-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .payrollrundetail-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }

    .payrollrundetail-modal .form-control,
    .payrollrundetail-modal .form-select {
        border-radius: 0.65rem;
        border-color: #dbe3f2;
        padding: 0.6rem 0.85rem;
        box-shadow: none;
    }

    .payrollrundetail-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    .payrollrundetail-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }

    .payrollrundetail-modal.modal-delete .modal-footer {
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
            if (e.target.closest('.view-payrollrundetail-btn')) {
                const btn = e.target.closest('.view-payrollrundetail-btn');
                const detailId = btn.dataset.detailId;
                
                document.getElementById('view_payrollrun').value = btn.dataset.payrollRun || '-';
                document.getElementById('view_teachername').value = btn.dataset.teacherName || '-';
                
                const totalIncome = parseFloat(btn.dataset.totalIncome || 0);
                document.getElementById('view_totalincome').value = totalIncome.toLocaleString('vi-VN') + ' đ';
                
                const totalEmployeeDeductions = parseFloat(btn.dataset.totalEmployeeDeductions || 0);
                document.getElementById('view_totalemployeedeductions').value = totalEmployeeDeductions.toLocaleString('vi-VN') + ' đ';
                
                const totalEmployerContributions = parseFloat(btn.dataset.totalEmployerContributions || 0);
                document.getElementById('view_totalemployercontributions').value = totalEmployerContributions.toLocaleString('vi-VN') + ' đ';
                
                const netPay = parseFloat(btn.dataset.netPay || 0);
                document.getElementById('view_netpay').value = netPay.toLocaleString('vi-VN') + ' đ';
                
                const totalCost = parseFloat(btn.dataset.totalCost || 0);
                document.getElementById('view_totalcost').value = totalCost.toLocaleString('vi-VN') + ' đ';

                document.getElementById('view_note').value = btn.dataset.note || '';

                // Load components và cách tính
                loadCalculationDetails(detailId);
            }
        });

        // Hàm load chi tiết tính toán
        function loadCalculationDetails(detailId) {
            const tbody = document.getElementById('view_components_tbody');
            const calculationDiv = document.getElementById('view_calculation_details');
            
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Đang tải...</td></tr>';
            calculationDiv.innerHTML = '<div class="text-center text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Đang tải...</div>';
            
            fetch('{{ route("accounting.payrollrundetail.getCalculationDetails", ":id") }}'.replace(':id', detailId))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hiển thị danh sách components
                        if (data.components && data.components.length > 0) {
                            let html = '';
                            data.components.forEach((component, index) => {
                                html += '<tr>';
                                html += '<td>' + (index + 1) + '</td>';
                                html += '<td>' + (component.component_name || '-') + '</td>';
                                html += '<td>' + (component.calculation_method || '-') + '</td>';
                                html += '<td class="text-end">' + (component.applied_coefficient ? parseFloat(component.applied_coefficient).toFixed(4) : '-') + '</td>';
                                html += '<td class="text-end">' + (component.applied_percentage ? parseFloat(component.applied_percentage).toFixed(4) : '-') + '</td>';
                                html += '<td class="text-end"><strong>' + (component.calculated_amount ? parseFloat(component.calculated_amount).toLocaleString('vi-VN') + ' đ' : '-') + '</strong></td>';
                                html += '<td title="' + (component.note || '') + '">' + (component.note ? (component.note.length > 30 ? component.note.substring(0, 30) + '...' : component.note) : '-') + '</td>';
                                html += '</tr>';
                            });
                            tbody.innerHTML = html;
                        } else {
                            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Chưa có thành phần lương nào.</td></tr>';
                        }

                        // Hiển thị cách tính chi tiết
                        const detail = data.detail;
                        let calcHtml = '';
                        
                        // 1. Thông tin cơ bản
                        calcHtml += '<div class="mb-3"><strong>1. THÔNG TIN CƠ BẢN:</strong>';
                        calcHtml += '   - Hệ số lương: ' + formatNumber(detail.teacher_coefficient, 4) + '<br>';
                        calcHtml += '   - Mức lương cơ bản: ' + formatNumber(detail.base_salary, 0) + ' đ</div>';

                        // 2. Các phụ cấp hệ số
                        calcHtml += '<div class="mb-3"><strong>2. CÁC PHỤ CẤP (HỆ SỐ):</strong>';
                        calcHtml += '   - Phụ cấp chức vụ: ' + formatNumber(detail.phu_cap_chuc_vu, 4) + '<br>';
                        calcHtml += '   - Phụ cấp vượt khung: ' + formatNumber(detail.phu_cap_vuot_khung, 4) + '<br>';
                        calcHtml += '   - Phụ cấp trách nhiệm: ' + formatNumber(detail.phu_cap_trach_nhiem, 4) + '<br>';
                        calcHtml += '   - Phụ cấp độc hại: ' + formatNumber(detail.phu_cap_doc_hai, 4) + '</div>';

                        // 3. Tính hệ số phụ cấp
                        const tongHeSoChoPhuCap = parseFloat(detail.teacher_coefficient || 0) + parseFloat(detail.phu_cap_chuc_vu || 0) + parseFloat(detail.phu_cap_vuot_khung || 0);
                        const phuCapUuDaiPct = parseFloat(detail.phu_cap_uu_dai_percentage || 0) >= 1 ? parseFloat(detail.phu_cap_uu_dai_percentage || 0) / 100 : parseFloat(detail.phu_cap_uu_dai_percentage || 0) / 10;
                        calcHtml += '<div class="mb-3"><strong>3. TÍNH HỆ SỐ PHỤ CẤP:</strong>';
                        calcHtml += '   - Tổng hệ số cho phụ cấp = ' + formatNumber(detail.teacher_coefficient, 4) + ' + ' + formatNumber(detail.phu_cap_chuc_vu, 4) + ' + ' + formatNumber(detail.phu_cap_vuot_khung, 4) + ' = <strong>' + formatNumber(tongHeSoChoPhuCap, 4) + '</strong><br>';
                        calcHtml += '   - Phụ cấp ưu đãi (%): ' + formatNumber(detail.phu_cap_uu_dai_percentage, 4) + ' (' + formatNumber(phuCapUuDaiPct * 100, 2) + '%)<br>';
                        calcHtml += '   - Hệ số phụ cấp = ' + formatNumber(tongHeSoChoPhuCap, 4) + ' × ' + formatNumber(phuCapUuDaiPct, 4) + ' = <strong>' + formatNumber(detail.he_so_phu_cap, 4) + '</strong></div>';

                        // 4. Tính hệ số phụ cấp thâm niên
                        const phuCapThamNienPct = parseFloat(detail.phu_cap_tham_nien_percentage || 0) >= 1 ? parseFloat(detail.phu_cap_tham_nien_percentage || 0) / 100 : parseFloat(detail.phu_cap_tham_nien_percentage || 0) / 10;
                        calcHtml += '<div class="mb-3"><strong>4. TÍNH HỆ SỐ PHỤ CẤP THÂM NIÊN:</strong>';
                        calcHtml += '   - Phụ cấp thâm niên (%): ' + formatNumber(detail.phu_cap_tham_nien_percentage, 4) + ' (' + formatNumber(phuCapThamNienPct * 100, 2) + '%)<br>';
                        calcHtml += '   - Hệ số phụ cấp thâm niên = ' + formatNumber(tongHeSoChoPhuCap, 4) + ' × ' + formatNumber(phuCapThamNienPct, 4) + ' = <strong>' + formatNumber(detail.he_so_phu_cap_tham_nien, 4) + '</strong></div>';

                        // 5. Tổng hệ số
                        calcHtml += '<div class="mb-3"><strong>5. TỔNG HỆ SỐ:</strong>';
                        calcHtml += '   = ' + formatNumber(detail.teacher_coefficient, 4) + ' + ' + formatNumber(detail.phu_cap_chuc_vu, 4) + ' + ' + formatNumber(detail.phu_cap_vuot_khung, 4) + ' + ' + formatNumber(detail.phu_cap_trach_nhiem, 4) + ' + ' + formatNumber(detail.phu_cap_doc_hai, 4) + ' + ' + formatNumber(detail.he_so_phu_cap, 4) + ' + ' + formatNumber(detail.he_so_phu_cap_tham_nien, 4) + ' = <strong>' + formatNumber(detail.tong_he_so, 4) + '</strong></div>';

                        // 6. Quỹ lương phụ cấp
                        calcHtml += '<div class="mb-3"><strong>6. QUỸ LƯƠNG PHỤ CẤP 01 THÁNG:</strong>';
                        calcHtml += '   = ' + formatNumber(detail.tong_he_so, 4) + ' × ' + formatNumber(detail.base_salary, 0) + ' đ = <strong>' + formatNumber(detail.quy_luong_phu_cap, 0) + ' đ</strong></div>';

                        // 7. Các khoản trừ
                        const bhxhPct = parseFloat(detail.bhxh_nhan_vien_percentage || 0) >= 1 ? parseFloat(detail.bhxh_nhan_vien_percentage || 0) / 100 : parseFloat(detail.bhxh_nhan_vien_percentage || 0) / 10;
                        const bhytPct = parseFloat(detail.bhyt_nhan_vien_percentage || 0) >= 1 ? parseFloat(detail.bhyt_nhan_vien_percentage || 0) / 100 : parseFloat(detail.bhyt_nhan_vien_percentage || 0) / 10;
                        const bhtnPct = parseFloat(detail.bhtn_nhan_vien_percentage || 0) >= 1 ? parseFloat(detail.bhtn_nhan_vien_percentage || 0) / 100 : parseFloat(detail.bhtn_nhan_vien_percentage || 0) / 10;
                        calcHtml += '<div class="mb-3"><strong>7. CÁC KHOẢN TRỪ (10.5%):</strong>';
                        calcHtml += '   - Tổng hệ số để trừ = ' + formatNumber(detail.teacher_coefficient, 4) + ' + ' + formatNumber(detail.phu_cap_chuc_vu, 4) + ' + ' + formatNumber(detail.phu_cap_vuot_khung, 4) + ' + ' + formatNumber(detail.he_so_phu_cap_tham_nien, 4) + ' = <strong>' + formatNumber(detail.tong_he_so_cho_tru, 4) + '</strong><br>';
                        calcHtml += '   - Quỹ lương để trừ = ' + formatNumber(detail.tong_he_so_cho_tru, 4) + ' × ' + formatNumber(detail.base_salary, 0) + ' đ = <strong>' + formatNumber(detail.quy_luong_cho_tru, 0) + ' đ</strong><br>';
                        calcHtml += '   - BHXH nhân viên (%): ' + formatNumber(detail.bhxh_nhan_vien_percentage, 4) + ' (' + formatNumber(bhxhPct * 100, 2) + '%)<br>';
                        calcHtml += '     → Số tiền trừ BHXH = ' + formatNumber(detail.quy_luong_cho_tru, 0) + ' đ × ' + formatNumber(bhxhPct, 4) + ' = <strong>' + formatNumber(detail.tien_tru_bhxh, 0) + ' đ</strong><br>';
                        calcHtml += '   - BHYT nhân viên (%): ' + formatNumber(detail.bhyt_nhan_vien_percentage, 4) + ' (' + formatNumber(bhytPct * 100, 2) + '%)<br>';
                        calcHtml += '     → Số tiền trừ BHYT = ' + formatNumber(detail.quy_luong_cho_tru, 0) + ' đ × ' + formatNumber(bhytPct, 4) + ' = <strong>' + formatNumber(detail.tien_tru_bhyt, 0) + ' đ</strong><br>';
                        calcHtml += '   - BHTN nhân viên (%): ' + formatNumber(detail.bhtn_nhan_vien_percentage, 4) + ' (' + formatNumber(bhtnPct * 100, 2) + '%)<br>';
                        calcHtml += '     → Số tiền trừ BHTN = ' + formatNumber(detail.quy_luong_cho_tru, 0) + ' đ × ' + formatNumber(bhtnPct, 4) + ' = <strong>' + formatNumber(detail.tien_tru_bhtn, 0) + ' đ</strong><br>';
                        calcHtml += '   - Tổng trừ nhân viên = ' + formatNumber(detail.tien_tru_bhxh, 0) + ' đ + ' + formatNumber(detail.tien_tru_bhyt, 0) + ' đ + ' + formatNumber(detail.tien_tru_bhtn, 0) + ' đ = <strong>' + formatNumber(detail.tong_tru_nhan_vien, 0) + ' đ</strong></div>';

                        // 8. Số tiền thực lĩnh
                        calcHtml += '<div class="mb-3"><strong>8. SỐ TIỀN THỰC LĨNH:</strong>';
                        calcHtml += '   = ' + formatNumber(detail.quy_luong_phu_cap, 0) + ' đ - ' + formatNumber(detail.tong_tru_nhan_vien, 0) + ' đ = <strong>' + formatNumber(detail.thuc_linh, 0) + ' đ</strong></div>';

                        // 9. Các khoản ngân sách
                        const bhxhDonViPct = parseFloat(detail.bhxh_don_vi_percentage || 0) >= 1 ? parseFloat(detail.bhxh_don_vi_percentage || 0) / 100 : parseFloat(detail.bhxh_don_vi_percentage || 0) / 10;
                        const bhytDonViPct = parseFloat(detail.bhyt_don_vi_percentage || 0) >= 1 ? parseFloat(detail.bhyt_don_vi_percentage || 0) / 100 : parseFloat(detail.bhyt_don_vi_percentage || 0) / 10;
                        const bhtnDonViPct = parseFloat(detail.bhtn_don_vi_percentage || 0) >= 1 ? parseFloat(detail.bhtn_don_vi_percentage || 0) / 100 : parseFloat(detail.bhtn_don_vi_percentage || 0) / 10;
                        const bhtnTuNSPct = parseFloat(detail.bhtn_tu_ngan_sach_percentage || 0) >= 1 ? parseFloat(detail.bhtn_tu_ngan_sach_percentage || 0) / 100 : parseFloat(detail.bhtn_tu_ngan_sach_percentage || 0) / 10;
                        calcHtml += '<div class="mb-3"><strong>9. CÁC KHOẢN NGÂN SÁCH (21.5%):</strong>';
                        calcHtml += '   - BHXH đơn vị (%): ' + formatNumber(detail.bhxh_don_vi_percentage, 4) + ' (' + formatNumber(bhxhDonViPct * 100, 2) + '%)<br>';
                        calcHtml += '     → Ngân sách BHXH = ' + formatNumber(detail.quy_luong_cho_tru, 0) + ' đ × ' + formatNumber(bhxhDonViPct, 4) + ' = <strong>' + formatNumber(detail.ngan_sach_bhxh, 0) + ' đ</strong><br>';
                        calcHtml += '   - BHYT đơn vị (%): ' + formatNumber(detail.bhyt_don_vi_percentage, 4) + ' (' + formatNumber(bhytDonViPct * 100, 2) + '%)<br>';
                        calcHtml += '     → Ngân sách BHYT = ' + formatNumber(detail.quy_luong_cho_tru, 0) + ' đ × ' + formatNumber(bhytDonViPct, 4) + ' = <strong>' + formatNumber(detail.ngan_sach_bhyt, 0) + ' đ</strong><br>';
                        calcHtml += '   - BHTN đơn vị (%): ' + formatNumber(detail.bhtn_don_vi_percentage, 4) + ' (' + formatNumber(bhtnDonViPct * 100, 2) + '%)<br>';
                        calcHtml += '     → Ngân sách BHTN = ' + formatNumber(detail.quy_luong_cho_tru, 0) + ' đ × ' + formatNumber(bhtnDonViPct, 4) + ' = <strong>' + formatNumber(detail.ngan_sach_bhtn, 0) + ' đ</strong><br>';
                        calcHtml += '   - BHTN từ ngân sách (%): ' + formatNumber(detail.bhtn_tu_ngan_sach_percentage, 4) + ' (' + formatNumber(bhtnTuNSPct * 100, 2) + '%)<br>';
                        calcHtml += '     → Ngân sách BHTN từ NS = ' + formatNumber(detail.quy_luong_cho_tru, 0) + ' đ × ' + formatNumber(bhtnTuNSPct, 4) + ' = <strong>' + formatNumber(detail.ngan_sach_bhtn_tu_ns, 0) + ' đ</strong><br>';
                        calcHtml += '   - Tổng ngân sách = ' + formatNumber(detail.ngan_sach_bhxh, 0) + ' đ + ' + formatNumber(detail.ngan_sach_bhyt, 0) + ' đ + ' + formatNumber(detail.ngan_sach_bhtn, 0) + ' đ + ' + formatNumber(detail.ngan_sach_bhtn_tu_ns, 0) + ' đ = <strong>' + formatNumber(detail.tong_ngan_sach, 0) + ' đ</strong></div>';

                        // 10. Tổng chi phí
                        calcHtml += '<div class="mb-3"><strong>10. TỔNG CHI PHÍ:</strong>';
                        calcHtml += '    = ' + formatNumber(detail.quy_luong_phu_cap, 0) + ' đ + ' + formatNumber(detail.tong_ngan_sach, 0) + ' đ = <strong>' + formatNumber(detail.tong_chi_phi, 0) + ' đ</strong></div>';

                        calculationDiv.innerHTML = calcHtml;
                    } else {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Lỗi khi tải dữ liệu.</td></tr>';
                        calculationDiv.innerHTML = '<div class="text-center text-danger">Lỗi khi tải dữ liệu.</div>';
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi tải chi tiết tính toán:', error);
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Lỗi khi tải dữ liệu.</td></tr>';
                    calculationDiv.innerHTML = '<div class="text-center text-danger">Lỗi khi tải dữ liệu.</div>';
                });
        }

        // Hàm format số
        function formatNumber(value, decimals = 0) {
            if (value === null || value === undefined || isNaN(value)) {
                if (decimals === 0) {
                    return '0';
                }
                return '0,' + '0'.repeat(decimals);
            }
            const num = parseFloat(value);
            if (decimals === 0) {
                return num.toLocaleString('vi-VN');
            }
            // Format với số thập phân
            const parts = num.toFixed(decimals).split('.');
            const integerPart = parseInt(parts[0]).toLocaleString('vi-VN');
            const decimalPart = parts[1];
            return integerPart + ',' + decimalPart;
        }

        // Delete button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-payrollrundetail-btn')) {
                const btn = e.target.closest('.delete-payrollrundetail-btn');
                const detailId = btn.dataset.detailId;
                const form = document.getElementById('deletePayrollRunDetailForm');
                form.action = '{{ route("accounting.payrollrundetail.destroy", ":id") }}'.replace(':id', detailId);
                document.getElementById('delete_teacher_name').textContent = btn.dataset.teacherName || 'này';
            }
        });
    });
</script>
@endpush

