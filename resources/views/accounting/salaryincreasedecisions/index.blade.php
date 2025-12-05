@extends('layouts.accounting')

@section('title', 'Quản lý quyết định nâng lương')

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
                <h3 class="page-title">Quản lý quyết định nâng lương</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('accounting.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý quyết định nâng lương</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('accounting.salaryincreasedecision.index') }}">
        <div class="row">
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã quyết định</label>
                    <input type="text" name="search_id" class="form-control" placeholder="Tìm kiếm theo mã ..." value="{{ request('search_id') }}">
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
                    <label class="form-label small text-muted">Ngày ký quyết định</label>
                    <input type="date" name="search_decisiondate" class="form-control" value="{{ request('search_decisiondate') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Ngày áp dụng</label>
                    <input type="date" name="search_applydate" class="form-control" value="{{ request('search_applydate') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4 d-flex align-items-end">
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
                            <h3 class="page-title">Danh sách quyết định nâng lương</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSalaryIncreaseDecisionModal" onclick="resetCreateForm()">
                                <i class="fas fa-plus me-1"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped salaryincreasedecision-table">
                        <thead class="student-thread">
                            <tr>
                                <th class="text-center" style="padding-right: 50px !important;">Mã quyết định</th>
                                <th>Giáo viên</th>
                                <th>Ngày ký quyết định</th>
                                <th>Ngày áp dụng</th>
                                <th class="text-center">Hệ số cũ</th>
                                <th class="text-center">Hệ số mới</th>
                                <th class="text-center">Chênh lệch</th>
                                <th class="description-column">Ghi chú</th>
                                <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($decisions as $decision)
                                <tr>
                                    <td style="padding-left: 80px !important;">{{ $decision->decisionid }}</td>
                                    <td>{{ $decision->teacher->fullname ?? '-' }}</td>
                                    <td>{{ $decision->decisiondate ? $decision->decisiondate->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $decision->applydate ? $decision->applydate->format('d/m/Y') : '-' }}</td>
                                    <td class="text-center">{{ $decision->oldcoefficient ? number_format($decision->oldcoefficient, 4, '.', '') : '-' }}</td>
                                    <td class="text-center">
                                        <strong class="text-success">{{ $decision->newcoefficient ? number_format($decision->newcoefficient, 4, '.', '') : '-' }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if($decision->oldcoefficient && $decision->newcoefficient)
                                            <span class="badge bg-info">
                                                +{{ number_format($decision->newcoefficient - $decision->oldcoefficient, 4, '.', '') }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="description-column" title="{{ $decision->note ?? '-' }}">
                                        @if($decision->note)
                                            {{ Str::limit($decision->note, 50, '...') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group salaryincreasedecision-actions" role="group">
                                            <a href="#"
                                               class="btn btn-warning btn-sm rounded-pill me-1 text-white view-salaryincreasedecision-btn"
                                               data-bs-toggle="modal" data-bs-target="#view_salaryincreasedecision"
                                               title="Xem chi tiết"
                                               data-decision-id="{{ $decision->decisionid }}"
                                               data-teacher-name="{{ htmlspecialchars($decision->teacher->fullname ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-decision-date="{{ $decision->decisiondate ? $decision->decisiondate->format('Y-m-d') : '' }}"
                                               data-apply-date="{{ $decision->applydate ? $decision->applydate->format('Y-m-d') : '' }}"
                                               data-old-coefficient="{{ $decision->oldcoefficient ?? '' }}"
                                               data-new-coefficient="{{ $decision->newcoefficient ?? '' }}"
                                               data-note="{{ htmlspecialchars($decision->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#"
                                               class="btn btn-success btn-sm rounded-pill me-1 text-white edit-salaryincreasedecision-btn"
                                               data-bs-toggle="modal" data-bs-target="#edit_salaryincreasedecision"
                                               title="Chỉnh sửa"
                                               data-decision-id="{{ $decision->decisionid }}"
                                               data-teacher-id="{{ $decision->teacherid }}"
                                               data-teacher-name="{{ htmlspecialchars($decision->teacher->fullname ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-decision-date="{{ $decision->decisiondate ? $decision->decisiondate->format('Y-m-d') : '' }}"
                                               data-apply-date="{{ $decision->applydate ? $decision->applydate->format('Y-m-d') : '' }}"
                                               data-old-coefficient="{{ $decision->oldcoefficient ?? '' }}"
                                               data-new-coefficient="{{ $decision->newcoefficient ?? '' }}"
                                               data-note="{{ htmlspecialchars($decision->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="#"
                                               class="btn btn-danger btn-sm rounded-pill text-white delete-salaryincreasedecision-btn"
                                               data-bs-toggle="modal" data-bs-target="#delete_salaryincreasedecision"
                                               title="Xóa"
                                               data-decision-id="{{ $decision->decisionid }}"
                                               data-teacher-name="{{ htmlspecialchars($decision->teacher->fullname ?? '-', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Chưa có dữ liệu quyết định nâng lương.</td>
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
<div class="modal fade salaryincreasedecision-modal" id="createSalaryIncreaseDecisionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Thêm quyết định nâng lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('accounting.salaryincreasedecision.store') }}" method="post" id="createSalaryIncreaseDecisionForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Giáo viên <span class="text-danger">*</span></label>
                        <select name="teacherid" id="create_teacherid" class="form-control" required onchange="loadCurrentCoefficient(this.value)">
                            <option value="">-- Chọn giáo viên --</option>
                            @foreach($allTeachers as $teacher)
                                <option value="{{ $teacher->teacherid }}" {{ old('teacherid') == $teacher->teacherid ? 'selected' : '' }}>
                                    {{ $teacher->fullname }}
                                </option>
                            @endforeach
                        </select>
                        @error('teacherid')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày ký quyết định <span class="text-danger">*</span></label>
                                <input type="date" name="decisiondate" id="create_decisiondate" class="form-control" required value="{{ old('decisiondate') }}" onchange="validateApplyDate('create');">
                                @error('decisiondate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày áp dụng <span class="text-danger">*</span></label>
                                <input type="date" name="applydate" id="create_applydate" class="form-control" required value="{{ old('applydate') }}" onchange="validateApplyDate('create');">
                                @error('applydate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hệ số cũ</label>
                                <input type="number" id="create_oldcoefficient" class="form-control" step="0.0001" min="0" max="9999.9999" readonly>
                                <small class="text-muted">Hệ số hiện tại của giáo viên (tự động lấy)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hệ số mới <span class="text-danger">*</span></label>
                                <input type="number" name="newcoefficient" id="create_newcoefficient" class="form-control" step="0.0001" min="0" max="9999.9999" required value="{{ old('newcoefficient') }}" oninput="calculateDifference('create')">
                                <small class="text-muted">Hệ số sau khi nâng lương</small>
                                @error('newcoefficient')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info mb-3">
                                <strong>Chênh lệch:</strong> <span id="create_difference">0.0000</span>
                            </div>
                        </div>
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
<div class="modal fade salaryincreasedecision-modal" id="view_salaryincreasedecision" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết quyết định nâng lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Mã quyết định</label>
                    <input type="text" id="view_decisionid" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giáo viên</label>
                    <input type="text" id="view_teachername" class="form-control" readonly>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Ngày ký quyết định</label>
                            <input type="text" id="view_decisiondate" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Ngày áp dụng</label>
                            <input type="text" id="view_applydate" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Hệ số cũ</label>
                            <input type="text" id="view_oldcoefficient" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Hệ số mới</label>
                            <input type="text" id="view_newcoefficient" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info mb-3">
                            <strong>Chênh lệch:</strong> <span id="view_difference">0.0000</span>
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
<div class="modal fade salaryincreasedecision-modal" id="edit_salaryincreasedecision" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chỉnh sửa quyết định nâng lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSalaryIncreaseDecisionForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Giáo viên <span class="text-danger">*</span></label>
                        <select name="teacherid" id="edit_teacherid" class="form-control" required>
                            <option value="">-- Chọn giáo viên --</option>
                            @foreach($allTeachers as $teacher)
                                <option value="{{ $teacher->teacherid }}">
                                    {{ $teacher->fullname }}
                                </option>
                            @endforeach
                        </select>
                        @error('teacherid')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày ký quyết định <span class="text-danger">*</span></label>
                                <input type="date" name="decisiondate" id="edit_decisiondate" class="form-control" required onchange="validateApplyDate('edit')">
                                @error('decisiondate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày áp dụng <span class="text-danger">*</span></label>
                                <input type="date" name="applydate" id="edit_applydate" class="form-control" required onchange="validateApplyDate('edit')">
                                @error('applydate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hệ số cũ <span class="text-danger">*</span></label>
                                <input type="number" name="oldcoefficient" id="edit_oldcoefficient" class="form-control" step="0.0001" min="0" max="9999.9999" required oninput="calculateDifference('edit')">
                                @error('oldcoefficient')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hệ số mới <span class="text-danger">*</span></label>
                                <input type="number" name="newcoefficient" id="edit_newcoefficient" class="form-control" step="0.0001" min="0" max="9999.9999" required oninput="calculateDifference('edit')">
                                @error('newcoefficient')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info mb-3">
                                <strong>Chênh lệch:</strong> <span id="edit_difference">0.0000</span>
                            </div>
                        </div>
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
<div class="modal fade salaryincreasedecision-modal modal-delete" id="delete_salaryincreasedecision" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xóa quyết định nâng lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteSalaryIncreaseDecisionForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa quyết định nâng lương của giáo viên <strong id="delete_teacher_name">này</strong> không?</p>
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
    .salaryincreasedecision-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        padding: 0.75rem;
    }

    .salaryincreasedecision-table tbody td {
        font-size: 1rem;
    }

    .salaryincreasedecision-table thead th {
        cursor: default !important;
    }

    .salaryincreasedecision-table thead th::before,
    .salaryincreasedecision-table thead th::after {
        display: none !important;
    }

    .salaryincreasedecision-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .salaryincreasedecision-table td {
        vertical-align: middle;
    }

    .salaryincreasedecision-table th.description-column,
    .salaryincreasedecision-table td.description-column {
        max-width: 200px;
        width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .salaryincreasedecision-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .salaryincreasedecision-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .salaryincreasedecision-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .salaryincreasedecision-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .salaryincreasedecision-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }

    .salaryincreasedecision-modal .form-label {
        font-weight: 500;
        color: #1f2937;
    }

    .salaryincreasedecision-modal .form-control,
    .salaryincreasedecision-modal .form-select {
        border-radius: 0.65rem;
        border-color: #dbe3f2;
        padding: 0.6rem 0.85rem;
        box-shadow: none;
    }

    /* Điều chỉnh padding cho input date để icon calendar gần hơn */
    .salaryincreasedecision-modal input[type="date"] {
        padding-right: 0.5rem;
    }

    /* Điều chỉnh padding cho input date khi có validation icon để không bị đè với icon calendar */
    .salaryincreasedecision-modal input[type="date"].is-valid,
    .salaryincreasedecision-modal input[type="date"].is-invalid {
        padding-right: calc(1.5em + 0.5rem);
    }

    .salaryincreasedecision-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    .salaryincreasedecision-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .salaryincreasedecision-modal.modal-delete .modal-footer {
        background: #fef2f2;
    }

    .salaryincreasedecision-actions .btn {
        transition: all .2s ease;
    }

    .salaryincreasedecision-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(148, 163, 184, 0.25);
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    /* Badge trong cột chênh lệch có font-size bằng các cột khác */
    .salaryincreasedecision-table td .badge {
        font-size: 0.9rem;
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
        document.getElementById('create_teacherid').value = '';
        document.getElementById('create_oldcoefficient').value = '';
        document.getElementById('create_newcoefficient').value = '';
        document.getElementById('create_decisiondate').value = '';
        document.getElementById('create_applydate').value = '';
        document.getElementById('create_note').value = '';
        document.getElementById('create_difference').textContent = '0.0000';
        
        // Xóa validation classes
        const decisionDateInput = document.getElementById('create_decisiondate');
        const applyDateInput = document.getElementById('create_applydate');
        if (decisionDateInput) {
            decisionDateInput.classList.remove('is-invalid', 'is-valid');
        }
        if (applyDateInput) {
            applyDateInput.classList.remove('is-invalid', 'is-valid');
            const errorDiv = applyDateInput.parentElement.querySelector('.apply-date-error');
            if (errorDiv) {
                errorDiv.remove();
            }
        }
    }

    // Hàm load hệ số hiện tại của giáo viên
    async function loadCurrentCoefficient(teacherId) {
        if (!teacherId) {
            document.getElementById('create_oldcoefficient').value = '';
            calculateDifference('create');
            return;
        }

        try {
            const response = await fetch(`{{ url('admin/salaryincreasedecision/get-current-coefficient') }}/${teacherId}`);
            const data = await response.json();

            if (data.success) {
                const currentCoeff = parseFloat(data.currentcoefficient || 0);
                document.getElementById('create_oldcoefficient').value = currentCoeff.toFixed(4);
                calculateDifference('create');
            }
        } catch (error) {
            console.error('Error loading current coefficient:', error);
        }
    }

    // Hàm tính chênh lệch hệ số
    function calculateDifference(formType) {
        const oldCoeff = parseFloat(document.getElementById(formType + '_oldcoefficient')?.value || 0);
        const newCoeff = parseFloat(document.getElementById(formType + '_newcoefficient')?.value || 0);
        const difference = newCoeff - oldCoeff;
        
        const differenceElement = document.getElementById(formType + '_difference');
        if (differenceElement) {
            differenceElement.textContent = (difference >= 0 ? '+' : '') + difference.toFixed(4);
            differenceElement.parentElement.className = difference >= 0 ? 'alert alert-info mb-3' : 'alert alert-warning mb-3';
        }
    }

    // Hàm validate ngày áp dụng (giống như payrollcomponentconfig)
    function validateApplyDate(formType) {
        const decisionDateInput = document.getElementById(formType + '_decisiondate');
        const applyDateInput = document.getElementById(formType + '_applydate');
        
        if (!decisionDateInput || !applyDateInput) return false;
        
        const decisionDate = decisionDateInput.value;
        const applyDate = applyDateInput.value;
        
        applyDateInput.classList.remove('is-invalid', 'is-valid');
        
        if (applyDate && decisionDate) {
            const decision = new Date(decisionDate);
            const apply = new Date(applyDate);
            
            if (apply < decision) {
                applyDateInput.classList.add('is-invalid');
                const errorDiv = applyDateInput.parentElement.querySelector('.apply-date-error');
                if (errorDiv) {
                    errorDiv.textContent = 'Ngày áp dụng phải sau hoặc bằng ngày ký quyết định';
                } else {
                    const errorEl = document.createElement('div');
                    errorEl.className = 'text-danger small apply-date-error';
                    errorEl.textContent = 'Ngày áp dụng phải sau hoặc bằng ngày ký quyết định';
                    applyDateInput.parentElement.appendChild(errorEl);
                }
                return false;
            } else {
                applyDateInput.classList.add('is-valid');
                const errorDiv = applyDateInput.parentElement.querySelector('.apply-date-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
                return true;
            }
        }
        
        return true;
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

        // View button handler
        document.addEventListener('click', function (e) {
            if (e.target.closest('.view-salaryincreasedecision-btn')) {
                const btn = e.target.closest('.view-salaryincreasedecision-btn');
                
                document.getElementById('view_decisionid').value = btn.dataset.decisionId || '';
                document.getElementById('view_teachername').value = btn.dataset.teacherName || '';
                
                const decisionDate = btn.dataset.decisionDate || '';
                document.getElementById('view_decisiondate').value = decisionDate ? new Date(decisionDate + 'T00:00:00').toLocaleDateString('vi-VN') : '-';
                
                const applyDate = btn.dataset.applyDate || '';
                document.getElementById('view_applydate').value = applyDate ? new Date(applyDate + 'T00:00:00').toLocaleDateString('vi-VN') : '-';
                
                const oldCoeff = parseFloat(btn.dataset.oldCoefficient || 0);
                const newCoeff = parseFloat(btn.dataset.newCoefficient || 0);
                const difference = newCoeff - oldCoeff;
                
                document.getElementById('view_oldcoefficient').value = oldCoeff.toFixed(4);
                document.getElementById('view_newcoefficient').value = newCoeff.toFixed(4);
                document.getElementById('view_difference').textContent = (difference >= 0 ? '+' : '') + difference.toFixed(4);
                document.getElementById('view_note').value = btn.dataset.note || '';
            }
        });

        // Edit button handler
        document.addEventListener('click', function (e) {
            if (e.target.closest('.edit-salaryincreasedecision-btn')) {
                const btn = e.target.closest('.edit-salaryincreasedecision-btn');
                const decisionId = btn.dataset.decisionId;
                
                const form = document.getElementById('editSalaryIncreaseDecisionForm');
                if (form) {
                    form.action = '{{ route("accounting.salaryincreasedecision.update", ":id") }}'.replace(':id', decisionId);
                }

                const editModal = document.getElementById('edit_salaryincreasedecision');
                if (editModal) {
                    editModal.addEventListener('shown.bs.modal', function modalShown() {
                        document.getElementById('edit_teacherid').value = btn.dataset.teacherId || '';
                        document.getElementById('edit_decisiondate').value = btn.dataset.decisionDate || '';
                        document.getElementById('edit_applydate').value = btn.dataset.applyDate || '';
                        document.getElementById('edit_oldcoefficient').value = btn.dataset.oldCoefficient || '';
                        document.getElementById('edit_newcoefficient').value = btn.dataset.newCoefficient || '';
                        document.getElementById('edit_note').value = btn.dataset.note || '';
                        
                        calculateDifference('edit');
                        validateApplyDate('edit');
                        
                        editModal.removeEventListener('shown.bs.modal', modalShown);
                    }, { once: true });
                }
            }
        });

        // Delete button handler
        document.addEventListener('click', function (e) {
            if (e.target.closest('.delete-salaryincreasedecision-btn')) {
                const btn = e.target.closest('.delete-salaryincreasedecision-btn');
                const decisionId = btn.dataset.decisionId;
                const form = document.getElementById('deleteSalaryIncreaseDecisionForm');
                form.action = '{{ route("accounting.salaryincreasedecision.destroy", ":id") }}'.replace(':id', decisionId);
                const teacherName = btn.dataset.teacherName || '';
                document.getElementById('delete_teacher_name').textContent = teacherName || 'này';
            }
        });
    });
</script>
@endpush
