@extends('layouts.accounting')

@section('title', 'Quản lý chi tiết thành phần trong bảng lương')

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
                <h3 class="page-title">Quản lý chi tiết thành phần trong bảng lương</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('accounting.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý chi tiết thành phần trong bảng lương</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('accounting.payrollrundetailcomponent.index') }}">
        <div class="row">
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã chi tiết</label>
                    <input type="text" name="search_id" class="form-control" placeholder="Tìm kiếm theo mã ..." value="{{ request('search_id') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Chi tiết bảng lương</label>
                    <select name="search_detailid" class="form-control">
                        <option value="">-- Tất cả --</option>
                        @foreach($allPayrollRunDetails as $detail)
                            <option value="{{ $detail->detailid }}" {{ (string)request('search_detailid') === (string)$detail->detailid ? 'selected' : '' }}>
                                #{{ $detail->detailid }} - {{ $detail->teacher ? $detail->teacher->fullname : '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
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
                    <label class="form-label small text-muted">Số tiền từ</label>
                    <input type="number" name="search_calculatedamount_from" class="form-control" step="0.01" min="0" placeholder="Từ" value="{{ request('search_calculatedamount_from') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Số tiền đến</label>
                    <input type="number" name="search_calculatedamount_to" class="form-control" step="0.01" min="0" placeholder="Đến" value="{{ request('search_calculatedamount_to') }}">
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
                            <h3 class="page-title">Danh sách chi tiết thành phần trong bảng lương</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPayrollRunDetailComponentModal" onclick="resetCreateForm()">
                                <i class="fas fa-plus me-1"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped payrollrundetailcomponent-table">
                        <thead class="student-thread">
                            <tr>
                                <th class="text-center" style="padding-right: 50px !important;">Mã chi tiết</th>
                                <th>Chi tiết bảng lương</th>
                                <th>Thành phần lương</th>
                                <th class="text-center">Hệ số đã sử dụng</th>
                                <th class="text-center">Tỷ lệ % đã sử dụng</th>
                                <th class="text-end">Số tiền đã tính</th>
                                <th class="description-column">Ghi chú</th>
                                <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrollRunDetailComponents as $component)
                                <tr>
                                    <td style="padding-left: 90px !important;">{{ $component->detailcomponentid }}</td>
                                    <td>
                                        @if($component->detail && $component->detail->teacher)
                                            #{{ $component->detail->detailid }} - {{ $component->detail->teacher->fullname }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $component->component ? $component->component->componentname : '-' }}</td>
                                    <td class="text-center">
                                        {{ $component->appliedcoefficient ? number_format($component->appliedcoefficient, 4, '.', '') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $component->appliedpercentage ? number_format($component->appliedpercentage, 4, '.', '') . '%' : '-' }}
                                    </td>
                                    <td class="text-end">{{ number_format($component->calculatedamount, 0, '.', ',') }} đ</td>
                                    <td class="description-column" title="{{ $component->note ?? '-' }}">
                                        @if($component->note)
                                            {{ Str::limit($component->note, 50, '...') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group payrollrundetailcomponent-actions" role="group">
                                            <a href="#"
                                               class="btn btn-warning btn-sm rounded-pill me-1 text-white view-payrollrundetailcomponent-btn"
                                               data-bs-toggle="modal" data-bs-target="#view_payrollrundetailcomponent"
                                               title="Xem chi tiết"
                                               data-detail-component-id="{{ $component->detailcomponentid }}"
                                               data-detail="{{ $component->detail && $component->detail->teacher ? '#'.$component->detail->detailid.' - '.$component->detail->teacher->fullname : '-' }}"
                                               data-component-name="{{ htmlspecialchars($component->component ? $component->component->componentname : '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-applied-coefficient="{{ $component->appliedcoefficient ?? '' }}"
                                               data-applied-percentage="{{ $component->appliedpercentage ?? '' }}"
                                               data-calculated-amount="{{ number_format($component->calculatedamount, 2, '.', '') }}"
                                               data-note="{{ htmlspecialchars($component->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#"
                                               class="btn btn-success btn-sm rounded-pill me-1 text-white edit-payrollrundetailcomponent-btn"
                                               data-bs-toggle="modal" data-bs-target="#edit_payrollrundetailcomponent"
                                               title="Chỉnh sửa"
                                               data-detail-component-id="{{ $component->detailcomponentid }}"
                                               data-detail-id="{{ $component->detailid ?? '' }}"
                                               data-component-id="{{ $component->componentid ?? '' }}"
                                               data-applied-coefficient="{{ $component->appliedcoefficient ?? '' }}"
                                               data-applied-percentage="{{ $component->appliedpercentage ?? '' }}"
                                               data-calculated-amount="{{ number_format($component->calculatedamount, 2, '.', '') }}"
                                               data-note="{{ htmlspecialchars($component->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="#"
                                               class="btn btn-danger btn-sm rounded-pill text-white delete-payrollrundetailcomponent-btn"
                                               data-bs-toggle="modal" data-bs-target="#delete_payrollrundetailcomponent"
                                               title="Xóa"
                                               data-detail-component-id="{{ $component->detailcomponentid }}"
                                               data-component-name="{{ htmlspecialchars($component->component ? $component->component->componentname : '-', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Chưa có dữ liệu chi tiết thành phần trong bảng lương.</td>
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
<div class="modal fade payrollrundetailcomponent-modal" id="createPayrollRunDetailComponentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Thêm chi tiết thành phần trong bảng lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('accounting.payrollrundetailcomponent.store') }}" method="post" id="createPayrollRunDetailComponentForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Chi tiết bảng lương <span class="text-danger">*</span></label>
                        <select name="detailid" id="create_detailid" class="form-control" required>
                            <option value="">-- Chọn chi tiết bảng lương --</option>
                            @foreach($allPayrollRunDetails as $detail)
                                <option value="{{ $detail->detailid }}" {{ old('detailid') == $detail->detailid ? 'selected' : '' }}>
                                    #{{ $detail->detailid }} - {{ $detail->teacher ? $detail->teacher->fullname : '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('detailid')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thành phần lương <span class="text-danger">*</span></label>
                        <select name="componentid" id="create_componentid" class="form-control" required>
                            <option value="">-- Chọn thành phần lương --</option>
                            @foreach($allComponents as $component)
                                <option value="{{ $component->componentid }}" {{ old('componentid') == $component->componentid ? 'selected' : '' }}>
                                    {{ $component->componentname }}
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
                                <label class="form-label">Hệ số đã sử dụng</label>
                                <input type="number" name="appliedcoefficient" id="create_appliedcoefficient" class="form-control" step="0.0001" min="0" value="{{ old('appliedcoefficient') }}">
                                <small class="text-muted">Nếu thành phần lương tính theo hệ số</small>
                                @error('appliedcoefficient')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tỷ lệ phần trăm đã sử dụng</label>
                                <input type="number" name="appliedpercentage" id="create_appliedpercentage" class="form-control" step="0.0001" min="0" max="100" value="{{ old('appliedpercentage') }}">
                                <small class="text-muted">Nếu thành phần lương tính theo phần trăm</small>
                                @error('appliedpercentage')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số tiền đã tính toán <span class="text-danger">*</span></label>
                        <input type="number" name="calculatedamount" id="create_calculatedamount" class="form-control" step="0.01" required value="{{ old('calculatedamount') }}">
                        @error('calculatedamount')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="note" id="create_note" rows="3" class="form-control" placeholder="Nhập ghi chú về cách tính hoặc lý do đặc biệt (không bắt buộc)">{{ old('note') }}</textarea>
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
<div class="modal fade payrollrundetailcomponent-modal" id="view_payrollrundetailcomponent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết thành phần trong bảng lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Mã chi tiết thành phần</label>
                    <input type="text" id="view_detailcomponentid" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Chi tiết bảng lương</label>
                    <input type="text" id="view_detail" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Thành phần lương</label>
                    <input type="text" id="view_componentname" class="form-control" readonly>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Hệ số đã sử dụng</label>
                            <input type="text" id="view_appliedcoefficient" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tỷ lệ phần trăm đã sử dụng</label>
                            <input type="text" id="view_appliedpercentage" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số tiền đã tính toán</label>
                    <input type="text" id="view_calculatedamount" class="form-control" readonly>
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
<div class="modal fade payrollrundetailcomponent-modal" id="edit_payrollrundetailcomponent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chỉnh sửa chi tiết thành phần trong bảng lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPayrollRunDetailComponentForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Chi tiết bảng lương <span class="text-danger">*</span></label>
                        <select name="detailid" id="edit_detailid" class="form-control" required>
                            <option value="">-- Chọn chi tiết bảng lương --</option>
                            @foreach($allPayrollRunDetails as $detail)
                                <option value="{{ $detail->detailid }}">
                                    #{{ $detail->detailid }} - {{ $detail->teacher ? $detail->teacher->fullname : '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('detailid')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thành phần lương <span class="text-danger">*</span></label>
                        <select name="componentid" id="edit_componentid" class="form-control" required>
                            <option value="">-- Chọn thành phần lương --</option>
                            @foreach($allComponents as $component)
                                <option value="{{ $component->componentid }}">
                                    {{ $component->componentname }}
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
                                <label class="form-label">Hệ số đã sử dụng</label>
                                <input type="number" name="appliedcoefficient" id="edit_appliedcoefficient" class="form-control" step="0.0001" min="0">
                                <small class="text-muted">Nếu thành phần lương tính theo hệ số</small>
                                @error('appliedcoefficient')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tỷ lệ phần trăm đã sử dụng</label>
                                <input type="number" name="appliedpercentage" id="edit_appliedpercentage" class="form-control" step="0.0001" min="0" max="100">
                                <small class="text-muted">Nếu thành phần lương tính theo phần trăm</small>
                                @error('appliedpercentage')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số tiền đã tính toán <span class="text-danger">*</span></label>
                        <input type="number" name="calculatedamount" id="edit_calculatedamount" class="form-control" step="0.01" required>
                        @error('calculatedamount')
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
<div class="modal fade payrollrundetailcomponent-modal modal-delete" id="delete_payrollrundetailcomponent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xóa chi tiết thành phần trong bảng lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deletePayrollRunDetailComponentForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa chi tiết thành phần lương <strong id="delete_component_name">này</strong> không?</p>
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
    .payrollrundetailcomponent-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        padding: 0.75rem;
    }

    .payrollrundetailcomponent-table tbody td {
        font-size: 1rem;
    }

    .payrollrundetailcomponent-table thead th {
        cursor: default !important;
    }

    .payrollrundetailcomponent-table thead th.sorting::before,
    .payrollrundetailcomponent-table thead th.sorting::after,
    .payrollrundetailcomponent-table thead th.sorting_asc::before,
    .payrollrundetailcomponent-table thead th.sorting_asc::after,
    .payrollrundetailcomponent-table thead th.sorting_desc::before,
    .payrollrundetailcomponent-table thead th.sorting_desc::after,
    .payrollrundetailcomponent-table thead th.sorting_asc_disabled::before,
    .payrollrundetailcomponent-table thead th.sorting_asc_disabled::after,
    .payrollrundetailcomponent-table thead th.sorting_desc_disabled::before,
    .payrollrundetailcomponent-table thead th.sorting_desc_disabled::after,
    .payrollrundetailcomponent-table thead th::before,
    .payrollrundetailcomponent-table thead th::after {
        display: none !important;
        content: none !important;
    }

    .payrollrundetailcomponent-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .payrollrundetailcomponent-table td {
        vertical-align: middle;
    }

    .payrollrundetailcomponent-table th.description-column,
    .payrollrundetailcomponent-table td.description-column {
        max-width: 200px;
        width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .payrollrundetailcomponent-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .payrollrundetailcomponent-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .payrollrundetailcomponent-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .payrollrundetailcomponent-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .payrollrundetailcomponent-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }

    .payrollrundetailcomponent-modal .form-control,
    .payrollrundetailcomponent-modal .form-select {
        border-radius: 0.65rem;
        border-color: #dbe3f2;
        padding: 0.6rem 0.85rem;
        box-shadow: none;
    }

    .payrollrundetailcomponent-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    .payrollrundetailcomponent-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }

    .payrollrundetailcomponent-modal.modal-delete .modal-footer {
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
    // Hàm reset form create
    function resetCreateForm() {
        document.getElementById('create_detailid').value = '';
        document.getElementById('create_componentid').value = '';
        document.getElementById('create_appliedcoefficient').value = '';
        document.getElementById('create_appliedpercentage').value = '';
        document.getElementById('create_calculatedamount').value = '';
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
            if (e.target.closest('.view-payrollrundetailcomponent-btn')) {
                const btn = e.target.closest('.view-payrollrundetailcomponent-btn');
                document.getElementById('view_detailcomponentid').value = btn.dataset.detailComponentId || '';
                document.getElementById('view_detail').value = btn.dataset.detail || '-';
                document.getElementById('view_componentname').value = btn.dataset.componentName || '-';
                
                const appliedCoefficient = btn.dataset.appliedCoefficient || '';
                if (appliedCoefficient) {
                    const coeff = parseFloat(appliedCoefficient);
                    document.getElementById('view_appliedcoefficient').value = !isNaN(coeff) ? coeff.toFixed(4) : appliedCoefficient;
                } else {
                    document.getElementById('view_appliedcoefficient').value = '-';
                }
                
                const appliedPercentage = btn.dataset.appliedPercentage || '';
                if (appliedPercentage) {
                    const perc = parseFloat(appliedPercentage);
                    document.getElementById('view_appliedpercentage').value = !isNaN(perc) ? perc.toFixed(4) + '%' : appliedPercentage + '%';
                } else {
                    document.getElementById('view_appliedpercentage').value = '-';
                }
                
                const calculatedAmount = parseFloat(btn.dataset.calculatedAmount || 0);
                document.getElementById('view_calculatedamount').value = calculatedAmount.toLocaleString('vi-VN') + ' đ';

                document.getElementById('view_note').value = btn.dataset.note || '';
            }
        });

        // Edit button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-payrollrundetailcomponent-btn')) {
                const btn = e.target.closest('.edit-payrollrundetailcomponent-btn');
                const detailComponentId = btn.dataset.detailComponentId;
                
                const form = document.getElementById('editPayrollRunDetailComponentForm');
                form.action = '{{ route("accounting.payrollrundetailcomponent.update", ":id") }}'.replace(':id', detailComponentId);

                document.getElementById('edit_detailid').value = btn.dataset.detailId || '';
                document.getElementById('edit_componentid').value = btn.dataset.componentId || '';
                
                const appliedCoefficient = btn.dataset.appliedCoefficient || '';
                document.getElementById('edit_appliedcoefficient').value = appliedCoefficient;
                
                const appliedPercentage = btn.dataset.appliedPercentage || '';
                document.getElementById('edit_appliedpercentage').value = appliedPercentage;
                
                const calculatedAmount = parseFloat(btn.dataset.calculatedAmount || 0);
                document.getElementById('edit_calculatedamount').value = calculatedAmount.toFixed(2);

                document.getElementById('edit_note').value = btn.dataset.note || '';
            }
        });

        // Delete button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-payrollrundetailcomponent-btn')) {
                const btn = e.target.closest('.delete-payrollrundetailcomponent-btn');
                const detailComponentId = btn.dataset.detailComponentId;
                const form = document.getElementById('deletePayrollRunDetailComponentForm');
                form.action = '{{ route("accounting.payrollrundetailcomponent.destroy", ":id") }}'.replace(':id', detailComponentId);
                document.getElementById('delete_component_name').textContent = btn.dataset.componentName || 'này';
            }
        });
    });
</script>
@endpush

