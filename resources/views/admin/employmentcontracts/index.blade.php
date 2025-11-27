@extends('layouts.admin')

@section('title', 'Quản lý hợp đồng')

@section('content')
<!-- Success Notification Modal -->
@if (session('success'))
    <div class="modal fade contract-modal modal-success" id="successNotificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title">Thành công</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ session('success') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Error Notification Modal -->
@if ($errors->any())
    <div class="modal fade contract-modal modal-error" id="errorNotificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title">Đã có lỗi xảy ra</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Quản lý hợp đồng</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý hợp đồng</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('admin.employmentcontract.index') }}">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Họ và tên</label>
                    <input type="text" name="search_teacher" class="form-control" placeholder="Tìm kiếm theo họ và tên ..." value="{{ request('search_teacher') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Loại hợp đồng</label>
                    <select name="search_contracttype" class="form-control">
                        <option value="">Tất cả loại hợp đồng</option>
                        <option value="Làm việc không xác định thời hạn" {{ request('search_contracttype') == 'Làm việc không xác định thời hạn' ? 'selected' : '' }}>Làm việc không xác định thời hạn</option>
                        <option value="Làm việc xác định thời hạn" {{ request('search_contracttype') == 'Làm việc xác định thời hạn' ? 'selected' : '' }}>Làm việc xác định thời hạn</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Ngày ký</label>
                    <input type="date" name="search_signdate" class="form-control" value="{{ request('search_signdate') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Ngày hiệu lực</label>
                    <input type="date" name="search_startdate" class="form-control" value="{{ request('search_startdate') }}">
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
                            <h3 class="page-title">Danh sách hợp đồng</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createContractModal">
                                <i class="fas fa-plus me-1"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped contract-table">
                        <thead class="student-thread">
                            <tr>
                                <th>Mã hợp đồng</th>
                                <th>Họ và tên</th>
                                <th>Loại hợp đồng</th>
                                <th>Ngày ký</th>
                                <th>Ngày hiệu lực</th>
                                <th>Ngày hết hạn</th>
                                <th>Ghi chú</th>
                                <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contracts as $contract)
                                <tr>
                                    <td style="padding-left: 55px !important;">{{ $contract->contractid }}</td>
                                    <td>{{ $contract->teacher ? $contract->teacher->fullname : '-' }}</td>
                                    <td>{{ $contract->contracttype }}</td>
                                    <td>{{ $contract->signdate ? \Illuminate\Support\Carbon::parse($contract->signdate)->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $contract->startdate ? \Illuminate\Support\Carbon::parse($contract->startdate)->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $contract->enddate ? \Illuminate\Support\Carbon::parse($contract->enddate)->format('d/m/Y') : '-' }}</td>
                                    <td>{{ Str::limit($contract->note ?? '-', 50) }}</td>
                                    <td class="text-end">
                                        <div class="btn-group contract-actions" role="group">
                                            <a href="#" class="btn btn-warning btn-sm rounded-pill me-1 text-white view-contract-btn"
                                               data-bs-toggle="modal" data-bs-target="#view_contract"
                                               title="Xem chi tiết"
                                               data-contract-id="{{ $contract->contractid }}"
                                               data-teacher-name="{{ $contract->teacher ? htmlspecialchars($contract->teacher->fullname, ENT_QUOTES, 'UTF-8') : '-' }}"
                                               data-contracttype="{{ htmlspecialchars($contract->contracttype, ENT_QUOTES, 'UTF-8') }}"
                                               data-signdate="{{ $contract->signdate ? \Illuminate\Support\Carbon::parse($contract->signdate)->format('Y-m-d') : '' }}"
                                               data-startdate="{{ $contract->startdate ? \Illuminate\Support\Carbon::parse($contract->startdate)->format('Y-m-d') : '' }}"
                                               data-enddate="{{ $contract->enddate ? \Illuminate\Support\Carbon::parse($contract->enddate)->format('Y-m-d') : '' }}"
                                               data-note="{{ htmlspecialchars($contract->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-success btn-sm rounded-pill me-1 text-white edit-contract-btn"
                                               data-bs-toggle="modal" data-bs-target="#edit_contract"
                                               title="Chỉnh sửa"
                                               data-contract-id="{{ $contract->contractid }}"
                                               data-teacher-id="{{ $contract->teacherid }}"
                                               data-contracttype="{{ htmlspecialchars($contract->contracttype, ENT_QUOTES, 'UTF-8') }}"
                                               data-signdate="{{ $contract->signdate ? \Illuminate\Support\Carbon::parse($contract->signdate)->format('Y-m-d') : '' }}"
                                               data-startdate="{{ $contract->startdate ? \Illuminate\Support\Carbon::parse($contract->startdate)->format('Y-m-d') : '' }}"
                                               data-enddate="{{ $contract->enddate ? \Illuminate\Support\Carbon::parse($contract->enddate)->format('Y-m-d') : '' }}"
                                               data-note="{{ htmlspecialchars($contract->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="#" class="btn btn-danger btn-sm rounded-pill text-white delete-contract-btn"
                                               data-bs-toggle="modal" data-bs-target="#delete_contract"
                                               title="Xóa"
                                               data-contract-id="{{ $contract->contractid }}"
                                               data-teacher-name="{{ $contract->teacher ? htmlspecialchars($contract->teacher->fullname, ENT_QUOTES, 'UTF-8') : '-' }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Không có dữ liệu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden data for JavaScript -->
