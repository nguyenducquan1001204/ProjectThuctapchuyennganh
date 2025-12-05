@extends('layouts.accounting')

@section('title', 'Quản lý cấu hình thành phần lương')

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
                <h3 class="page-title">Quản lý cấu hình thành phần lương</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('accounting.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý cấu hình thành phần lương</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('accounting.payrollcomponentconfig.index') }}">
        <div class="row">
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã cấu hình</label>
                    <input type="text" name="search_id" class="form-control" placeholder="Tìm kiếm theo mã cấu hình ..." value="{{ request('search_id') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Thành phần lương</label>
                    <select name="search_componentid" class="form-control">
                        <option value="">-- Tất cả --</option>
                        @foreach($allComponents as $component)
                            <option value="{{ $component->componentid }}" {{ (string)request('search_componentid') === (string)$component->componentid ? 'selected' : '' }}>
                                {{ $component->componentname }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Ngày hiệu lực</label>
                    <input type="date" name="search_effectivedate" class="form-control" value="{{ request('search_effectivedate') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Trạng thái</label>
                    <select name="search_status" class="form-control">
                        <option value="">-- Tất cả --</option>
                        <option value="active" {{ request('search_status') === 'active' ? 'selected' : '' }}>Đang hiệu lực</option>
                        <option value="expired" {{ request('search_status') === 'expired' ? 'selected' : '' }}>Đã hết hạn</option>
                    </select>
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
                            <h3 class="page-title">Danh sách cấu hình thành phần lương</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPayrollComponentConfigModal">
                                <i class="fas fa-plus me-1"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped payrollcomponentconfig-table">
                        <thead class="student-thread">
                            <tr>
                                <th class="text-end" style="padding-right: 50px !important;">Mã cấu hình</th>
                                <th>Thành phần lương</th>
                                <th>Ngày hiệu lực</th>
                                <th>Ngày hết hạn</th>
                                <th>Giá trị</th>
                                <th class="description-column">Ghi chú</th>
                                <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($configs as $config)
                                <tr>
                                    <td style="padding-left: 100px !important;">{{ $config->configid }}</td>
                                    <td>{{ $config->component->componentname ?? '-' }}</td>
                                    <td>{{ $config->effectivedate ? $config->effectivedate->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($config->expirationdate)
                                            {{ $config->expirationdate->format('d/m/Y') }}
                                        @else
                                            <span class="badge bg-success">Đang hiệu lực</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            // Hiển thị giá trị nào có dữ liệu trong database (không null)
                                            // Ưu tiên: percentagevalue -> defaultcoefficient -> fixedamount
                                            // Dùng dấu chấm (.) cho phần thập phân giống database
                                            if ($config->percentagevalue !== null) {
                                                echo number_format($config->percentagevalue, 2, '.', ',') . '%';
                                            } elseif ($config->defaultcoefficient !== null) {
                                                echo number_format($config->defaultcoefficient, 4, '.', ',');
                                            } elseif ($config->fixedamount !== null) {
                                                echo number_format($config->fixedamount, 0, '.', ',') . ' đ';
                                            } else {
                                                echo '-';
                                            }
                                        @endphp
                                    </td>
                                    <td class="description-column" title="{{ $config->note ?? '-' }}">
                                        @if($config->note)
                                            {{ Str::limit($config->note, 50, '...') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group payrollcomponentconfig-actions" role="group">
                                            <a href="#"
                                               class="btn btn-warning btn-sm rounded-pill me-1 text-white view-payrollcomponentconfig-btn"
                                               data-bs-toggle="modal" data-bs-target="#view_payrollcomponentconfig"
                                               title="Xem chi tiết"
                                               data-config-id="{{ $config->configid }}"
                                               data-component-name="{{ htmlspecialchars($config->component->componentname ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-component-method="{{ htmlspecialchars($config->component->calculationmethod ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-effective-date="{{ $config->effectivedate ? $config->effectivedate->format('Y-m-d') : '' }}"
                                               data-expiration-date="{{ $config->expirationdate ? $config->expirationdate->format('Y-m-d') : '' }}"
                                               data-default-coefficient="{{ $config->defaultcoefficient ?? '' }}"
                                               data-percentage-value="{{ $config->percentagevalue ?? '' }}"
                                               data-fixed-amount="{{ $config->fixedamount ?? '' }}"
                                               data-note="{{ htmlspecialchars($config->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#"
                                               class="btn btn-success btn-sm rounded-pill me-1 text-white edit-payrollcomponentconfig-btn"
                                               data-bs-toggle="modal" data-bs-target="#edit_payrollcomponentconfig"
                                               title="Chỉnh sửa"
                                               data-config-id="{{ $config->configid }}"
                                               data-component-id="{{ $config->componentid }}"
                                               data-component-name="{{ htmlspecialchars($config->component->componentname ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-component-method="{{ htmlspecialchars($config->component->calculationmethod ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-effective-date="{{ $config->effectivedate ? $config->effectivedate->format('Y-m-d') : '' }}"
                                               data-expiration-date="{{ $config->expirationdate ? $config->expirationdate->format('Y-m-d') : '' }}"
                                               data-default-coefficient="{{ $config->defaultcoefficient ?? '' }}"
                                               data-percentage-value="{{ $config->percentagevalue ?? '' }}"
                                               data-fixed-amount="{{ $config->fixedamount ?? '' }}"
                                               data-note="{{ htmlspecialchars($config->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="#"
                                               class="btn btn-danger btn-sm rounded-pill text-white delete-payrollcomponentconfig-btn"
                                               data-bs-toggle="modal" data-bs-target="#delete_payrollcomponentconfig"
                                               title="Xóa"
                                               data-config-id="{{ $config->configid }}"
                                               data-component-name="{{ htmlspecialchars($config->component->componentname ?? '-', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Chưa có dữ liệu cấu hình thành phần lương.</td>
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
<div class="modal fade payrollcomponentconfig-modal" id="createPayrollComponentConfigModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Thêm cấu hình thành phần lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('accounting.payrollcomponentconfig.store') }}" method="post" id="createPayrollComponentConfigForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Thành phần lương <span class="text-danger">*</span></label>
                        <select name="componentid" id="create_componentid" class="form-control" required onchange="updateValueFields('create', this.value)">
                            <option value="">-- Chọn thành phần lương --</option>
                            @foreach($componentsForCreate as $component)
                                <option value="{{ $component->componentid }}" 
                                        data-method="{{ $component->calculationmethod }}"
                                        {{ old('componentid') == $component->componentid ? 'selected' : '' }}>
                                    {{ $component->componentname }} ({{ $component->calculationmethod }})
                                </option>
                            @endforeach
                        </select>
                        @if($componentsForCreate->isEmpty())
                            <small class="text-muted">Tất cả thành phần lương đã có cấu hình đang hiệu lực.</small>
                        @endif
                        @error('componentid')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày hiệu lực <span class="text-danger">*</span></label>
                                <input type="date" name="effectivedate" id="create_effectivedate" class="form-control" required value="{{ old('effectivedate') }}" onchange="validateExpirationDate('create');">
                                @error('effectivedate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày hết hạn</label>
                                <input type="date" name="expirationdate" id="create_expirationdate" class="form-control" value="{{ old('expirationdate') }}" onchange="validateExpirationDate('create');">
                                @error('expirationdate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" id="create_coefficient_group" style="display: none;">
                        <label class="form-label">Hệ số mặc định</label>
                        <input type="number" name="defaultcoefficient" id="create_defaultcoefficient" class="form-control" step="0.0001" min="0" max="9999.9999" value="{{ old('defaultcoefficient') }}">
                        @error('defaultcoefficient')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="create_percentage_group" style="display: none;">
                        <label class="form-label">Tỷ lệ phần trăm (%)</label>
                        <input type="number" name="percentagevalue" id="create_percentagevalue" class="form-control" step="0.01" min="0" max="100" value="{{ old('percentagevalue') }}">
                        @error('percentagevalue')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="create_fixed_group" style="display: none;">
                        <label class="form-label">Số tiền cố định (đồng)</label>
                        <input type="number" name="fixedamount" id="create_fixedamount" class="form-control" step="1" min="0" value="{{ old('fixedamount') }}">
                        @error('fixedamount')
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
<div class="modal fade payrollcomponentconfig-modal" id="view_payrollcomponentconfig" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết cấu hình thành phần lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Mã cấu hình</label>
                    <input type="text" id="view_configid" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Thành phần lương</label>
                    <input type="text" id="view_componentname" class="form-control" readonly>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Ngày hiệu lực</label>
                            <input type="text" id="view_effectivedate" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Ngày hết hạn</label>
                            <input type="text" id="view_expirationdate" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="mb-3" id="view_value_group">
                    <label class="form-label">Giá trị</label>
                    <input type="text" id="view_value" class="form-control" readonly>
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
<div class="modal fade payrollcomponentconfig-modal" id="edit_payrollcomponentconfig" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chỉnh sửa cấu hình thành phần lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPayrollComponentConfigForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Thành phần lương <span class="text-danger">*</span></label>
                        <select name="componentid" id="edit_componentid" class="form-control" required onchange="updateValueFields('edit', this.value)">
                            <option value="">-- Chọn thành phần lương --</option>
                            @foreach($componentsForEdit as $component)
                                <option value="{{ $component->componentid }}" data-method="{{ $component->calculationmethod }}">
                                    {{ $component->componentname }} ({{ $component->calculationmethod }})
                                </option>
                            @endforeach
                        </select>
                        @error('componentid')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày hiệu lực <span class="text-danger">*</span></label>
                                <input type="date" name="effectivedate" id="edit_effectivedate" class="form-control" required onchange="validateExpirationDate('edit');">
                                @error('effectivedate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày hết hạn</label>
                                <input type="date" name="expirationdate" id="edit_expirationdate" class="form-control" onchange="validateExpirationDate('edit');">
                                @error('expirationdate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" id="edit_coefficient_group" style="display: none;">
                        <label class="form-label">Hệ số mặc định</label>
                        <input type="number" name="defaultcoefficient" id="edit_defaultcoefficient" class="form-control" step="0.0001" min="0" max="9999.9999">
                        @error('defaultcoefficient')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="edit_percentage_group" style="display: none;">
                        <label class="form-label">Tỷ lệ phần trăm (%)</label>
                        <input type="number" name="percentagevalue" id="edit_percentagevalue" class="form-control" step="0.01" min="0" max="100">
                        @error('percentagevalue')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="edit_fixed_group" style="display: none;">
                        <label class="form-label">Số tiền cố định (đồng)</label>
                        <input type="number" name="fixedamount" id="edit_fixedamount" class="form-control" step="1" min="0">
                        @error('fixedamount')
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
<div class="modal fade payrollcomponentconfig-modal modal-delete" id="delete_payrollcomponentconfig" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xóa cấu hình thành phần lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deletePayrollComponentConfigForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa cấu hình thành phần lương <strong id="delete_component_name">này</strong> không?</p>
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
    .payrollcomponentconfig-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        padding: 0.75rem;
    }

    .payrollcomponentconfig-table tbody td {
        font-size: 1rem;
    }

    .payrollcomponentconfig-table thead th {
        cursor: default !important;
    }

    .payrollcomponentconfig-table thead th.sorting::before,
    .payrollcomponentconfig-table thead th.sorting::after,
    .payrollcomponentconfig-table thead th.sorting_asc::before,
    .payrollcomponentconfig-table thead th.sorting_asc::after,
    .payrollcomponentconfig-table thead th.sorting_desc::before,
    .payrollcomponentconfig-table thead th.sorting_desc::after,
    .payrollcomponentconfig-table thead th.sorting_asc_disabled::before,
    .payrollcomponentconfig-table thead th.sorting_asc_disabled::after,
    .payrollcomponentconfig-table thead th.sorting_desc_disabled::before,
    .payrollcomponentconfig-table thead th.sorting_desc_disabled::after,
    .payrollcomponentconfig-table thead th::before,
    .payrollcomponentconfig-table thead th::after {
        display: none !important;
        content: none !important;
    }

    .payrollcomponentconfig-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .payrollcomponentconfig-table td {
        vertical-align: middle;
    }

    .payrollcomponentconfig-table th.description-column,
    .payrollcomponentconfig-table td.description-column {
        max-width: 200px;
        width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .payrollcomponentconfig-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .payrollcomponentconfig-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .payrollcomponentconfig-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .payrollcomponentconfig-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .payrollcomponentconfig-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }

    .payrollcomponentconfig-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    .payrollcomponentconfig-modal.modal-success .modal-header {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .payrollcomponentconfig-modal.modal-success .modal-footer {
        background: #ecfdf5;
    }

    .payrollcomponentconfig-modal.modal-error .modal-header {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
    }

    .payrollcomponentconfig-modal.modal-error .modal-footer {
        background: #fef2f2;
    }

    .payrollcomponentconfig-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }

    .payrollcomponentconfig-modal.modal-delete .modal-footer {
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
    // Hàm kiểm tra ngày hết hạn phải sau ngày hiệu lực
    function validateExpirationDate(formType) {
        const effectiveDateInput = document.getElementById(formType + '_effectivedate');
        const expirationDateInput = document.getElementById(formType + '_expirationdate');
        
        if (!effectiveDateInput || !expirationDateInput) return false;
        
        const effectiveDate = effectiveDateInput.value;
        const expirationDate = expirationDateInput.value;
        
        expirationDateInput.classList.remove('is-invalid', 'is-valid');
        
        if (expirationDate && effectiveDate) {
            const effective = new Date(effectiveDate);
            const expiration = new Date(expirationDate);
            
            if (expiration <= effective) {
                expirationDateInput.classList.add('is-invalid');
                const errorDiv = expirationDateInput.parentElement.querySelector('.expiration-date-error');
                if (errorDiv) {
                    errorDiv.textContent = 'Ngày hết hạn phải sau ngày hiệu lực';
                } else {
                    const errorEl = document.createElement('div');
                    errorEl.className = 'text-danger small expiration-date-error';
                    errorEl.textContent = 'Ngày hết hạn phải sau ngày hiệu lực';
                    expirationDateInput.parentElement.appendChild(errorEl);
                }
                return false;
            } else {
                expirationDateInput.classList.add('is-valid');
                const errorDiv = expirationDateInput.parentElement.querySelector('.expiration-date-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
                return true;
            }
        }
        
        return true; // Nếu không có ngày hết hạn hoặc ngày hiệu lực, coi như hợp lệ
    }

    // Hàm cập nhật hiển thị trường giá trị theo calculationmethod
    function updateValueFields(formType, componentId) {
        const select = document.getElementById(formType + '_componentid');
        if (!select || !componentId) {
            // Ẩn tất cả các trường giá trị
            document.getElementById(formType + '_coefficient_group').style.display = 'none';
            document.getElementById(formType + '_percentage_group').style.display = 'none';
            document.getElementById(formType + '_fixed_group').style.display = 'none';
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const method = selectedOption ? selectedOption.getAttribute('data-method') : '';

        // Ẩn tất cả các trường giá trị
        document.getElementById(formType + '_coefficient_group').style.display = 'none';
        document.getElementById(formType + '_percentage_group').style.display = 'none';
        document.getElementById(formType + '_fixed_group').style.display = 'none';

        // Hiển thị trường giá trị tương ứng
        if (method === 'Hệ số') {
            document.getElementById(formType + '_coefficient_group').style.display = 'block';
        } else if (method === 'Phần trăm') {
            document.getElementById(formType + '_percentage_group').style.display = 'block';
        } else if (method === 'Cố định') {
            document.getElementById(formType + '_fixed_group').style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('.datatable')) {
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

        // Khởi tạo hiển thị trường giá trị cho form create (nếu đã chọn component)
        const createComponentSelect = document.getElementById('create_componentid');
        if (createComponentSelect && createComponentSelect.value) {
            updateValueFields('create', createComponentSelect.value);
        }


        // View button handler
        document.addEventListener('click', function (e) {
            if (e.target.closest('.view-payrollcomponentconfig-btn')) {
                const btn = e.target.closest('.view-payrollcomponentconfig-btn');
                document.getElementById('view_configid').value = btn.dataset.configId || '';
                document.getElementById('view_componentname').value = btn.dataset.componentName || '';
                
                // Format ngày
                const effectiveDate = btn.dataset.effectiveDate || '';
                document.getElementById('view_effectivedate').value = effectiveDate ? new Date(effectiveDate + 'T00:00:00').toLocaleDateString('vi-VN') : '-';
                
                const expirationDate = btn.dataset.expirationDate || '';
                document.getElementById('view_expirationdate').value = expirationDate ? new Date(expirationDate + 'T00:00:00').toLocaleDateString('vi-VN') : 'Đang hiệu lực';

                // Hiển thị giá trị - dùng dấu chấm (.) cho phần thập phân giống database
                let valueText = '-';
                if (btn.dataset.percentageValue) {
                    valueText = parseFloat(btn.dataset.percentageValue).toFixed(2) + '%';
                } else if (btn.dataset.defaultCoefficient) {
                    valueText = parseFloat(btn.dataset.defaultCoefficient).toFixed(4);
                } else if (btn.dataset.fixedAmount) {
                    valueText = parseFloat(btn.dataset.fixedAmount).toFixed(0) + ' đ';
                }
                document.getElementById('view_value').value = valueText;

                document.getElementById('view_note').value = btn.dataset.note || '';
            }
        });

        // Edit button handler
        document.addEventListener('click', function (e) {
            if (e.target.closest('.edit-payrollcomponentconfig-btn')) {
                const btn = e.target.closest('.edit-payrollcomponentconfig-btn');
                const configId = btn.dataset.configId;
                const currentComponentId = btn.dataset.componentId || '';
                const currentComponentName = btn.dataset.componentName || '';
                const currentComponentMethod = btn.dataset.componentMethod || '';
                
                const form = document.getElementById('editPayrollComponentConfigForm');
                form.action = '{{ route("accounting.payrollcomponentconfig.update", ":id") }}'.replace(':id', configId);

                // Kiểm tra và thêm component hiện tại vào dropdown nếu chưa có
                const editComponentSelect = document.getElementById('edit_componentid');
                if (editComponentSelect && currentComponentId) {
                    const existingOption = editComponentSelect.querySelector(`option[value="${currentComponentId}"]`);
                    if (!existingOption) {
                        // Thêm component hiện tại vào dropdown
                        const newOption = document.createElement('option');
                        newOption.value = currentComponentId;
                        newOption.setAttribute('data-method', currentComponentMethod);
                        newOption.textContent = currentComponentName + ' (' + currentComponentMethod + ')';
                        editComponentSelect.appendChild(newOption);
                    }
                }

                document.getElementById('edit_componentid').value = currentComponentId;
                document.getElementById('edit_effectivedate').value = btn.dataset.effectiveDate || '';
                document.getElementById('edit_expirationdate').value = btn.dataset.expirationDate || '';
                document.getElementById('edit_defaultcoefficient').value = btn.dataset.defaultCoefficient || '';
                document.getElementById('edit_percentagevalue').value = btn.dataset.percentageValue || '';
                document.getElementById('edit_fixedamount').value = btn.dataset.fixedAmount || '';
                document.getElementById('edit_note').value = btn.dataset.note || '';

                // Cập nhật hiển thị trường giá trị
                updateValueFields('edit', currentComponentId);
            }
        });

        // Delete button handler
        document.addEventListener('click', function (e) {
            if (e.target.closest('.delete-payrollcomponentconfig-btn')) {
                const btn = e.target.closest('.delete-payrollcomponentconfig-btn');
                const configId = btn.dataset.configId;
                const form = document.getElementById('deletePayrollComponentConfigForm');
                form.action = '{{ route("accounting.payrollcomponentconfig.destroy", ":id") }}'.replace(':id', configId);
                document.getElementById('delete_component_name').textContent = btn.dataset.componentName || 'này';
            }
        });

        // Validate trước khi submit form Create
        const createForm = document.getElementById('createPayrollComponentConfigForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                if (!validateExpirationDate('create')) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // Validate trước khi submit form Edit
        const editForm = document.getElementById('editPayrollComponentConfigForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                if (!validateExpirationDate('edit')) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    });
</script>
@endpush

