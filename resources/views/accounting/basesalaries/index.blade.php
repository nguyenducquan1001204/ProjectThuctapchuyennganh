@extends('layouts.accounting')

@section('title', 'Quản lý mức lương cơ bản')

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
                <h3 class="page-title">Quản lý mức lương cơ bản</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('accounting.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý mức lương cơ bản</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('accounting.basesalary.index') }}">
        <div class="row">
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã mức lương</label>
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
                            <h3 class="page-title">Danh sách mức lương cơ bản</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBaseSalaryModal">
                                <i class="fas fa-plus me-1"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped basesalary-table">
                        <thead class="student-thread">
                            <tr>
                                <th class="text-center" style="padding-right: 50px !important;">Mã mức lương</th>
                                <th>Đơn vị</th>
                                <th>Ngày hiệu lực</th>
                                <th>Ngày hết hạn</th>
                                <th class="text-center">Mức lương cơ bản</th>
                                <th class="description-column">Ghi chú</th>
                                <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($baseSalaries as $baseSalary)
                                <tr>
                                    <td style="padding-left: 90px !important;">{{ $baseSalary->basesalaryid }}</td>
                                    <td>{{ $baseSalary->unit ? $baseSalary->unit->unitname : 'Toàn hệ thống' }}</td>
                                    <td>{{ $baseSalary->effectivedate ? $baseSalary->effectivedate->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($baseSalary->expirationdate)
                                            {{ $baseSalary->expirationdate->format('d/m/Y') }}
                                        @else
                                            <span class="badge bg-success">Đang hiệu lực</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($baseSalary->basesalaryamount !== null)
                                            {{ number_format($baseSalary->basesalaryamount, 0, '.', ',') }} đ
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="description-column" title="{{ $baseSalary->note ?? '-' }}">
                                        @if($baseSalary->note)
                                            {{ Str::limit($baseSalary->note, 50, '...') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group basesalary-actions" role="group">
                                            <a href="#"
                                               class="btn btn-warning btn-sm rounded-pill me-1 text-white view-basesalary-btn"
                                               data-bs-toggle="modal" data-bs-target="#view_basesalary"
                                               title="Xem chi tiết"
                                               data-basesalary-id="{{ $baseSalary->basesalaryid }}"
                                               data-unit-name="{{ htmlspecialchars($baseSalary->unit ? $baseSalary->unit->unitname : 'Toàn hệ thống', ENT_QUOTES, 'UTF-8') }}"
                                               data-effective-date="{{ $baseSalary->effectivedate ? $baseSalary->effectivedate->format('Y-m-d') : '' }}"
                                               data-expiration-date="{{ $baseSalary->expirationdate ? $baseSalary->expirationdate->format('Y-m-d') : '' }}"
                                               data-basesalary-amount="{{ $baseSalary->basesalaryamount !== null ? number_format((float)$baseSalary->basesalaryamount, 0, '.', '') : '' }}"
                                               data-note="{{ htmlspecialchars($baseSalary->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$baseSalary->expirationdate)
                                            <a href="#"
                                               class="btn btn-info btn-sm rounded-pill me-1 text-white terminate-basesalary-btn"
                                               data-bs-toggle="modal" data-bs-target="#terminate_basesalary"
                                               title="Tạm kết thúc"
                                               data-basesalary-id="{{ $baseSalary->basesalaryid }}"
                                               data-unit-name="{{ htmlspecialchars($baseSalary->unit ? $baseSalary->unit->unitname : 'Toàn hệ thống', ENT_QUOTES, 'UTF-8') }}"
                                               data-basesalary-amount="{{ $baseSalary->basesalaryamount !== null ? number_format((float)$baseSalary->basesalaryamount, 0, '.', ',') : '' }}">
                                                <i class="fas fa-stop-circle"></i>
                                            </a>
                                            <a href="#"
                                               class="btn btn-success btn-sm rounded-pill me-1 text-white edit-basesalary-btn"
                                               data-bs-toggle="modal" data-bs-target="#edit_basesalary"
                                               title="Chỉnh sửa"
                                               data-basesalary-id="{{ $baseSalary->basesalaryid }}"
                                               data-unit-id="{{ $baseSalary->unitid ?? '' }}"
                                               data-effective-date="{{ $baseSalary->effectivedate ? $baseSalary->effectivedate->format('Y-m-d') : '' }}"
                                               data-expiration-date="{{ $baseSalary->expirationdate ? $baseSalary->expirationdate->format('Y-m-d') : '' }}"
                                               data-basesalary-amount="{{ $baseSalary->basesalaryamount !== null ? number_format((float)$baseSalary->basesalaryamount, 0, '.', '') : '' }}"
                                               data-note="{{ htmlspecialchars($baseSalary->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            @endif
                                            <a href="#"
                                               class="btn btn-danger btn-sm rounded-pill text-white delete-basesalary-btn"
                                               data-bs-toggle="modal" data-bs-target="#delete_basesalary"
                                               title="Xóa"
                                               data-basesalary-id="{{ $baseSalary->basesalaryid }}"
                                               data-unit-name="{{ htmlspecialchars($baseSalary->unit ? $baseSalary->unit->unitname : 'Toàn hệ thống', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Chưa có dữ liệu mức lương cơ bản.</td>
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
<div class="modal fade basesalary-modal" id="createBaseSalaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Thêm mức lương cơ bản</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('accounting.basesalary.store') }}" method="post" id="createBaseSalaryForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Đơn vị</label>
                        <select name="unitid" id="create_unitid" class="form-control">
                            <option value="">-- Chọn đơn vị (để trống nếu áp dụng toàn hệ thống) --</option>
                            @foreach($allUnits as $unit)
                                <option value="{{ $unit->unitid }}" {{ old('unitid') == $unit->unitid ? 'selected' : '' }}>
                                    {{ $unit->unitname }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Nếu để trống, mức lương này sẽ áp dụng cho toàn hệ thống</small>
                        @error('unitid')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày hiệu lực <span class="text-danger">*</span></label>
                                <input type="date" name="effectivedate" id="create_effectivedate" class="form-control" required value="{{ old('effectivedate') }}">
                                @error('effectivedate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày hết hạn</label>
                                <input type="date" name="expirationdate" id="create_expirationdate" class="form-control" value="{{ old('expirationdate') }}">
                                @error('expirationdate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mức lương cơ bản (đồng) <span class="text-danger">*</span></label>
                        <input type="text" name="basesalaryamount" id="create_basesalaryamount" class="form-control" 
                               pattern="[0-9]+" required value="{{ old('basesalaryamount') }}"
                               placeholder="Nhập mức lương cơ bản (tối thiểu 100,000)"
                               min="100000"
                               oninput="validateBaseSalaryAmount(this, 'create');">
                        <div id="create_basesalaryamount_error" class="text-danger small" style="display: none;"></div>
                        @error('basesalaryamount')
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
<div class="modal fade basesalary-modal" id="view_basesalary" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết mức lương cơ bản</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Mã mức lương</label>
                    <input type="text" id="view_basesalaryid" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Đơn vị</label>
                    <input type="text" id="view_unitname" class="form-control" readonly>
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
                <div class="mb-3">
                    <label class="form-label">Mức lương cơ bản (đồng)</label>
                    <input type="text" id="view_basesalaryamount" class="form-control" readonly>
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
<div class="modal fade basesalary-modal" id="edit_basesalary" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chỉnh sửa mức lương cơ bản</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBaseSalaryForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Đơn vị</label>
                        <select name="unitid" id="edit_unitid" class="form-control">
                            <option value="">-- Chọn đơn vị (để trống nếu áp dụng toàn hệ thống) --</option>
                            @foreach($allUnits as $unit)
                                <option value="{{ $unit->unitid }}">
                                    {{ $unit->unitname }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Nếu để trống, mức lương này sẽ áp dụng cho toàn hệ thống</small>
                        @error('unitid')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày hiệu lực <span class="text-danger">*</span></label>
                                <input type="date" name="effectivedate" id="edit_effectivedate" class="form-control" required>
                                @error('effectivedate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày hết hạn</label>
                                <input type="date" name="expirationdate" id="edit_expirationdate" class="form-control">
                                @error('expirationdate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mức lương cơ bản (đồng) <span class="text-danger">*</span></label>
                        <input type="text" name="basesalaryamount" id="edit_basesalaryamount" class="form-control" 
                               pattern="[0-9]+" required
                               placeholder="Nhập mức lương cơ bản (tối thiểu 100,000)"
                               min="100000"
                               oninput="validateBaseSalaryAmount(this, 'edit');">
                        <div id="edit_basesalaryamount_error" class="text-danger small" style="display: none;"></div>
                        @error('basesalaryamount')
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

<!-- Terminate Modal -->
<div class="modal fade basesalary-modal modal-terminate" id="terminate_basesalary" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <h5 class="modal-title">Tạm kết thúc mức lương cơ bản</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="terminateBaseSalaryForm" method="post">
                @csrf
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn tạm kết thúc mức lương cơ bản này không?</p>
                    <div class="alert alert-info mb-0">
                        <strong>Đơn vị:</strong> <span id="terminate_unit_name"></span><br>
                        <strong>Mức lương:</strong> <span id="terminate_basesalary_amount"></span> đ
                    </div>
                    <p class="mt-3 mb-0"><small class="text-muted">Sau khi kết thúc, bạn có thể tạo mức lương cơ bản mới cho đơn vị này.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-info text-white">Kết thúc</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade basesalary-modal modal-delete" id="delete_basesalary" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xóa mức lương cơ bản</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteBaseSalaryForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa mức lương cơ bản <strong id="delete_unit_name">này</strong> không?</p>
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
    .basesalary-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        padding: 0.75rem;
    }

    .basesalary-table tbody td {
        font-size: 1rem;
    }

    .basesalary-table thead th {
        cursor: default !important;
    }

    .basesalary-table thead th.sorting::before,
    .basesalary-table thead th.sorting::after,
    .basesalary-table thead th.sorting_asc::before,
    .basesalary-table thead th.sorting_asc::after,
    .basesalary-table thead th.sorting_desc::before,
    .basesalary-table thead th.sorting_desc::after,
    .basesalary-table thead th.sorting_asc_disabled::before,
    .basesalary-table thead th.sorting_asc_disabled::after,
    .basesalary-table thead th.sorting_desc_disabled::before,
    .basesalary-table thead th.sorting_desc_disabled::after,
    .basesalary-table thead th::before,
    .basesalary-table thead th::after {
        display: none !important;
        content: none !important;
    }

    .basesalary-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .basesalary-table td {
        vertical-align: middle;
    }

    .basesalary-table th.description-column,
    .basesalary-table td.description-column {
        max-width: 200px;
        width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .basesalary-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .basesalary-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .basesalary-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .basesalary-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .basesalary-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }

    .basesalary-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    .basesalary-modal.modal-success .modal-header {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .basesalary-modal.modal-success .modal-footer {
        background: #ecfdf5;
    }

    .basesalary-modal.modal-error .modal-header {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
    }

    .basesalary-modal.modal-error .modal-footer {
        background: #fef2f2;
    }

    .basesalary-modal.modal-terminate .modal-header {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .basesalary-modal.modal-terminate .modal-footer {
        background: #eff6ff;
    }

    .basesalary-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }

    .basesalary-modal.modal-delete .modal-footer {
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
    // Hàm kiểm tra mức lương cơ bản phải lớn hơn hoặc bằng 100,000
    function validateBaseSalaryAmount(input, formType) {
        // Chỉ cho phép nhập số
        input.value = input.value.replace(/[^0-9]/g, '');
        
        const value = input.value.trim();
        const errorDiv = document.getElementById(formType + '_basesalaryamount_error');
        const minAmount = 100000;
        
        input.classList.remove('is-invalid', 'is-valid');
        
        if (value === '') {
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            return false;
        }
        
        const numValue = parseInt(value, 10);
        
        if (isNaN(numValue) || numValue < minAmount) {
            input.classList.add('is-invalid');
            errorDiv.style.display = 'block';
            errorDiv.textContent = 'Mức lương cơ bản phải lớn hơn hoặc bằng 100,000 VNĐ';
            return false;
        } else {
            input.classList.add('is-valid');
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            return true;
        }
    }

    // Hàm kiểm tra mức lương cơ bản phải lớn hơn hoặc bằng 100,000
    function validateBaseSalaryAmount(input, formType) {
        // Chỉ cho phép nhập số
        input.value = input.value.replace(/[^0-9]/g, '');
        
        const value = input.value.trim();
        const errorDiv = document.getElementById(formType + '_basesalaryamount_error');
        const minAmount = 100000;
        
        input.classList.remove('is-invalid', 'is-valid');
        
        if (value === '') {
            if (errorDiv) {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
            }
            return false;
        }
        
        const numValue = parseInt(value, 10);
        
        if (isNaN(numValue) || numValue < minAmount) {
            input.classList.add('is-invalid');
            if (errorDiv) {
                errorDiv.style.display = 'block';
                errorDiv.textContent = 'Mức lương cơ bản phải lớn hơn hoặc bằng 100,000 VNĐ';
            }
            return false;
        } else {
            input.classList.add('is-valid');
            if (errorDiv) {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
            }
            return true;
        }
    }

    // Hàm kiểm tra ngày hết hạn phải sau ngày hiệu lực
    function validateExpirationDate(formType) {
        const effectiveDateInput = document.getElementById(formType + '_effectivedate');
        const expirationDateInput = document.getElementById(formType + '_expirationdate');
        
        if (!effectiveDateInput || !expirationDateInput) return;
        
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
            } else {
                expirationDateInput.classList.add('is-valid');
                const errorDiv = expirationDateInput.parentElement.querySelector('.expiration-date-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
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
        if (errorModal && (@json($errors->any()) || @json(session('error')))) {
            const modal = new bootstrap.Modal(errorModal);
            modal.show();
        }

        // Thêm validation cho ngày hết hạn (form create)
        const createEffectiveDate = document.getElementById('create_effectivedate');
        const createExpirationDate = document.getElementById('create_expirationdate');
        if (createEffectiveDate) {
            createEffectiveDate.addEventListener('change', function() {
                validateExpirationDate('create');
            });
        }
        if (createExpirationDate) {
            createExpirationDate.addEventListener('change', function() {
                validateExpirationDate('create');
            });
        }

        // Thêm validation cho ngày hết hạn (form edit)
        const editEffectiveDate = document.getElementById('edit_effectivedate');
        const editExpirationDate = document.getElementById('edit_expirationdate');
        if (editEffectiveDate) {
            editEffectiveDate.addEventListener('change', function() {
                validateExpirationDate('edit');
            });
        }
        if (editExpirationDate) {
            editExpirationDate.addEventListener('change', function() {
                validateExpirationDate('edit');
            });
        }

        // View button handler
        document.addEventListener('click', function (e) {
            if (e.target.closest('.view-basesalary-btn')) {
                const btn = e.target.closest('.view-basesalary-btn');
                document.getElementById('view_basesalaryid').value = btn.dataset.basesalaryId || '';
                document.getElementById('view_unitname').value = btn.dataset.unitName || '';
                
                // Format ngày
                const effectiveDate = btn.dataset.effectiveDate || '';
                document.getElementById('view_effectivedate').value = effectiveDate ? new Date(effectiveDate + 'T00:00:00').toLocaleDateString('vi-VN') : '-';
                
                const expirationDate = btn.dataset.expirationDate || '';
                document.getElementById('view_expirationdate').value = expirationDate ? new Date(expirationDate + 'T00:00:00').toLocaleDateString('vi-VN') : 'Đang hiệu lực';

                // Format mức lương
                const amount = btn.dataset.basesalaryAmount || '';
                if (amount) {
                    const numAmount = parseFloat(amount);
                    document.getElementById('view_basesalaryamount').value = !isNaN(numAmount) ? numAmount.toLocaleString('vi-VN') + ' đ' : amount + ' đ';
                } else {
                    document.getElementById('view_basesalaryamount').value = '-';
                }

                document.getElementById('view_note').value = btn.dataset.note || '';
            }
        });

        // Edit button handler
        document.addEventListener('click', function (e) {
            if (e.target.closest('.edit-basesalary-btn')) {
                const btn = e.target.closest('.edit-basesalary-btn');
                const basesalaryId = btn.dataset.basesalaryId;
                const currentUnitId = btn.dataset.unitId || '';
                
                const form = document.getElementById('editBaseSalaryForm');
                form.action = '{{ route("accounting.basesalary.update", ":id") }}'.replace(':id', basesalaryId);

                document.getElementById('edit_unitid').value = currentUnitId;
                document.getElementById('edit_effectivedate').value = btn.dataset.effectiveDate || '';
                document.getElementById('edit_expirationdate').value = btn.dataset.expirationDate || '';
                
                // Xử lý mức lương - đảm bảo dùng số nguyên
                const amount = btn.dataset.basesalaryAmount || '';
                if (amount) {
                    let value = amount.toString().replace(/,/g, '');
                    const numValue = parseFloat(value);
                    document.getElementById('edit_basesalaryamount').value = !isNaN(numValue) ? Math.round(numValue).toString() : value.replace(/[^0-9]/g, '');
                } else {
                    document.getElementById('edit_basesalaryamount').value = '';
                }
                
                document.getElementById('edit_note').value = btn.dataset.note || '';
            }
        });

        // Terminate button handler
        document.addEventListener('click', function (e) {
            if (e.target.closest('.terminate-basesalary-btn')) {
                const btn = e.target.closest('.terminate-basesalary-btn');
                const basesalaryId = btn.dataset.basesalaryId;
                const form = document.getElementById('terminateBaseSalaryForm');
                form.action = '{{ route("accounting.basesalary.terminate", ":id") }}'.replace(':id', basesalaryId);
                document.getElementById('terminate_unit_name').textContent = btn.dataset.unitName || 'Toàn hệ thống';
                document.getElementById('terminate_basesalary_amount').textContent = btn.dataset.basesalaryAmount || '-';
            }
        });

        // Delete button handler
        document.addEventListener('click', function (e) {
            if (e.target.closest('.delete-basesalary-btn')) {
                const btn = e.target.closest('.delete-basesalary-btn');
                const basesalaryId = btn.dataset.basesalaryId;
                const form = document.getElementById('deleteBaseSalaryForm');
                form.action = '{{ route("accounting.basesalary.destroy", ":id") }}'.replace(':id', basesalaryId);
                document.getElementById('delete_unit_name').textContent = btn.dataset.unitName || 'này';
            }
        });
        
        // Xử lý khi modal edit được hiển thị - đảm bảo giá trị mức lương đúng
        const editModal = document.getElementById('edit_basesalary');
        if (editModal) {
            editModal.addEventListener('shown.bs.modal', function() {
                const amountInput = document.getElementById('edit_basesalaryamount');
                if (amountInput && amountInput.value) {
                    let value = amountInput.value.toString().replace(/,/g, '');
                    const numValue = parseFloat(value);
                    if (!isNaN(numValue)) {
                        amountInput.value = Math.round(numValue).toString();
                        // Validate sau khi set giá trị
                        validateBaseSalaryAmount(amountInput, 'edit');
                    }
                }
            });
        }

        // Validate trước khi submit form Create
        const createForm = document.getElementById('createBaseSalaryForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                const amountInput = document.getElementById('create_basesalaryamount');
                if (!validateBaseSalaryAmount(amountInput, 'create')) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // Validate trước khi submit form Edit
        const editForm = document.getElementById('editBaseSalaryForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                const amountInput = document.getElementById('edit_basesalaryamount');
                if (!validateBaseSalaryAmount(amountInput, 'edit')) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    });
</script>
@endpush