<div id="page-data" 
     data-has-success="{{ session('success') ? 'true' : 'false' }}" 
     data-has-errors="{{ $errors->any() ? 'true' : 'false' }}" 
     style="display: none;">
</div>

<!-- Create Modal -->
<div class="modal fade contract-modal" id="createContractModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Thêm hợp đồng mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.employmentcontract.store') }}" method="post" id="createContractForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giáo viên <span class="text-danger">*</span></label>
                            <select name="teacherid" id="create_teacherid" class="form-control" required>
                                <option value="">-- Chọn giáo viên --</option>
                                @forelse($teachers as $teacher)
                                    <option value="{{ $teacher->teacherid }}" {{ old('teacherid') == $teacher->teacherid ? 'selected' : '' }}>
                                        {{ $teacher->fullname }}
                                    </option>
                                @empty
                                    <option value="" disabled>Không có giáo viên nào có thể thêm hợp đồng mới</option>
                                @endforelse
                            </select>
                            @if($teachers->isEmpty())
                                <div class="text-warning small mt-1">Tất cả giáo viên đều đã có hợp đồng đang hiệu lực</div>
                            @endif
                            @error('teacherid')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Loại hợp đồng <span class="text-danger">*</span></label>
                            <select name="contracttype" id="create_contracttype" class="form-control" required>
                                <option value="">-- Chọn loại hợp đồng --</option>
                                <option value="Làm việc không xác định thời hạn" {{ old('contracttype') == 'Làm việc không xác định thời hạn' ? 'selected' : '' }}>Làm việc không xác định thời hạn</option>
                                <option value="Làm việc xác định thời hạn" {{ old('contracttype') == 'Làm việc xác định thời hạn' ? 'selected' : '' }}>Làm việc xác định thời hạn</option>
                            </select>
                            @error('contracttype')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ngày ký <span class="text-danger">*</span></label>
                            <input type="date" name="signdate" id="create_signdate" class="form-control" required
                                   value="{{ old('signdate') }}"
                                   onchange="validateSignDate(this, 'create')">
                            <div id="create_signdate_error" class="invalid-feedback"></div>
                            @error('signdate')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ngày hiệu lực <span class="text-danger">*</span></label>
                            <input type="date" name="startdate" id="create_startdate" class="form-control" required
                                   value="{{ old('startdate') }}"
                                   onchange="validateStartDate(this, 'create')">
                            <div id="create_startdate_error" class="invalid-feedback"></div>
                            @error('startdate')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ngày hết hạn</label>
                            <input type="date" name="enddate" id="create_enddate" class="form-control"
                                   value="{{ old('enddate') }}"
                                   onchange="validateEndDate(this, 'create')">
                            <div id="create_enddate_error" class="invalid-feedback"></div>
                            @error('enddate')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="note" id="create_note" rows="3" class="form-control" placeholder="Nhập ghi chú (không bắt buộc)">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
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
<div class="modal fade contract-modal" id="view_contract" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết hợp đồng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mã hợp đồng</label>
                        <input type="text" id="view_contractid" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" id="view_teacher" class="form-control" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Loại hợp đồng</label>
                        <input type="text" id="view_contracttype" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ngày ký</label>
                        <input type="text" id="view_signdate" class="form-control" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ngày hiệu lực</label>
                        <input type="text" id="view_startdate" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ngày hết hạn</label>
                        <input type="text" id="view_enddate" class="form-control" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea id="view_note" rows="3" class="form-control" readonly></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade contract-modal" id="edit_contract" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chỉnh sửa hợp đồng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editContractForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giáo viên <span class="text-danger">*</span></label>
                            <select name="teacherid" id="edit_teacherid" class="form-control" required>
                                <option value="">-- Chọn giáo viên --</option>
                                @foreach($allTeachers ?? $teachers as $teacher)
                                    <option value="{{ $teacher->teacherid }}">
                                        {{ $teacher->fullname }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacherid')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Loại hợp đồng <span class="text-danger">*</span></label>
                            <select name="contracttype" id="edit_contracttype" class="form-control" required>
                                <option value="">-- Chọn loại hợp đồng --</option>
                                <option value="Làm việc không xác định thời hạn">Làm việc không xác định thời hạn</option>
                                <option value="Làm việc xác định thời hạn">Làm việc xác định thời hạn</option>
                            </select>
                            @error('contracttype')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ngày ký <span class="text-danger">*</span></label>
                            <input type="date" name="signdate" id="edit_signdate" class="form-control" required
                                   onchange="validateSignDate(this, 'edit')">
                            <div id="edit_signdate_error" class="invalid-feedback"></div>
                            @error('signdate')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ngày hiệu lực <span class="text-danger">*</span></label>
                            <input type="date" name="startdate" id="edit_startdate" class="form-control" required
                                   onchange="validateStartDate(this, 'edit')">
                            <div id="edit_startdate_error" class="invalid-feedback"></div>
                            @error('startdate')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ngày hết hạn</label>
                            <input type="date" name="enddate" id="edit_enddate" class="form-control"
                                   onchange="validateEndDate(this, 'edit')">
                            <div id="edit_enddate_error" class="invalid-feedback"></div>
                            @error('enddate')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="note" id="edit_note" rows="3" class="form-control" placeholder="Nhập ghi chú (không bắt buộc)"></textarea>
                            @error('note')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
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
<div class="modal fade contract-modal modal-delete" id="delete_contract" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteContractForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa hợp đồng của <strong id="delete_teacher_name"></strong>?</p>
                    <p class="text-danger small mb-0">Hành động này không thể hoàn tác!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .contract-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        padding: 0.75rem;
    }
    
    .contract-table tbody td {
        font-size: 1rem;
    }
    
    .contract-table thead th::before,
    .contract-table thead th::after {
        display: none !important;
    }
    
    .contract-table thead th {
        cursor: default !important;
    }
    
    .contract-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }
    
    .contract-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .contract-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .contract-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .contract-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }
    
    .contract-modal.modal-success .modal-header {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .contract-modal.modal-success .modal-footer {
        background: #ecfdf5;
        justify-content: center;
    }

    .contract-modal.modal-success .modal-footer .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
    }

    .contract-modal.modal-success .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #059669, #047857);
    }
    
    .contract-modal.modal-error .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .contract-modal.modal-error .modal-footer {
        background: #fef2f2;
        justify-content: center;
    }

    .contract-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .contract-modal.modal-delete .modal-footer {
        background: #fef2f2;
    }

    .contract-modal .form-label {
        font-weight: 500;
        color: #1f2937;
    }

    .contract-modal .form-control,
    .contract-modal .form-select {
        border-radius: 0.65rem;
        border-color: #dbe3f2;
        padding: 0.6rem 0.85rem;
        box-shadow: none;
    }

    .contract-modal .form-control.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .contract-modal .form-control.is-valid {
        border-color: #198754;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .contract-modal .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .contract-modal textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .contract-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    .contract-actions .btn {
        transition: all .2s ease;
    }

    .contract-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(148, 163, 184, 0.25);
    }

    /* Cách trái 30px cho cột Thao tác */
    .contract-table th.text-end,
    .contract-table td.text-end {
        padding-left: 30px !important;
    }
