@extends('layouts.accounting')

@section('title', 'Quản lý đơn vị')

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
@if ($errors->any())
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
                <h3 class="page-title">Quản lý đơn vị</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('accounting.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý đơn vị</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('accounting.budgetspendingunit.index') }}">
        <div class="row">
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã đơn vị</label>
                    <input type="text" name="search_id" class="form-control" placeholder="Tìm kiếm theo mã đơn vị ..." value="{{ request('search_id') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Tên đơn vị</label>
                    <input type="text" name="search_name" class="form-control" placeholder="Tìm kiếm theo tên đơn vị ..." value="{{ request('search_name') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Địa chỉ</label>
                    <input type="text" name="search_address" class="form-control" placeholder="Tìm kiếm theo địa chỉ ..." value="{{ request('search_address') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã số thuế</label>
                    <input type="text" name="search_taxnumber" class="form-control" placeholder="Tìm kiếm theo mã số thuế ..." value="{{ request('search_taxnumber') }}">
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label class="form-label small text-muted" style="opacity: 0; visibility: hidden;">&nbsp;</label>
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
                            <h3 class="page-title">Danh sách đơn vị</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUnitModal">
                                <i class="fas fa-plus me-1"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped unit-table">
                        <thead class="student-thread">
                            <tr>
                                <th>Mã đơn vị</th>
                                <th>Tên đơn vị</th>
                                <th>Địa chỉ</th>
                                <th>Mã số thuế</th>
                                <th>Ghi chú</th>
                                <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($units as $unit)
                                <tr>
                                    <td style="padding-left: 55px !important;">{{ $unit->unitid }}</td>
                                    <td>{{ $unit->unitname }}</td>
                                    <td>{{ $unit->address ?? '-' }}</td>
                                    <td>{{ $unit->taxnumber ?? '-' }}</td>
                                    <td>{{ Str::limit($unit->note ?? '-', 50) }}</td>
                                    <td class="text-end">
                                        <div class="btn-group unit-actions" role="group">
                                            <a href="#" class="btn btn-warning btn-sm rounded-pill me-1 text-white view-unit-btn"
                                               data-bs-toggle="modal" data-bs-target="#view_unit"
                                               title="Xem chi tiết"
                                               data-unit-id="{{ $unit->unitid }}"
                                               data-unit-name="{{ htmlspecialchars($unit->unitname, ENT_QUOTES, 'UTF-8') }}"
                                               data-unit-address="{{ htmlspecialchars($unit->address ?? '', ENT_QUOTES, 'UTF-8') }}"
                                               data-unit-taxnumber="{{ htmlspecialchars($unit->taxnumber ?? '', ENT_QUOTES, 'UTF-8') }}"
                                               data-unit-note="{{ htmlspecialchars($unit->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-success btn-sm rounded-pill me-1 text-white edit-unit-btn"
                                               data-bs-toggle="modal" data-bs-target="#edit_unit"
                                               title="Chỉnh sửa"
                                               data-unit-id="{{ $unit->unitid }}"
                                               data-unit-name="{{ htmlspecialchars($unit->unitname, ENT_QUOTES, 'UTF-8') }}"
                                               data-unit-address="{{ htmlspecialchars($unit->address ?? '', ENT_QUOTES, 'UTF-8') }}"
                                               data-unit-taxnumber="{{ htmlspecialchars($unit->taxnumber ?? '', ENT_QUOTES, 'UTF-8') }}"
                                               data-unit-note="{{ htmlspecialchars($unit->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="#" class="btn btn-danger btn-sm rounded-pill text-white delete-unit-btn"
                                               data-bs-toggle="modal" data-bs-target="#delete_unit"
                                               title="Xóa"
                                               data-unit-id="{{ $unit->unitid }}"
                                               data-unit-name="{{ htmlspecialchars($unit->unitname, ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Chưa có dữ liệu đơn vị.</td>
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
<div class="modal fade unit-modal" id="createUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Thêm đơn vị mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('accounting.budgetspendingunit.store') }}" method="post" id="createUnitForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên đơn vị <span class="text-danger">*</span></label>
                        <input type="text" name="unitname" id="create_unitname" class="form-control" required autofocus
                               value="{{ old('unitname') }}" maxlength="255" minlength="3"
                               oninput="validateUnitName(this, 'create')">
                        <div id="create_unitname_error" class="invalid-feedback"></div>
                        @error('unitname')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="address" id="create_address" class="form-control"
                               value="{{ old('address') }}" maxlength="255"
                               oninput="validateAddress(this, 'create')">
                        <div id="create_address_error" class="invalid-feedback"></div>
                        @error('address')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mã số thuế <span class="text-danger">*</span></label>
                        <input type="text" name="taxnumber" id="create_taxnumber" class="form-control" required
                               value="{{ old('taxnumber') }}" maxlength="10" pattern="[0-9]{10}"
                               placeholder="Nhập 10 chữ số" oninput="validateTaxNumber(this, 'create')">
                        <div id="create_taxnumber_error" class="invalid-feedback"></div>
                        @error('taxnumber')
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
<div class="modal fade unit-modal" id="view_unit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết đơn vị</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Mã đơn vị</label>
                    <input type="text" id="view_unitid" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tên đơn vị</label>
                    <input type="text" id="view_unitname" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" id="view_address" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mã số thuế</label>
                    <input type="text" id="view_taxnumber" class="form-control" readonly>
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
<div class="modal fade unit-modal" id="edit_unit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chỉnh sửa đơn vị</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUnitForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên đơn vị <span class="text-danger">*</span></label>
                        <input type="text" name="unitname" id="edit_unitname" class="form-control" required maxlength="255" minlength="3"
                               oninput="validateUnitName(this, 'edit')">
                        <div id="edit_unitname_error" class="invalid-feedback"></div>
                        @error('unitname')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="address" id="edit_address" class="form-control" maxlength="255"
                               oninput="validateAddress(this, 'edit')">
                        <div id="edit_address_error" class="invalid-feedback"></div>
                        @error('address')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mã số thuế <span class="text-danger">*</span></label>
                        <input type="text" name="taxnumber" id="edit_taxnumber" class="form-control" required
                               maxlength="10" pattern="[0-9]{10}" placeholder="Nhập 10 chữ số"
                               oninput="validateTaxNumber(this, 'edit')">
                        <div id="edit_taxnumber_error" class="invalid-feedback"></div>
                        @error('taxnumber')
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
<div class="modal fade unit-modal modal-delete" id="delete_unit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xóa đơn vị</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteUnitForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa đơn vị <strong id="delete_unit_name">này</strong> không?</p>
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
    .unit-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        cursor: default !important;
        padding: 0.75rem;
    }
    
    .unit-table tbody td {
        font-size: 1rem;
    }
    
    .unit-table thead th.sorting::before,
    .unit-table thead th.sorting::after,
    .unit-table thead th.sorting_asc::before,
    .unit-table thead th.sorting_asc::after,
    .unit-table thead th.sorting_desc::before,
    .unit-table thead th.sorting_desc::after,
    .unit-table thead th.sorting_asc_disabled::before,
    .unit-table thead th.sorting_asc_disabled::after,
    .unit-table thead th.sorting_desc_disabled::before,
    .unit-table thead th.sorting_desc_disabled::after,
    .unit-table thead th::before,
    .unit-table thead th::after {
        display: none !important;
        content: none !important;
    }

    .unit-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .unit-table td {
        vertical-align: middle;
    }

    .unit-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .unit-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .unit-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .unit-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .unit-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }

    .unit-modal.modal-success .modal-header {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .unit-modal.modal-success .modal-footer {
        background: #ecfdf5;
        justify-content: center;
    }

    .unit-modal.modal-success .modal-footer .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
    }

    .unit-modal.modal-success .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #059669, #047857);
    }

    .unit-modal.modal-error .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .unit-modal.modal-error .modal-footer {
        background: #fef2f2;
        justify-content: center;
    }

    .unit-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .unit-modal.modal-delete .modal-footer {
        background: #fef2f2;
    }

    .unit-modal .form-label {
        font-weight: 500;
        color: #1f2937;
    }

    .unit-modal .form-control,
    .unit-modal .form-select {
        border-radius: 0.65rem;
        border-color: #dbe3f2;
        padding: 0.6rem 0.85rem;
        box-shadow: none;
    }

    .unit-modal .form-control.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .unit-modal .form-control.is-valid {
        border-color: #198754;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .unit-modal .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .unit-modal textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .unit-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    .unit-actions .btn {
        transition: all .2s ease;
    }

    .unit-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(148, 163, 184, 0.25);
    }

    .unit-table th.text-end,
    .unit-table td.text-end {
        padding-left: 30px !important;
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
            if (e.target.closest('.view-unit-btn')) {
                const btn = e.target.closest('.view-unit-btn');
                document.getElementById('view_unitid').value = btn.dataset.unitId || '';
                document.getElementById('view_unitname').value = btn.dataset.unitName || '';
                document.getElementById('view_address').value = btn.dataset.unitAddress || '';
                document.getElementById('view_taxnumber').value = btn.dataset.unitTaxnumber || '';
                document.getElementById('view_note').value = btn.dataset.unitNote || '';
            }
        });

        // Edit button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-unit-btn')) {
                const btn = e.target.closest('.edit-unit-btn');
                const unitId = btn.dataset.unitId;
                const form = document.getElementById('editUnitForm');
                form.action = '{{ route("accounting.budgetspendingunit.update", ":id") }}'.replace(':id', unitId);
                document.getElementById('edit_unitname').value = btn.dataset.unitName || '';
                document.getElementById('edit_address').value = btn.dataset.unitAddress || '';
                document.getElementById('edit_taxnumber').value = btn.dataset.unitTaxnumber || '';
                document.getElementById('edit_note').value = btn.dataset.unitNote || '';
            }
        });

        // Delete button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-unit-btn')) {
                const btn = e.target.closest('.delete-unit-btn');
                const unitId = btn.dataset.unitId;
                const form = document.getElementById('deleteUnitForm');
                form.action = '{{ route("accounting.budgetspendingunit.destroy", ":id") }}'.replace(':id', unitId);
                document.getElementById('delete_unit_name').textContent = btn.dataset.unitName || 'này';
            }
        });
    });

    // Validate tên đơn vị real-time
    function validateUnitName(input, formType) {
        const value = input.value.trim();
        const errorDiv = document.getElementById(formType + '_unitname_error');
        let isValid = true;
        let errorMessage = '';

        // Xóa class cũ
        input.classList.remove('is-invalid', 'is-valid');

        // Kiểm tra độ dài
        if (value.length > 0 && value.length < 3) {
            isValid = false;
            errorMessage = 'Tên đơn vị phải có ít nhất 3 ký tự';
        } else if (value.length > 255) {
            isValid = false;
            errorMessage = 'Tên đơn vị không được vượt quá 255 ký tự';
        }
        // Kiểm tra định dạng (regex)
        else if (value.length > 0 && !/^[\p{L}\p{N}\s,.\-()]+$/u.test(value)) {
            isValid = false;
            errorMessage = 'Tên đơn vị chỉ được chứa chữ cái, số, khoảng trắng và các ký tự: , . - ( )';
        }
        // Kiểm tra không được có nhiều khoảng trắng liên tiếp
        else if (value.length > 0 && /\s{2,}/.test(value)) {
            isValid = false;
            errorMessage = 'Tên đơn vị không được có nhiều khoảng trắng liên tiếp';
        }
        // Kiểm tra chuỗi vô nghĩa
        else if (value.length > 0 && !isValidText(value)) {
            isValid = false;
            errorMessage = 'Tên đơn vị phải có ý nghĩa, không được chứa các chuỗi vô nghĩa.';
        }

        // Hiển thị lỗi hoặc thành công
        if (value.length > 0) {
            if (isValid) {
                input.classList.add('is-valid');
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            } else {
                input.classList.add('is-invalid');
                errorDiv.textContent = errorMessage;
                errorDiv.style.display = 'block';
            }
        } else {
            input.classList.remove('is-invalid', 'is-valid');
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';
        }
    }

    // Validate địa chỉ real-time
    function validateAddress(input, formType) {
        const value = input.value.trim();
        const errorDiv = document.getElementById(formType + '_address_error');
        let isValid = true;
        let errorMessage = '';

        // Xóa class cũ
        input.classList.remove('is-invalid', 'is-valid');

        // Nếu có nhập địa chỉ, kiểm tra định dạng
        if (value.length > 0) {
            if (value.length > 255) {
                isValid = false;
                errorMessage = 'Địa chỉ không được vượt quá 255 ký tự';
            }
            // Kiểm tra định dạng (regex)
            else if (!/^[\p{L}\p{N}\s,.\-()]+$/u.test(value)) {
                isValid = false;
                errorMessage = 'Địa chỉ chỉ được chứa chữ cái, số, khoảng trắng và các ký tự: , . - ( )';
            }
            // Kiểm tra không được có nhiều khoảng trắng liên tiếp
            else if (/\s{2,}/.test(value)) {
                isValid = false;
                errorMessage = 'Địa chỉ không được có nhiều khoảng trắng liên tiếp';
            }
            // Kiểm tra chuỗi vô nghĩa
            else if (!isValidText(value)) {
                isValid = false;
                errorMessage = 'Địa chỉ phải có ý nghĩa, không được chứa các chuỗi vô nghĩa.';
            }
        }

        // Hiển thị lỗi hoặc thành công
        if (value.length > 0) {
            if (isValid) {
                input.classList.add('is-valid');
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            } else {
                input.classList.add('is-invalid');
                errorDiv.textContent = errorMessage;
                errorDiv.style.display = 'block';
            }
        } else {
            input.classList.remove('is-invalid', 'is-valid');
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';
        }
    }

    // Kiểm tra xem chuỗi có ý nghĩa hay không (giống logic PHP)
    function isValidText(text) {
        if (!text || text.trim().length === 0) {
            return true;
        }

        // Loại bỏ các ký tự đặc biệt và số để chỉ kiểm tra chữ cái
        // Giữ lại chữ cái tiếng Việt và khoảng trắng
        const cleanedText = text.replace(/[^a-zA-ZàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ ]/gu, '');
        
        // Nếu chỉ còn số hoặc ký tự đặc biệt, không hợp lệ
        if (!cleanedText || cleanedText.trim().length === 0) {
            return false;
        }

        // Tách thành các từ
        const words = cleanedText.trim().split(/\s+/).filter(w => w.length > 0);
        
        let validWordsCount = 0;
        
        for (const word of words) {
            const trimmedWord = word.trim();
            if (!trimmedWord) {
                continue;
            }

            // Bỏ qua các từ quá ngắn (dưới 2 ký tự) hoặc là số
            const wordLength = trimmedWord.length;
            if (wordLength < 2 || !isNaN(trimmedWord)) {
                continue;
            }

            // Kiểm tra xem từ có phải là viết tắt không (tất cả chữ hoa, không có dấu, từ 2-10 ký tự)
            const isAbbreviation = (
                trimmedWord.toUpperCase() === trimmedWord &&
                !/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/iu.test(trimmedWord) &&
                /[A-Z]{2,}/.test(trimmedWord) &&
                wordLength <= 10
            );

            // Nếu là viết tắt hợp lệ (như THCS, ABC), cho phép
            if (isAbbreviation) {
                validWordsCount++;
                continue;
            }

            // Kiểm tra các ký tự lặp lại liên tiếp (như "fgfg" hoặc "dfdf")
            if (/(.)\1{2,}/u.test(trimmedWord)) {
                return false; // Có ký tự lặp lại 3 lần trở lên
            }

            // Kiểm tra các mẫu lặp lại (như "dgfgdfgd" có mẫu "dgf" lặp lại)
            // Chỉ kiểm tra cho từ không phải viết tắt
            if (wordLength >= 6) {
                // Kiểm tra các chuỗi con có lặp lại không
                for (let i = 2; i <= Math.floor(wordLength / 2); i++) {
                    const pattern = trimmedWord.substring(0, i);
                    const rest = trimmedWord.substring(i);
                    if (rest.includes(pattern)) {
                        // Kiểm tra xem có phải là lặp lại hoàn toàn không
                        const repeated = pattern.repeat(Math.ceil(wordLength / i));
                        if (repeated.substring(0, wordLength) === trimmedWord) {
                            return false;
                        }
                    }
                }
            }

            // Kiểm tra xem từ có chứa ít nhất một nguyên âm không
            const vowels = /[aeiouyàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđAEIOUYÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ]/iu;
            if (!vowels.test(trimmedWord)) {
                // Không có nguyên âm - có thể là viết tắt hoặc từ đặc biệt
                // Cho phép từ ngắn (2-3 ký tự) không có nguyên âm
                if (wordLength <= 3) {
                    validWordsCount++;
                    continue;
                }
                // Từ dài hơn 3 ký tự không có nguyên âm có khả năng là vô nghĩa
                // Nhưng không từ chối ngay, xem xét kỹ hơn
            }

            // Kiểm tra các phụ âm liên tiếp quá nhiều (nhiều hơn 4 cho từ dài)
            // Chỉ kiểm tra cho từ không phải viết tắt
            if (/[bcdfghjklmnpqrstvwxzBCDFGHJKLMNPQRSTVWXZ]{5,}/u.test(trimmedWord)) {
                return false;
            }

            validWordsCount++;
        }

        // Phải có ít nhất một từ hợp lệ
        return validWordsCount > 0;
    }

    // Validate mã số thuế real-time (10 chữ số)
    function validateTaxNumber(input, formType) {
        const value = input.value.trim();
        const errorDiv = document.getElementById(formType + '_taxnumber_error');
        let isValid = true;
        let errorMessage = '';

        // Xóa class cũ
        input.classList.remove('is-invalid', 'is-valid');

        // Chỉ cho phép số
        const numbersOnly = value.replace(/\D/g, '');
        if (value !== numbersOnly) {
            input.value = numbersOnly;
        }

        // Kiểm tra độ dài
        if (numbersOnly.length > 0 && numbersOnly.length !== 10) {
            isValid = false;
            errorMessage = 'Mã số thuế phải có đúng 10 chữ số';
        }
        // Kiểm tra định dạng
        else if (numbersOnly.length > 0 && !/^\d{10}$/.test(numbersOnly)) {
            isValid = false;
            errorMessage = 'Mã số thuế phải là số có đúng 10 chữ số';
        }

        // Hiển thị lỗi hoặc thành công
        if (numbersOnly.length > 0) {
            if (isValid && numbersOnly.length === 10) {
                input.classList.add('is-valid');
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            } else {
                input.classList.add('is-invalid');
                errorDiv.textContent = errorMessage || 'Mã số thuế phải có đúng 10 chữ số';
                errorDiv.style.display = 'block';
            }
        } else {
            input.classList.remove('is-invalid', 'is-valid');
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';
        }
    }

    // Validate form trước khi submit
    document.getElementById('createUnitForm')?.addEventListener('submit', function(e) {
        const unitnameInput = document.getElementById('create_unitname');
        const addressInput = document.getElementById('create_address');
        const taxnumberInput = document.getElementById('create_taxnumber');
        
        validateUnitName(unitnameInput, 'create');
        validateAddress(addressInput, 'create');
        validateTaxNumber(taxnumberInput, 'create');
        
        if (unitnameInput.classList.contains('is-invalid') || 
            addressInput.classList.contains('is-invalid') || 
            taxnumberInput.classList.contains('is-invalid') || 
            taxnumberInput.value.length !== 10) {
            e.preventDefault();
            return false;
        }
    });

    document.getElementById('editUnitForm')?.addEventListener('submit', function(e) {
        const unitnameInput = document.getElementById('edit_unitname');
        const addressInput = document.getElementById('edit_address');
        const taxnumberInput = document.getElementById('edit_taxnumber');
        
        validateUnitName(unitnameInput, 'edit');
        validateAddress(addressInput, 'edit');
        validateTaxNumber(taxnumberInput, 'edit');
        
        if (unitnameInput.classList.contains('is-invalid') || 
            addressInput.classList.contains('is-invalid') || 
            taxnumberInput.classList.contains('is-invalid') || 
            taxnumberInput.value.length !== 10) {
            e.preventDefault();
            return false;
        }
    });
</script>
@endpush

