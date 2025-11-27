@extends('layouts.admin')

@section('title', 'Quản lý vai trò')

@section('content')
<!-- Success Notification Modal -->
@if (session('success'))
    <div class="modal fade role-modal modal-success" id="successNotificationModal" tabindex="-1" aria-hidden="true">
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
    <div class="modal fade role-modal modal-error" id="errorNotificationModal" tabindex="-1" aria-hidden="true">
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
                <h3 class="page-title">Quản lý vai trò</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý vai trò</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('admin.role.index') }}">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã vai trò</label>
                    <input type="text" name="search_id" class="form-control" placeholder="Tìm kiếm theo mã vai trò ..." value="{{ request('search_id') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Tên vai trò</label>
                    <input type="text" name="search_name" class="form-control" placeholder="Tìm kiếm theo tên vai trò ..." value="{{ request('search_name') }}">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Mô tả</label>
                    <input type="text" name="search_description" class="form-control" placeholder="Tìm kiếm theo mô tả ..." value="{{ request('search_description') }}">
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
                            <h3 class="page-title">Danh sách vai trò</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                                <i class="fas fa-plus me-1"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped role-table">
                        <thead class="student-thread">
                            <tr>
                                <th>Mã vai trò</th>
                                <th>Tên vai trò</th>
                                <th>Mô tả</th>
                                <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                <tr>
                                    <td style="padding-left: 60px !important;">{{ $role->roleid }}</td>
                                    <td>{{ $role->rolename }}</td>
                                    <td>{{ $role->roledescription ?? '-' }}</td>
                                    <td class="text-end">
                                        <div class="btn-group role-actions" role="group">
                                            <a href="#" class="btn btn-warning btn-sm rounded-pill me-1 text-white view-role-btn"
                                               data-bs-toggle="modal" data-bs-target="#view_role"
                                               title="Xem chi tiết"
                                               data-role-id="{{ $role->roleid }}"
                                               data-role-name="{{ htmlspecialchars($role->rolename, ENT_QUOTES, 'UTF-8') }}"
                                               data-role-description="{{ htmlspecialchars($role->roledescription ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-success btn-sm rounded-pill me-1 text-white edit-role-btn"
                                               data-bs-toggle="modal" data-bs-target="#edit_role"
                                               title="Chỉnh sửa"
                                               data-role-id="{{ $role->roleid }}"
                                               data-role-name="{{ htmlspecialchars($role->rolename, ENT_QUOTES, 'UTF-8') }}"
                                               data-role-description="{{ htmlspecialchars($role->roledescription ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="#" class="btn btn-danger btn-sm rounded-pill text-white delete-role-btn"
                                               data-bs-toggle="modal" data-bs-target="#delete_role"
                                               title="Xóa"
                                               data-role-id="{{ $role->roleid }}"
                                               data-role-name="{{ htmlspecialchars($role->rolename, ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Chưa có dữ liệu vai trò.</td>
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
<div class="modal fade role-modal" id="createRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Thêm vai trò mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.role.store') }}" method="post" id="createRoleForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên vai trò <span class="text-danger">*</span></label>
                        <input type="text" name="rolename" id="create_rolename" class="form-control" required autofocus
                               value="{{ old('rolename') }}" maxlength="80" minlength="2"
                               oninput="validateRoleName(this, 'create')">
                        <div id="create_rolename_error" class="invalid-feedback"></div>
                        @error('rolename')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="roledescription" id="create_roledescription" rows="3" class="form-control" placeholder="Nhập mô tả (không bắt buộc)">{{ old('roledescription') }}</textarea>
                        @error('roledescription')
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
<div class="modal fade role-modal" id="view_role" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết vai trò</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Mã vai trò</label>
                    <input type="text" id="view_roleid" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tên vai trò</label>
                    <input type="text" id="view_rolename" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea id="view_roledescription" rows="3" class="form-control" readonly></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade role-modal" id="edit_role" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chỉnh sửa vai trò</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRoleForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên vai trò <span class="text-danger">*</span></label>
                        <input type="text" name="rolename" id="edit_rolename" class="form-control" required maxlength="80" minlength="2"
                               oninput="validateRoleName(this, 'edit')">
                        <div id="edit_rolename_error" class="invalid-feedback"></div>
                        @error('rolename')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="roledescription" id="edit_roledescription" rows="3" class="form-control"></textarea>
                        @error('roledescription')
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
<div class="modal fade role-modal modal-delete" id="delete_role" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xóa vai trò</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteRoleForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa vai trò <strong id="delete_role_name">này</strong> không?</p>
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
    .role-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        padding: 0.75rem;
    }
    
    .role-table tbody td {
        font-size: 1rem;
        padding: 0.75rem;
    }

    /* Ẩn biểu tượng sắp xếp trong header */
    .role-table thead th {
        cursor: default !important;
    }
    
    .role-table thead th.sorting::before,
    .role-table thead th.sorting::after,
    .role-table thead th.sorting_asc::before,
    .role-table thead th.sorting_asc::after,
    .role-table thead th.sorting_desc::before,
    .role-table thead th.sorting_desc::after,
    .role-table thead th.sorting_asc_disabled::before,
    .role-table thead th.sorting_asc_disabled::after,
    .role-table thead th.sorting_desc_disabled::before,
    .role-table thead th.sorting_desc_disabled::after,
    .role-table thead th::before,
    .role-table thead th::after {
        display: none !important;
        content: none !important;
    }

    .role-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .role-table td {
        vertical-align: middle;
    }

    .role-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .role-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .role-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .role-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .role-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }

    /* Notification Modal Styles */
    .role-modal.modal-success .modal-header {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .role-modal.modal-success .modal-footer {
        background: #ecfdf5;
        justify-content: center;
    }

    .role-modal.modal-success .modal-footer .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
    }

    .role-modal.modal-success .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #059669, #047857);
    }

    .role-modal.modal-error .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .role-modal.modal-error .modal-footer {
        background: #fef2f2;
        justify-content: center;
    }

    .role-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .role-modal.modal-delete .modal-footer {
        background: #fef2f2;
    }

    .role-modal .form-label {
        font-weight: 500;
        color: #1f2937;
    }

    .role-modal .form-control,
    .role-modal .form-select {
        border-radius: 0.65rem;
        border-color: #dbe3f2;
        padding: 0.6rem 0.85rem;
        box-shadow: none;
    }

    /* Validation styles */
    .role-modal .form-control.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .role-modal .form-control.is-valid {
        border-color: #198754;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .role-modal .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .role-modal textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .role-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    .role-actions .btn {
        transition: all .2s ease;
    }

    .role-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(148, 163, 184, 0.25);
    }

    /* Cách trái 30px cho cột Thao tác */
    .role-table th.text-end,
    .role-table td.text-end {
        padding-left: 30px !important;
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
            if (e.target.closest('.view-role-btn')) {
                const btn = e.target.closest('.view-role-btn');
                document.getElementById('view_roleid').value = btn.dataset.roleId || '';
                document.getElementById('view_rolename').value = btn.dataset.roleName || '';
                document.getElementById('view_roledescription').value = btn.dataset.roleDescription || '';
            }
        });

        // Edit button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-role-btn')) {
                const btn = e.target.closest('.edit-role-btn');
                const roleId = btn.dataset.roleId;
                const form = document.getElementById('editRoleForm');
                form.action = '{{ route("admin.role.update", ":id") }}'.replace(':id', roleId);
                document.getElementById('edit_rolename').value = btn.dataset.roleName || '';
                document.getElementById('edit_roledescription').value = btn.dataset.roleDescription || '';
            }
        });

        // Delete button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-role-btn')) {
                const btn = e.target.closest('.delete-role-btn');
                const roleId = btn.dataset.roleId;
                const form = document.getElementById('deleteRoleForm');
                form.action = '{{ route("admin.role.destroy", ":id") }}'.replace(':id', roleId);
                document.getElementById('delete_role_name').textContent = btn.dataset.roleName || 'này';
            }
        });
    });

    // Danh sách cụm từ hợp lệ cho tên vai trò
    const validPhrases = [
        // Quản trị
        'quản trị viên', 'quản trị', 'admin', 'administrator',
        // Kế toán
        'kế toán', 'accountant',
        // Nhân sự
        'nhân sự', 'human resources', 'hr',
        // Giáo viên
        'giáo viên', 'teacher',
        // Ban giám hiệu
        'hiệu trưởng', 'principal', 'headmaster',
        'phó hiệu trưởng', 'vice principal', 'deputy principal',
        // Quản lý
        'quản lý', 'manager',
        'quản lý hệ thống', 'system manager',
        // Nhân viên
        'nhân viên', 'employee', 'staff',
        'nhân viên hành chính', 'administrative staff',
        'nhân viên văn phòng', 'office staff',
        // Kiểm tra
        'kiểm tra viên', 'auditor',
    ];

    // Kiểm tra tên vai trò có chứa ít nhất 1 cụm từ hợp lệ và không có ký tự lạ
    function containsValidPhrase(value) {
        const valueLower = value.toLowerCase().trim();
        const valueNormalized = valueLower.replace(/\s+/g, ' '); // Chuẩn hóa khoảng trắng
        
        // Kiểm tra xem có chứa cụm từ hợp lệ không
        let hasValidPhrase = false;
        let matchedPhrase = null;
        for (const phrase of validPhrases) {
            if (valueNormalized.includes(phrase)) {
                hasValidPhrase = true;
                matchedPhrase = phrase;
                break;
            }
        }
        
        if (!hasValidPhrase) {
            return false;
        }
        
        // Kiểm tra từ lặp lại (ví dụ: "Giáo viên viên", "Kế toán toán")
        const words = valueNormalized.split(/\s+/);
        let previousWord = '';
        for (const word of words) {
            const trimmedWord = word.trim();
            // Nếu từ hiện tại giống từ trước đó, có thể là lặp lại không hợp lệ
            if (previousWord && trimmedWord === previousWord && trimmedWord.length > 2) {
                return false; // Từ lặp lại
            }
            previousWord = trimmedWord;
        }
        
        // Kiểm tra xem có từ nào là phần cuối của cụm từ hợp lệ nhưng bị lặp lại không
        // Ví dụ: "giáo viên viên" - từ "viên" là phần cuối của "giáo viên" nhưng bị lặp
        const validWords = [];
        
        // Lấy tất cả từ trong các cụm từ hợp lệ
        for (const phrase of validPhrases) {
            const phraseWords = phrase.split(/\s+/);
            validWords.push(...phraseWords);
        }
        const uniqueValidWords = [...new Set(validWords)];
        
        // Kiểm tra từ cuối của mỗi cụm từ hợp lệ
        const lastWordsOfPhrases = [];
        for (const phrase of validPhrases) {
            const phraseWords = phrase.split(/\s+/);
            if (phraseWords.length > 0) {
                const lastWord = phraseWords[phraseWords.length - 1];
                lastWordsOfPhrases.push(lastWord);
            }
        }
        const uniqueLastWords = [...new Set(lastWordsOfPhrases)];
        
        // Kiểm tra xem có từ nào trong chuỗi nằm trong lastWordsOfPhrases nhưng bị lặp lại
        if (matchedPhrase) {
            const matchedPhraseWords = matchedPhrase.split(/\s+/);
            const matchedLastWord = matchedPhraseWords[matchedPhraseWords.length - 1];
            
            // Tìm vị trí của cụm từ hợp lệ trong chuỗi
            const phrasePos = valueNormalized.indexOf(matchedPhrase);
            const phraseEndPos = phrasePos + matchedPhrase.length;
            const afterPhrase = valueNormalized.substring(phraseEndPos).trim();
            
            // Kiểm tra xem phần sau cụm từ có chứa từ cuối của cụm từ không (lặp lại)
            if (afterPhrase) {
                const afterWords = afterPhrase.split(/\s+/);
                // Nếu từ đầu tiên sau cụm từ giống với từ cuối của cụm từ, đó là lặp lại
                if (afterWords.length > 0 && afterWords[0] === matchedLastWord && matchedLastWord.length > 2) {
                    return false; // Từ lặp lại sau cụm từ hợp lệ
                }
            }
        }
        
        // Kiểm tra xem có ký tự lạ không
        let testValue = valueNormalized;
        
        // Loại bỏ tất cả cụm từ hợp lệ
        for (const phrase of validPhrases) {
            testValue = testValue.replace(new RegExp(phrase.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g'), '');
        }
        
        // Loại bỏ các ký tự hợp lệ: chữ cái, số, khoảng trắng, dấu phẩy, chấm, gạch ngang, ngoặc
        testValue = testValue.replace(/[\p{L}\p{N}\s,.\-()]/gu, '');
        
        // Nếu còn ký tự nào, nghĩa là có ký tự lạ
        if (testValue.trim().length > 0) {
            return false;
        }
        
        // Kiểm tra từ đơn lẻ không hợp lệ
        // Kiểm tra xem có từ nào không nằm trong danh sách từ hợp lệ không
        // Nhưng cho phép các từ ngắn (1-2 ký tự) vì có thể là từ viết tắt hoặc số
        for (const word of words) {
            const trimmedWord = word.trim();
            if (trimmedWord.length > 2 && !uniqueValidWords.includes(trimmedWord)) {
                // Kiểm tra xem từ này có nằm trong cụm từ hợp lệ nào không
                let isPartOfPhrase = false;
                for (const phrase of validPhrases) {
                    if (phrase.includes(trimmedWord)) {
                        isPartOfPhrase = true;
                        break;
                    }
                }
                if (!isPartOfPhrase) {
                    return false;
                }
            }
        }
        
        return true;
    }

    // Validate tên vai trò real-time
    function validateRoleName(input, formType) {
        const value = input.value.trim();
        const errorDiv = document.getElementById(formType + '_rolename_error');
        let isValid = true;
        let errorMessage = '';

        // Xóa class cũ
        input.classList.remove('is-invalid', 'is-valid');

        // Kiểm tra độ dài
        if (value.length > 0 && value.length < 2) {
            isValid = false;
            errorMessage = 'Tên vai trò phải có ít nhất 2 ký tự';
        } else if (value.length > 80) {
            isValid = false;
            errorMessage = 'Tên vai trò không được vượt quá 80 ký tự';
        }
        // Kiểm tra định dạng (regex)
        else if (value.length > 0 && !/^[\p{L}\p{N}\s,.\-()]+$/u.test(value)) {
            isValid = false;
            errorMessage = 'Tên vai trò chỉ được chứa chữ cái, số, khoảng trắng và các ký tự: , . - ( )';
        }
        // Kiểm tra không được có nhiều khoảng trắng liên tiếp
        else if (value.length > 0 && /\s{2,}/.test(value)) {
            isValid = false;
            errorMessage = 'Tên vai trò không được có nhiều khoảng trắng liên tiếp';
        }
        // Kiểm tra cụm từ hợp lệ (chỉ kiểm tra khi đã nhập đủ 2 ký tự)
        else if (value.length >= 2 && !containsValidPhrase(value)) {
            isValid = false;
            errorMessage = 'Tên vai trò phải chứa ít nhất một cụm từ hợp lệ (ví dụ: quản trị viên, kế toán, giáo viên, hiệu trưởng, nhân sự, ...)';
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

    // Validate form trước khi submit
    document.getElementById('createRoleForm')?.addEventListener('submit', function(e) {
        const input = document.getElementById('create_rolename');
        validateRoleName(input, 'create');
        if (input.classList.contains('is-invalid')) {
            e.preventDefault();
            return false;
        }
    });

    document.getElementById('editRoleForm')?.addEventListener('submit', function(e) {
        const input = document.getElementById('edit_rolename');
        validateRoleName(input, 'edit');
        if (input.classList.contains('is-invalid')) {
            e.preventDefault();
            return false;
        }
    });
</script>
@endpush

