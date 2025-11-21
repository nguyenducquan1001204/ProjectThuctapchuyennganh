@extends('layouts.admin')

@section('title', 'Quản lý giáo viên')

@section('content')
<!-- Success Notification Modal -->
@if (session('success'))
    <div class="modal fade teacher-modal modal-success" id="successNotificationModal" tabindex="-1" aria-hidden="true">
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
    <div class="modal fade teacher-modal modal-error" id="errorNotificationModal" tabindex="-1" aria-hidden="true">
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
                <h3 class="page-title">Quản lý giáo viên</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý giáo viên</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('admin.teacher.index') }}">
        <div class="row">
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã giáo viên</label>
                    <input type="text" name="search_id" class="form-control" placeholder="Tìm kiếm theo mã ..." value="{{ request('search_id') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Họ và tên</label>
                    <input type="text" name="search_name" class="form-control" placeholder="Tìm kiếm theo tên ..." value="{{ request('search_name') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Chức danh</label>
                    <input type="text" name="search_jobtitle" class="form-control" placeholder="Tìm kiếm theo chức danh ..." value="{{ request('search_jobtitle') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Giới tính</label>
                    <select name="search_gender" class="form-control">
                        <option value="">Tất cả giới tính</option>
                        <option value="male" {{ request('search_gender') == 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ request('search_gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                        <option value="other" {{ request('search_gender') == 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Trạng thái</label>
                    <select name="search_status" class="form-control">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('search_status') == 'active' ? 'selected' : '' }}>Đang làm việc</option>
                        <option value="suspended" {{ request('search_status') == 'suspended' ? 'selected' : '' }}>Tạm đình chỉ</option>
                        <option value="onleave" {{ request('search_status') == 'onleave' ? 'selected' : '' }}>Nghỉ phép</option>
                        <option value="contractended" {{ request('search_status') == 'contractended' ? 'selected' : '' }}>Hết hợp đồng</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
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
                            <h3 class="page-title">Danh sách giáo viên</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTeacherModal">
                                <i class="fas fa-plus me-1"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped teacher-table">
                        <thead class="student-thread">
                            <tr>
                                <th>Mã giáo viên</th>
                                <th>Họ và tên</th>
                                <th>Ngày sinh</th>
                                <th>Giới tính</th>
                                <th>Chức danh</th>
                                <th>Đơn vị</th>
                                <th>Ngày bắt đầu làm việc</th>
                                <th>Trạng thái</th>
                                <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teachers as $teacher)
                                <tr>
                                    <td class="text-end" style="padding-right: 60px !important;">{{ $teacher->teacherid }}</td>
                                    <td>{{ $teacher->fullname }}</td>
                                    <td>{{ $teacher->birthdate ? \Illuminate\Support\Carbon::parse($teacher->birthdate)->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($teacher->gender == 'male')
                                            Nam
                                        @elseif($teacher->gender == 'female')
                                            Nữ
                                        @elseif($teacher->gender == 'other')
                                            Khác
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $teacher->jobTitle ? $teacher->jobTitle->jobtitlename : '-' }}</td>
                                    <td>{{ $teacher->unit ? $teacher->unit->unitname : '-' }}</td>
                                    <td class="text-center">{{ $teacher->startdate ? \Illuminate\Support\Carbon::parse($teacher->startdate)->format('d/m/Y') : '-' }}</td>
                                    <td>
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
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group teacher-actions" role="group">
                                            <a href="#" class="btn btn-warning btn-sm rounded-pill me-1 text-white view-teacher-btn"
                                               data-bs-toggle="modal" data-bs-target="#view_teacher"
                                               title="Xem chi tiết"
                                               data-teacher-id="{{ $teacher->teacherid }}"
                                               data-teacher-fullname="{{ htmlspecialchars($teacher->fullname, ENT_QUOTES, 'UTF-8') }}"
                                               data-teacher-birthdate="{{ $teacher->birthdate ? \Illuminate\Support\Carbon::parse($teacher->birthdate)->format('Y-m-d') : '' }}"
                                               data-teacher-gender="{{ $teacher->gender ?? '' }}"
                                               data-teacher-jobtitle="{{ $teacher->jobTitle ? htmlspecialchars($teacher->jobTitle->jobtitlename, ENT_QUOTES, 'UTF-8') : '-' }}"
                                               data-teacher-unit="{{ $teacher->unit ? htmlspecialchars($teacher->unit->unitname, ENT_QUOTES, 'UTF-8') : '-' }}"
                                               data-teacher-startdate="{{ $teacher->startdate ? \Illuminate\Support\Carbon::parse($teacher->startdate)->format('Y-m-d') : '' }}"
                                               data-teacher-status="{{ $teacher->status }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-success btn-sm rounded-pill me-1 text-white edit-teacher-btn"
                                               data-bs-toggle="modal" data-bs-target="#edit_teacher"
                                               title="Chỉnh sửa"
                                               data-teacher-id="{{ $teacher->teacherid }}"
                                               data-teacher-fullname="{{ htmlspecialchars($teacher->fullname, ENT_QUOTES, 'UTF-8') }}"
                                               data-teacher-birthdate="{{ $teacher->birthdate ? \Illuminate\Support\Carbon::parse($teacher->birthdate)->format('Y-m-d') : '' }}"
                                               data-teacher-gender="{{ $teacher->gender ?? '' }}"
                                               data-teacher-jobtitleid="{{ $teacher->jobtitleid ?? '' }}"
                                               data-teacher-unitid="{{ $teacher->unitid ?? '' }}"
                                               data-teacher-startdate="{{ $teacher->startdate ? \Illuminate\Support\Carbon::parse($teacher->startdate)->format('Y-m-d') : '' }}"
                                               data-teacher-status="{{ $teacher->status }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="#" class="btn btn-danger btn-sm rounded-pill text-white delete-teacher-btn"
                                               data-bs-toggle="modal" data-bs-target="#delete_teacher"
                                               title="Xóa"
                                               data-teacher-id="{{ $teacher->teacherid }}"
                                               data-teacher-fullname="{{ htmlspecialchars($teacher->fullname, ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Không có dữ liệu</td>
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
<div class="modal fade teacher-modal" id="createTeacherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Thêm giáo viên mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.teacher.store') }}" method="post" id="createTeacherForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="fullname" id="create_fullname" class="form-control" required autofocus
                                   value="{{ old('fullname') }}" maxlength="255" minlength="3"
                                   oninput="validateFullName(this, 'create')">
                            <div id="create_fullname_error" class="invalid-feedback"></div>
                            @error('fullname')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="birthdate" id="create_birthdate" class="form-control"
                                   value="{{ old('birthdate') }}" max="{{ date('Y-m-d', strtotime('-22 years')) }}"
                                   onchange="validateBirthDate(this, 'create')">
                            <div id="create_birthdate_error" class="invalid-feedback"></div>
                            @error('birthdate')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giới tính</label>
                            <select name="gender" id="create_gender" class="form-control">
                                <option value="">-- Chọn giới tính --</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('gender')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" id="create_status" class="form-control" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Đang làm việc</option>
                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Tạm đình chỉ</option>
                                <option value="onleave" {{ old('status') == 'onleave' ? 'selected' : '' }}>Nghỉ phép</option>
                                <option value="contractended" {{ old('status') == 'contractended' ? 'selected' : '' }}>Hết hợp đồng</option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Chức danh</label>
                            <select name="jobtitleid" id="create_jobtitleid" class="form-control">
                                <option value="">-- Chọn chức danh --</option>
                                @foreach($jobTitles as $jobTitle)
                                    <option value="{{ $jobTitle->jobtitleid }}" {{ old('jobtitleid') == $jobTitle->jobtitleid ? 'selected' : '' }}>
                                        {{ $jobTitle->jobtitlename }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jobtitleid')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Đơn vị</label>
                            <select name="unitid" id="create_unitid" class="form-control">
                                <option value="">-- Chọn đơn vị --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->unitid }}" {{ old('unitid') == $unit->unitid ? 'selected' : '' }}>
                                        {{ $unit->unitname }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unitid')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày bắt đầu công tác</label>
                            <input type="date" name="startdate" id="create_startdate" class="form-control"
                                   value="{{ old('startdate') }}">
                            @error('startdate')
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
<div class="modal fade teacher-modal" id="view_teacher" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết giáo viên</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mã giáo viên</label>
                        <input type="text" id="view_teacherid" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" id="view_fullname" class="form-control" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ngày sinh</label>
                        <input type="text" id="view_birthdate" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Giới tính</label>
                        <input type="text" id="view_gender" class="form-control" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Chức danh</label>
                        <input type="text" id="view_jobtitle" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Đơn vị</label>
                        <input type="text" id="view_unit" class="form-control" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ngày bắt đầu công tác</label>
                        <input type="text" id="view_startdate" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Trạng thái</label>
                        <input type="text" id="view_status" class="form-control" readonly>
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
<div class="modal fade teacher-modal" id="edit_teacher" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chỉnh sửa giáo viên</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTeacherForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="fullname" id="edit_fullname" class="form-control" required maxlength="255" minlength="3"
                                   oninput="validateFullName(this, 'edit')">
                            <div id="edit_fullname_error" class="invalid-feedback"></div>
                            @error('fullname')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="birthdate" id="edit_birthdate" class="form-control"
                                   max="{{ date('Y-m-d', strtotime('-22 years')) }}"
                                   onchange="validateBirthDate(this, 'edit')">
                            <div id="edit_birthdate_error" class="invalid-feedback"></div>
                            @error('birthdate')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giới tính</label>
                            <select name="gender" id="edit_gender" class="form-control">
                                <option value="">-- Chọn giới tính --</option>
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other">Khác</option>
                            </select>
                            @error('gender')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" id="edit_status" class="form-control" required>
                                <option value="active">Đang làm việc</option>
                                <option value="suspended">Tạm đình chỉ</option>
                                <option value="onleave">Nghỉ phép</option>
                                <option value="contractended">Hết hợp đồng</option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Chức danh</label>
                            <select name="jobtitleid" id="edit_jobtitleid" class="form-control">
                                <option value="">-- Chọn chức danh --</option>
                                @foreach($jobTitles as $jobTitle)
                                    <option value="{{ $jobTitle->jobtitleid }}">
                                        {{ $jobTitle->jobtitlename }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jobtitleid')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Đơn vị</label>
                            <select name="unitid" id="edit_unitid" class="form-control">
                                <option value="">-- Chọn đơn vị --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->unitid }}">
                                        {{ $unit->unitname }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unitid')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày bắt đầu công tác</label>
                            <input type="date" name="startdate" id="edit_startdate" class="form-control">
                            @error('startdate')
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
<div class="modal fade teacher-modal modal-delete" id="delete_teacher" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteTeacherForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa giáo viên <strong id="delete_teacher_name"></strong>?</p>
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
    .teacher-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        padding: 0.75rem;
    }
    
    .teacher-table thead th::before,
    .teacher-table thead th::after {
        display: none !important;
    }
    
    .teacher-table thead th {
        cursor: default !important;
    }
    
    .teacher-table tbody td {
        font-size: 1rem;
    }
    
    .teacher-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }
    
    .teacher-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .teacher-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .teacher-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .teacher-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }

    .teacher-modal .form-label {
        font-weight: 500;
        color: #1f2937;
    }

    .teacher-modal .form-control,
    .teacher-modal .form-select {
        border-radius: 0.65rem;
        border-color: #dbe3f2;
        padding: 0.6rem 0.85rem;
        box-shadow: none;
    }
    
    .teacher-modal.modal-success .modal-header {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .teacher-modal.modal-success .modal-footer {
        background: #ecfdf5;
        justify-content: center;
    }

    .teacher-modal.modal-success .modal-footer .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
    }

    .teacher-modal.modal-success .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #059669, #047857);
    }
    
    .teacher-modal.modal-error .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .teacher-modal.modal-error .modal-footer {
        background: #fef2f2;
        justify-content: center;
    }

    .teacher-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .teacher-modal.modal-delete .modal-footer {
        background: #fef2f2;
    }
    
    .teacher-actions .btn {
        transition: all .2s ease;
    }

    .teacher-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(148, 163, 184, 0.25);
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    /* Validation styles */
    .teacher-modal .form-control.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .teacher-modal .form-control.is-valid {
        border-color: #198754;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .teacher-modal .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .teacher-modal textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .teacher-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    /* Cách trái 30px cho cột Thao tác */
    .teacher-table th.text-end,
    .teacher-table td.text-end {
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
        if ($('.teacher-table').length) {
            if ($.fn.DataTable.isDataTable('.teacher-table')) {
                $('.teacher-table').DataTable().destroy();
            }
            $('.teacher-table').DataTable({
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
        if (e.target.closest('.view-teacher-btn')) {
            const btn = e.target.closest('.view-teacher-btn');
            document.getElementById('view_teacherid').value = btn.dataset.teacherId || '';
            document.getElementById('view_fullname').value = btn.dataset.teacherFullname || '';
            
            const birthdate = btn.dataset.teacherBirthdate;
            if (birthdate) {
                const date = new Date(birthdate);
                document.getElementById('view_birthdate').value = date.toLocaleDateString('vi-VN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            } else {
                document.getElementById('view_birthdate').value = '-';
            }
            
            const gender = btn.dataset.teacherGender;
            let genderText = '-';
            if (gender == 'male') genderText = 'Nam';
            else if (gender == 'female') genderText = 'Nữ';
            else if (gender == 'other') genderText = 'Khác';
            document.getElementById('view_gender').value = genderText;
            
            document.getElementById('view_jobtitle').value = btn.dataset.teacherJobtitle || '-';
            document.getElementById('view_unit').value = btn.dataset.teacherUnit || '-';
            
            const startdate = btn.dataset.teacherStartdate;
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
            
            const status = btn.dataset.teacherStatus;
            let statusText = '-';
            if (status == 'active') {
                statusText = 'Đang làm việc';
            } else if (status == 'suspended') {
                statusText = 'Tạm đình chỉ';
            } else if (status == 'onleave') {
                statusText = 'Nghỉ phép';
            } else if (status == 'contractended') {
                statusText = 'Hết hợp đồng';
            }
            document.getElementById('view_status').value = statusText;
        }
    });

    // Edit button handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-teacher-btn')) {
            const btn = e.target.closest('.edit-teacher-btn');
            const teacherId = btn.dataset.teacherId;
            const form = document.getElementById('editTeacherForm');
            form.action = '{{ route("admin.teacher.update", ":id") }}'.replace(':id', teacherId);
            
            document.getElementById('edit_fullname').value = btn.dataset.teacherFullname || '';
            document.getElementById('edit_birthdate').value = btn.dataset.teacherBirthdate || '';
            document.getElementById('edit_gender').value = btn.dataset.teacherGender || '';
            document.getElementById('edit_jobtitleid').value = btn.dataset.teacherJobtitleid || '';
            document.getElementById('edit_unitid').value = btn.dataset.teacherUnitid || '';
            document.getElementById('edit_startdate').value = btn.dataset.teacherStartdate || '';
            document.getElementById('edit_status').value = btn.dataset.teacherStatus || 'active';
        }
    });

    // Delete button handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-teacher-btn')) {
            const btn = e.target.closest('.delete-teacher-btn');
            const teacherId = btn.dataset.teacherId;
            const form = document.getElementById('deleteTeacherForm');
            form.action = '{{ route("admin.teacher.destroy", ":id") }}'.replace(':id', teacherId);
            document.getElementById('delete_teacher_name').textContent = btn.dataset.teacherFullname || 'này';
        }
    });

    // Validate họ và tên real-time
    function validateFullName(input, formType) {
        const value = input.value.trim();
        const errorDiv = document.getElementById(formType + '_fullname_error');
        let isValid = true;
        let errorMessage = '';

        // Xóa class cũ
        input.classList.remove('is-invalid', 'is-valid');

        // Kiểm tra độ dài
        if (value.length > 0 && value.length < 3) {
            isValid = false;
            errorMessage = 'Họ và tên phải có ít nhất 3 ký tự';
        } else if (value.length > 255) {
            isValid = false;
            errorMessage = 'Họ và tên không được vượt quá 255 ký tự';
        }
        // Kiểm tra định dạng (chỉ chữ cái và khoảng trắng)
        else if (value.length > 0 && !/^[\p{L}\s]+$/u.test(value)) {
            isValid = false;
            errorMessage = 'Họ và tên chỉ được chứa chữ cái và khoảng trắng';
        }
        // Kiểm tra phải có ít nhất một khoảng trắng (có cả họ và tên)
        else if (value.length > 0 && !/\s+/.test(value)) {
            isValid = false;
            errorMessage = 'Họ và tên phải bao gồm cả họ và tên (có khoảng trắng)';
        }
        // Kiểm tra không được có nhiều khoảng trắng liên tiếp
        else if (value.length > 0 && /\s{2,}/.test(value)) {
            isValid = false;
            errorMessage = 'Họ và tên không được có nhiều khoảng trắng liên tiếp';
        }
        // Kiểm tra mỗi từ phải có ít nhất 1 ký tự
        else if (value.length > 0) {
            const words = value.split(/\s+/);
            for (let word of words) {
                if (word.length < 1) {
                    isValid = false;
                    errorMessage = 'Mỗi từ trong họ và tên phải có ít nhất 1 ký tự';
                    break;
                }
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

    // Validate ngày sinh real-time (phải đủ 22 tuổi)
    function validateBirthDate(input, formType) {
        const value = input.value;
        const errorDiv = document.getElementById(formType + '_birthdate_error');
        let isValid = true;
        let errorMessage = '';

        // Xóa class cũ
        input.classList.remove('is-invalid', 'is-valid');

        if (value) {
            const selectedDate = new Date(value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            // Tính tuổi
            let age = today.getFullYear() - selectedDate.getFullYear();
            const monthDiff = today.getMonth() - selectedDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < selectedDate.getDate())) {
                age--;
            }
            
            if (selectedDate >= today) {
                isValid = false;
                errorMessage = 'Ngày sinh phải trước hôm nay';
            } else if (age < 22) {
                isValid = false;
                errorMessage = 'Giáo viên phải đủ 22 tuổi mới được đăng ký';
            }
        }

        // Hiển thị lỗi hoặc thành công
        if (value) {
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

    // Validate form trước khi submit
    document.getElementById('createTeacherForm')?.addEventListener('submit', function(e) {
        const fullnameInput = document.getElementById('create_fullname');
        const birthdateInput = document.getElementById('create_birthdate');
        
        validateFullName(fullnameInput, 'create');
        validateBirthDate(birthdateInput, 'create');
        
        if (fullnameInput.classList.contains('is-invalid') || 
            birthdateInput.classList.contains('is-invalid')) {
            e.preventDefault();
            return false;
        }
    });

    document.getElementById('editTeacherForm')?.addEventListener('submit', function(e) {
        const fullnameInput = document.getElementById('edit_fullname');
        const birthdateInput = document.getElementById('edit_birthdate');
        
        validateFullName(fullnameInput, 'edit');
        validateBirthDate(birthdateInput, 'edit');
        
        if (fullnameInput.classList.contains('is-invalid') || 
            birthdateInput.classList.contains('is-invalid')) {
            e.preventDefault();
            return false;
        }
    });
</script>
@endpush

