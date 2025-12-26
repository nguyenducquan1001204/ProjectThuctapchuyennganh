@extends('layouts.admin')

@section('title', 'Quản lý người dùng hệ thống')

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
                <h3 class="page-title">Quản lý người dùng hệ thống</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý người dùng hệ thống</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('admin.systemuser.index') }}">
        <div class="row">
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Tên đăng nhập</label>
                    <input type="text" name="search_username" class="form-control"
                           placeholder="Tìm kiếm theo tên đăng nhập ..."
                           value="{{ request('search_username') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Email</label>
                    <input type="text" name="search_email" class="form-control"
                           placeholder="Tìm kiếm theo email ..."
                           value="{{ request('search_email') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Họ và tên</label>
                    <input type="text" name="search_fullname" class="form-control"
                           placeholder="Tìm kiếm theo họ và tên ..."
                           value="{{ request('search_fullname') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Trạng thái</label>
                    <select name="search_status" class="form-control">
                        <option value="">-- Tất cả --</option>
                        <option value="active" {{ request('search_status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="locked" {{ request('search_status') === 'locked' ? 'selected' : '' }}>Đã khóa</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Vai trò</label>
                    <select name="search_roleid" class="form-control">
                        <option value="">-- Tất cả --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->roleid }}" {{ (string)request('search_roleid') === (string)$role->roleid ? 'selected' : '' }}>
                                {{ $role->rolename }}
                            </option>
                        @endforeach
                    </select>
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
                            <h3 class="page-title">Danh sách người dùng hệ thống</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSystemUserModal">
                                <i class="fas fa-plus me-1"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped systemuser-table">
                        <thead class="student-thread">
                            <tr>
                                <th>Mã người dùng</th>
                                <th>Ảnh</th>
                                <th>Tên đăng nhập</th>
                                <th>Họ và tên</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td style="padding-left: 60px !important;">{{ $user->userid }}</td>
                                    <td>
                                        @php
                                            $avatarUrl = $user->avatar
                                                ? asset('storage/'.$user->avatar)
                                                : asset('assets/img/cropped_circle_image.png');
                                        @endphp
                                        <img src="{{ $avatarUrl }}" alt="Avatar" class="rounded-circle" width="32" height="32">
                                    </td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->fullname ?? '-' }}</td>
                                    <td>{{ $user->email ?? '-' }}</td>
                                    <td>{{ optional($user->role)->rolename ?? '-' }}</td>
                                    <td>
                                        @if($user->status === 'active')
                                            <span class="badge bg-success">Đang hoạt động</span>
                                        @else
                                            <span class="badge bg-danger">Đã khóa</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group systemuser-actions" role="group">
                                            <a href="#"
                                               class="btn btn-warning btn-sm rounded-pill me-1 text-white view-systemuser-btn"
                                               data-bs-toggle="modal" data-bs-target="#view_systemuser"
                                               title="Xem chi tiết"
                                               data-user-id="{{ $user->userid }}"
                                               data-username="{{ htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8') }}"
                                               data-fullname="{{ htmlspecialchars($user->fullname ?? '', ENT_QUOTES, 'UTF-8') }}"
                                               data-email="{{ htmlspecialchars($user->email ?? '', ENT_QUOTES, 'UTF-8') }}"
                                               data-teacher-name="{{ htmlspecialchars(optional($user->teacher)->fullname ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-role-name="{{ htmlspecialchars(optional($user->role)->rolename ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-status="{{ $user->status }}"
                                               data-avatar-url="{{ $avatarUrl }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#"
                                               class="btn btn-success btn-sm rounded-pill me-1 text-white edit-systemuser-btn"
                                               data-bs-toggle="modal" data-bs-target="#edit_systemuser"
                                               title="Chỉnh sửa"
                                               data-user-id="{{ $user->userid }}"
                                               data-username="{{ htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8') }}"
                                               data-fullname="{{ htmlspecialchars($user->fullname ?? '', ENT_QUOTES, 'UTF-8') }}"
                                               data-email="{{ htmlspecialchars($user->email ?? '', ENT_QUOTES, 'UTF-8') }}"
                                               data-teacher-id="{{ $user->teacherid }}"
                                               data-role-id="{{ $user->roleid }}"
                                               data-status="{{ $user->status }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="#"
                                               class="btn btn-danger btn-sm rounded-pill text-white delete-systemuser-btn"
                                               data-bs-toggle="modal" data-bs-target="#delete_systemuser"
                                               title="Xóa"
                                               data-user-id="{{ $user->userid }}"
                                               data-username="{{ htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Chưa có dữ liệu người dùng hệ thống.</td>
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
<div class="modal fade systemuser-modal" id="createSystemUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Thêm tài khoản người dùng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.systemuser.store') }}" method="post" id="createSystemUserForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ảnh đại diện</label>
                        <input type="file" name="avatar" class="form-control" accept="image/*">
                        @error('avatar')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                        <select name="roleid" id="create_roleid" class="form-control" required>
                            <option value="">-- Chọn vai trò --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->roleid }}" {{ old('roleid') == $role->roleid ? 'selected' : '' }}>
                                    {{ $role->rolename }}
                                </option>
                            @endforeach
                        </select>
                        @error('roleid')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="create_email" class="form-control"
                               value="{{ old('email') }}" oninput="validateEmailInput(this, 'create')">
                        <div id="create_email_error" class="invalid-feedback"></div>
                        @error('email')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập (tự tạo từ email)</label>
                        <input type="text" name="username" id="create_username" class="form-control" readonly
                               value="{{ old('username') }}">
                        <div id="create_username_error" class="invalid-feedback"></div>
                        @error('username')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="create_fullname_group" style="display: none;">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="fullname" id="create_fullname" class="form-control"
                               value="{{ old('fullname') }}">
                        @error('fullname')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="create_teacherid_group" style="display: none;">
                        <label class="form-label">Giáo viên (nếu là giáo viên)</label>
                        <select name="teacherid" id="create_teacherid" class="form-control">
                            <option value="">-- Không liên kết giáo viên --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->teacherid }}" {{ old('teacherid') == $teacher->teacherid ? 'selected' : '' }}>
                                    {{ $teacher->fullname }}
                                </option>
                            @endforeach
                        </select>
                        @error('teacherid')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" id="create_status" class="form-control">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="locked" {{ old('status') === 'locked' ? 'selected' : '' }}>Đã khóa</option>
                        </select>
                        @error('status')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <small class="text-muted">
                        Mật khẩu sẽ được hệ thống tự động sinh ngẫu nhiên và gửi tới email (nếu có).
                    </small>
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
<div class="modal fade systemuser-modal" id="view_systemuser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Thông tin tài khoản</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 text-center">
                    <label class="form-label d-block">Ảnh đại diện</label>
                    <img id="view_avatar" src="{{ asset('assets/img/cropped_circle_image.png') }}" alt="Avatar"
                         class="rounded-circle mb-2" width="64" height="64">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mã người dùng</label>
                    <input type="text" id="view_userid" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tên đăng nhập</label>
                    <input type="text" id="view_username" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Họ và tên</label>
                    <input type="text" id="view_fullname" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="text" id="view_email" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giáo viên</label>
                    <input type="text" id="view_teacher" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Vai trò</label>
                    <input type="text" id="view_role" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <input type="text" id="view_status" class="form-control" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade systemuser-modal" id="edit_systemuser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chỉnh sửa tài khoản</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSystemUserForm" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <label class="form-label d-block">Ảnh đại diện hiện tại</label>
                        <img id="edit_avatar_preview" src="{{ asset('assets/img/cropped_circle_image.png') }}" alt="Avatar"
                             class="rounded-circle mb-2" width="64" height="64">
                        <div>
                            <label class="form-label mt-2">Đổi ảnh đại diện</label>
                            <input type="file" name="avatar" class="form-control" accept="image/*">
                        </div>
                        @error('avatar')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập</label>
                        <input type="text" id="edit_username" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                        <select name="roleid" id="edit_roleid" class="form-control" required>
                            <option value="">-- Chọn vai trò --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->roleid }}">{{ $role->rolename }}</option>
                            @endforeach
                        </select>
                        @error('roleid')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control"
                               oninput="validateEmailInput(this, 'edit')">
                        <div id="edit_email_error" class="invalid-feedback"></div>
                        @error('email')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="edit_fullname_group" style="display: none;">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" id="edit_fullname" class="form-control" readonly>
                    </div>
                    <div class="mb-3" id="edit_teacherid_group" style="display: none;">
                        <label class="form-label">Giáo viên (nếu là giáo viên)</label>
                        <select id="edit_teacherid" class="form-control" disabled>
                            <option value="">-- Không liên kết giáo viên --</option>
                            @foreach($allTeachers as $teacher)
                                <option value="{{ $teacher->teacherid }}">{{ $teacher->fullname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option value="active">Đang hoạt động</option>
                            <option value="locked">Đã khóa</option>
                        </select>
                        @error('status')
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
<div class="modal fade systemuser-modal modal-delete" id="delete_systemuser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xóa tài khoản</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteSystemUserForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa tài khoản <strong id="delete_username"></strong> không?</p>
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
    .systemuser-table thead th,
    .systemuser-table tbody td {
        font-size: 1rem;
    }

    .systemuser-table thead th {
        font-weight: 600;
    }

    .systemuser-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .systemuser-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .systemuser-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .systemuser-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .systemuser-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }

    .systemuser-modal.modal-success .modal-header {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .systemuser-modal.modal-success .modal-footer {
        background: #ecfdf5;
        justify-content: center;
    }

    .systemuser-modal.modal-success .modal-footer .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
    }

    .systemuser-modal.modal-success .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #059669, #047857);
    }

    .systemuser-modal.modal-error .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .systemuser-modal.modal-error .modal-footer {
        background: #fef2f2;
        justify-content: center;
    }

    .systemuser-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .systemuser-modal.modal-delete .modal-footer {
        background: #fef2f2;
    }

    .systemuser-modal .form-label {
        font-weight: 500;
        color: #1f2937;
    }

    .systemuser-modal .form-control,
    .systemuser-modal .form-select,
    .systemuser-modal select.form-control {
        border-radius: 0.65rem;
        border-color: #dbe3f2;
        padding: 0.6rem 0.85rem;
        box-shadow: none;
    }

    .systemuser-modal .form-control.is-invalid {
        border-color: #dc3545;
    }

    .systemuser-modal .form-control.is-valid {
        border-color: #198754;
    }

    .systemuser-modal .invalid-feedback {
        display: block;
        width: 100%;
    }

    .systemuser-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
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
            if (e.target.closest('.view-systemuser-btn')) {
                const btn = e.target.closest('.view-systemuser-btn');
                document.getElementById('view_userid').value = btn.dataset.userId || '';
                document.getElementById('view_username').value = btn.dataset.username || '';
                document.getElementById('view_fullname').value = btn.dataset.fullname || '';
                document.getElementById('view_email').value = btn.dataset.email || '';
                document.getElementById('view_teacher').value = btn.dataset.teacherName || '-';
                document.getElementById('view_role').value = btn.dataset.roleName || '-';
                document.getElementById('view_status').value = btn.dataset.status === 'active' ? 'Đang hoạt động' : 'Đã khóa';

                const avatarImg = document.getElementById('view_avatar');
                if (avatarImg) {
                    avatarImg.src = btn.dataset.avatarUrl || "{{ asset('assets/img/cropped_circle_image.png') }}";
                }
            }
        });

        // Edit button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-systemuser-btn')) {
                const btn = e.target.closest('.edit-systemuser-btn');
                const userId = btn.dataset.userId;
                const form = document.getElementById('editSystemUserForm');
                form.action = '{{ route("admin.systemuser.update", ":id") }}'.replace(':id', userId);

                document.getElementById('edit_username').value = btn.dataset.username || '';
                document.getElementById('edit_email').value = btn.dataset.email || '';
                document.getElementById('edit_fullname').value = btn.dataset.fullname || '';

                const teacherSelect = document.getElementById('edit_teacherid');
                if (teacherSelect) {
                    teacherSelect.value = btn.dataset.teacherId || '';
                }

                const roleSelect = document.getElementById('edit_roleid');
                if (roleSelect) {
                    roleSelect.value = btn.dataset.roleId || '';
                }

                const statusSelect = document.getElementById('edit_status');
                if (statusSelect) {
                    statusSelect.value = btn.dataset.status || 'active';
                }

                const avatarPreview = document.getElementById('edit_avatar_preview');
                if (avatarPreview && btn.dataset.avatarUrl) {
                    avatarPreview.src = btn.dataset.avatarUrl;
                }

                // Cập nhật hiển thị Họ tên / Giáo viên theo vai trò khi mở modal sửa
                updateSystemUserFormVisibility('edit');
            }
        });

        // Delete button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-systemuser-btn')) {
                const btn = e.target.closest('.delete-systemuser-btn');
                const userId = btn.dataset.userId;
                const form = document.getElementById('deleteSystemUserForm');
                form.action = '{{ route("admin.systemuser.destroy", ":id") }}'.replace(':id', userId);
                document.getElementById('delete_username').textContent = btn.dataset.username || 'này';
            }
        });
    });

    // Cập nhật hiển thị ô Họ tên / Giáo viên theo vai trò
    function updateSystemUserFormVisibility(formType) {
        const roleSelect = document.getElementById(formType + '_roleid');
        if (!roleSelect) return;

        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
        const roleText = selectedOption ? selectedOption.text.toLowerCase() : '';

        const fullnameGroup = document.getElementById(formType + '_fullname_group');
        const teacherGroup = document.getElementById(formType + '_teacherid_group');
        const fullnameInput = document.getElementById(formType + '_fullname');
        const teacherSelect = document.getElementById(formType + '_teacherid');

        // Mặc định: chưa chọn vai trò -> ẩn cả hai
        let showFullname = false;
        let showTeacher = false;

        // Nếu chưa chọn vai trò (value rỗng) thì ẩn hết
        if (!roleSelect.value) {
            showFullname = false;
            showTeacher = false;
        } else {
            // Nếu là Admin / Quản trị viên: yêu cầu Họ tên, ẩn Giáo viên
            if (roleText.includes('admin') || roleText.includes('quản trị')) {
                showFullname = true;
                showTeacher = false;
            }
            // Nếu là Kế toán hoặc Giáo viên: ẩn Họ tên, yêu cầu chọn Giáo viên
            else if (roleText.includes('kế toán') || roleText.includes('giáo viên')) {
                showFullname = false;
                showTeacher = true;
            } else {
                // Các vai trò khác: có thể cho phép nhập Họ tên, không bắt buộc giáo viên
                showFullname = true;
                showTeacher = false;
            }
        }


        if (fullnameGroup) {
            fullnameGroup.style.display = showFullname ? '' : 'none';
        }
        if (teacherGroup) {
            teacherGroup.style.display = showTeacher ? '' : 'none';
        }

        if (fullnameInput) {
            if (showFullname) {
                fullnameInput.required = true;
            } else {
                fullnameInput.required = false;
            }
        }

        if (teacherSelect) {
            if (showTeacher) {
                teacherSelect.required = true;
            } else {
                teacherSelect.required = false;
            }
        }
    }

    // Validate username real-time
    function validateUsername(input, formType) {
        const value = input.value.trim();
        const errorDiv = document.getElementById(formType + '_username_error');
        let isValid = true;
        let errorMessage = '';

        input.classList.remove('is-invalid', 'is-valid');

        if (value.length > 0 && value.length < 4) {
            isValid = false;
            errorMessage = 'Tên đăng nhập phải có ít nhất 4 ký tự';
        } else if (value.length > 60) {
            isValid = false;
            errorMessage = 'Tên đăng nhập không được vượt quá 60 ký tự';
        } else if (value.length > 0 && !/^[A-Za-z0-9_.]+$/.test(value)) {
            isValid = false;
            errorMessage = 'Tên đăng nhập chỉ được chứa chữ cái, số, dấu chấm và gạch dưới';
        }

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

    // Áp dụng hiển thị ban đầu cho form tạo mới
    document.getElementById('create_roleid')?.addEventListener('change', function() {
        updateSystemUserFormVisibility('create');
    });
    updateSystemUserFormVisibility('create');

    // Validate email real-time + check tồn tại + gợi ý username
    async function validateEmailInput(input, formType) {
        const value = input.value.trim();
        const errorDiv = document.getElementById(formType + '_email_error');
        let isValid = true;
        let errorMessage = '';

        input.classList.remove('is-invalid', 'is-valid');

        const usernameInput = document.getElementById(formType + '_username');
        if (usernameInput) {
            usernameInput.value = '';
            usernameInput.classList.remove('is-invalid', 'is-valid');
            const usernameError = document.getElementById(formType + '_username_error');
            if (usernameError) {
                usernameError.textContent = '';
                usernameError.style.display = 'none';
            }
        }

        if (value.length === 0) {
            input.classList.remove('is-invalid', 'is-valid');
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';
            return;
        }

        if (value.length > 255) {
            isValid = false;
            errorMessage = 'Email không được vượt quá 255 ký tự';
        } else {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Email không hợp lệ';
            }
        }

        if (!isValid) {
            input.classList.add('is-invalid');
            errorDiv.textContent = errorMessage;
            errorDiv.style.display = 'block';
            return;
        }

        // Nếu email hợp lệ, gọi API kiểm tra tồn tại và gợi ý username (chỉ áp dụng cho form create)
        if (formType === 'create') {
            try {
                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '';

                const response = await fetch('{{ route("admin.systemuser.checkEmail") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email: value }),
                });

                const data = await response.json();

                if (!response.ok || !data.ok) {
                    input.classList.add('is-invalid');
                    errorDiv.textContent = data.message || 'Email không hợp lệ hoặc đã tồn tại';
                    errorDiv.style.display = 'block';
                    if (usernameInput) {
                        usernameInput.value = '';
                    }
                    return;
                }

                // Email OK, set valid và gợi ý username
                input.classList.add('is-valid');
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';

                if (usernameInput && data.username) {
                    usernameInput.value = data.username;
                    usernameInput.classList.add('is-valid');
                }
            } catch (error) {
                // Lỗi mạng hoặc server: chỉ validate format, không gợi ý username
                input.classList.add('is-valid');
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
        } else {
            // Form edit: chỉ kiểm tra định dạng trên frontend, unique sẽ để backend xử lý
            input.classList.add('is-valid');
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';
        }
    }

    // Validate form trước khi submit
    document.getElementById('createSystemUserForm')?.addEventListener('submit', function(e) {
        const usernameInput = document.getElementById('create_username');
        validateUsername(usernameInput, 'create');
        if (usernameInput.classList.contains('is-invalid')) {
            e.preventDefault();
            return false;
        }

        const emailInput = document.getElementById('create_email');
        if (emailInput && emailInput.value.trim().length > 0) {
            validateEmailInput(emailInput, 'create');
            if (emailInput.classList.contains('is-invalid')) {
                e.preventDefault();
                return false;
            }
        }
    });

    document.getElementById('edit_roleid')?.addEventListener('change', function() {
        updateSystemUserFormVisibility('edit');
    });

    document.getElementById('editSystemUserForm')?.addEventListener('submit', function(e) {
        const usernameInput = document.getElementById('edit_username');
        validateUsername(usernameInput, 'edit');
        if (usernameInput.classList.contains('is-invalid')) {
            e.preventDefault();
            return false;
        }

        const emailInput = document.getElementById('edit_email');
        if (emailInput && emailInput.value.trim().length > 0) {
            validateEmailInput(emailInput, 'edit');
            if (emailInput.classList.contains('is-invalid')) {
                e.preventDefault();
                return false;
            }
        }
    });
</script>
@endpush