</style>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables/datatables.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
<script>
    // Lấy giá trị từ data attribute
    const pageData = document.getElementById('page-data');
    const hasSuccess = pageData && pageData.dataset.hasSuccess === 'true';
    const hasErrors = pageData && pageData.dataset.hasErrors === 'true';
    
    // Hiển thị modal thông báo thành công/lỗi
    if (hasSuccess) {
        const successModal = document.getElementById('successNotificationModal');
        if (successModal) {
            const modal = new bootstrap.Modal(successModal);
            modal.show();
        }
    }
    
    if (hasErrors) {
        const errorModal = document.getElementById('errorNotificationModal');
        if (errorModal) {
            const modal = new bootstrap.Modal(errorModal);
            modal.show();
        }
    }

    $(document).ready(function() {
        // DataTables configuration
        if ($('.contract-table').length) {
            if ($.fn.DataTable.isDataTable('.contract-table')) {
                $('.contract-table').DataTable().destroy();
            }
            $('.contract-table').DataTable({
                language: {
                    paginate: {
                        previous: 'Trước',
                        next: 'Sau'
                    },
                    search: "",
                    searchPlaceholder: "Tìm kiếm trong bảng..."
                },
                pageLength: 10,
                searching: false,
                lengthChange: false,
                info: false,
                order: [],
                columnDefs: [
                    { orderable: false, targets: '_all' }
                ],
                responsive: true
            });
        }
    });

    // View button handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-contract-btn')) {
            const btn = e.target.closest('.view-contract-btn');
            document.getElementById('view_contractid').value = btn.dataset.contractId || '';
            document.getElementById('view_teacher').value = btn.dataset.teacherName || '-';
            document.getElementById('view_contracttype').value = btn.dataset.contracttype || '-';
            
            const signdate = btn.dataset.signdate;
            if (signdate) {
                const date = new Date(signdate);
                document.getElementById('view_signdate').value = date.toLocaleDateString('vi-VN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            } else {
                document.getElementById('view_signdate').value = '-';
            }
            
            const startdate = btn.dataset.startdate;
            if (startdate) {
                const date = new Date(startdate);
                document.getElementById('view_startdate').value = date.toLocaleDateString('vi-VN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            } else {
                document.getElementById('view_startdate').value = '-';
            }
            
            const enddate = btn.dataset.enddate;
            if (enddate) {
                const date = new Date(enddate);
                document.getElementById('view_enddate').value = date.toLocaleDateString('vi-VN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            } else {
                document.getElementById('view_enddate').value = '-';
            }
            
            document.getElementById('view_note').value = btn.dataset.note || '-';
        }
    });

    // Edit button handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-contract-btn')) {
            const btn = e.target.closest('.edit-contract-btn');
            const contractId = btn.dataset.contractId;
            const form = document.getElementById('editContractForm');
            form.action = '{{ route("admin.employmentcontract.update", ":id") }}'.replace(':id', contractId);
            
            document.getElementById('edit_teacherid').value = btn.dataset.teacherId || '';
            // Set giá trị cho dropdown loại hợp đồng
            const contracttype = btn.dataset.contracttype || '';
            const contracttypeSelect = document.getElementById('edit_contracttype');
            if (contracttypeSelect) {
                contracttypeSelect.value = contracttype;
            }
            document.getElementById('edit_signdate').value = btn.dataset.signdate || '';
            document.getElementById('edit_startdate').value = btn.dataset.startdate || '';
            document.getElementById('edit_enddate').value = btn.dataset.enddate || '';
            document.getElementById('edit_note').value = btn.dataset.note || '';
        }
    });

    // Delete button handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-contract-btn')) {
            const btn = e.target.closest('.delete-contract-btn');
            const contractId = btn.dataset.contractId;
            const form = document.getElementById('deleteContractForm');
            form.action = '{{ route("admin.employmentcontract.destroy", ":id") }}'.replace(':id', contractId);
            document.getElementById('delete_teacher_name').textContent = btn.dataset.teacherName || 'này';
        }
    });

    // Validate ngày ký
    function validateSignDate(input, formType) {
        const value = input.value;
        const errorDiv = document.getElementById(formType + '_signdate_error');
        const startdateInput = document.getElementById(formType + '_startdate');
        
        input.classList.remove('is-invalid', 'is-valid');
        errorDiv.textContent = '';
        errorDiv.style.display = 'none';
        
        if (value && startdateInput && startdateInput.value) {
            const signDate = new Date(value);
            const startDate = new Date(startdateInput.value);
            
            if (signDate > startDate) {
                input.classList.add('is-invalid');
                errorDiv.textContent = 'Ngày ký phải trước hoặc bằng ngày hiệu lực';
                errorDiv.style.display = 'block';
            } else {
                input.classList.add('is-valid');
                // Re-validate startdate khi signdate thay đổi
                if (startdateInput.value) {
                    validateStartDate(startdateInput, formType);
                }
            }
        } else if (value) {
            input.classList.add('is-valid');
        }
    }

    // Validate ngày hiệu lực
    function validateStartDate(input, formType) {
        const value = input.value;
        const errorDiv = document.getElementById(formType + '_startdate_error');
        const signdateInput = document.getElementById(formType + '_signdate');
        const enddateInput = document.getElementById(formType + '_enddate');
        
        input.classList.remove('is-invalid', 'is-valid');
        errorDiv.textContent = '';
        errorDiv.style.display = 'none';
        
        let isValid = true;
        
        // Kiểm tra với ngày ký
        if (value && signdateInput && signdateInput.value) {
            const signDate = new Date(signdateInput.value);
            const startDate = new Date(value);
            
            if (startDate < signDate) {
                input.classList.add('is-invalid');
                errorDiv.textContent = 'Ngày hiệu lực phải sau hoặc bằng ngày ký';
                errorDiv.style.display = 'block';
                isValid = false;
            }
        }
        
        // Kiểm tra với ngày hết hạn
        if (isValid && value && enddateInput && enddateInput.value) {
            const startDate = new Date(value);
            const endDate = new Date(enddateInput.value);
            
            if (startDate >= endDate) {
                input.classList.add('is-invalid');
                errorDiv.textContent = 'Ngày hiệu lực phải trước ngày hết hạn';
                errorDiv.style.display = 'block';
                isValid = false;
            }
        }
        
        if (isValid && value) {
            input.classList.add('is-valid');
        }
    }

    // Validate ngày hết hạn
    function validateEndDate(input, formType) {
        const value = input.value;
        const errorDiv = document.getElementById(formType + '_enddate_error');
        const startdateInput = document.getElementById(formType + '_startdate');
        
        input.classList.remove('is-invalid', 'is-valid');
        errorDiv.textContent = '';
        errorDiv.style.display = 'none';
        
        if (value && startdateInput && startdateInput.value) {
            const startDate = new Date(startdateInput.value);
            const endDate = new Date(value);
            
            if (endDate <= startDate) {
                input.classList.add('is-invalid');
                errorDiv.textContent = 'Ngày hết hạn phải sau ngày hiệu lực';
                errorDiv.style.display = 'block';
            } else {
                input.classList.add('is-valid');
                // Re-validate startdate khi enddate thay đổi
                validateStartDate(startdateInput, formType);
            }
        } else if (value) {
            input.classList.add('is-valid');
        }
    }

    // Validate form trước khi submit
    document.getElementById('createContractForm')?.addEventListener('submit', function(e) {
        const signdateInput = document.getElementById('create_signdate');
        const startdateInput = document.getElementById('create_startdate');
        const enddateInput = document.getElementById('create_enddate');
        
        validateSignDate(signdateInput, 'create');
        validateStartDate(startdateInput, 'create');
        if (enddateInput.value) {
            validateEndDate(enddateInput, 'create');
        }
        
        if (signdateInput.classList.contains('is-invalid') || 
            startdateInput.classList.contains('is-invalid') || 
            enddateInput.classList.contains('is-invalid')) {
            e.preventDefault();
            return false;
        }
    });

    document.getElementById('editContractForm')?.addEventListener('submit', function(e) {
        const signdateInput = document.getElementById('edit_signdate');
        const startdateInput = document.getElementById('edit_startdate');
        const enddateInput = document.getElementById('edit_enddate');
        
        validateSignDate(signdateInput, 'edit');
        validateStartDate(startdateInput, 'edit');
        if (enddateInput.value) {
            validateEndDate(enddateInput, 'edit');
        }
        
        if (signdateInput.classList.contains('is-invalid') || 
            startdateInput.classList.contains('is-invalid') || 
            enddateInput.classList.contains('is-invalid')) {
            e.preventDefault();
            return false;
        }
    });
</script>
@endpush

