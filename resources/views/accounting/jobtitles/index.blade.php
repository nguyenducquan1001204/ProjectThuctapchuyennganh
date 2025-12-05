@extends('layouts.accounting')

@section('title', 'Quản lý chức danh')

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
                <h3 class="page-title">Quản lý chức danh</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('accounting.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý chức danh</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('accounting.jobtitle.index') }}">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã chức danh</label>
                    <input type="text" name="search_id" class="form-control" placeholder="Tìm kiếm theo mã chức danh ..." value="{{ request('search_id') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Tên chức danh</label>
                    <input type="text" name="search_name" class="form-control" placeholder="Tìm kiếm theo tên chức danh ..." value="{{ request('search_name') }}">
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
                            <h3 class="page-title">Danh sách chức danh</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createJobTitleModal">
                                <i class="fas fa-plus me-1"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped jobtitle-table">
                        <thead class="student-thread">
                            <tr>
                                <th>Mã chức danh</th>
                                <th>Tên chức danh</th>
                                <th>Mô tả</th>
                                <th  class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobTitles as $jobTitle)
                                <tr>
                                    <td class="text-end" style="padding-right: 100px !important;">{{ $jobTitle->jobtitleid }}</td>
                                    <td>{{ $jobTitle->jobtitlename }}</td>
                                    <td>{{ $jobTitle->jobtitledescription ?? '-' }}</td>
                                    <td class="text-end">
                                        <div class="btn-group jobtitle-actions" role="group">
                                            <a href="#" class="btn btn-warning btn-sm rounded-pill me-1 text-white view-jobtitle-btn"
                                               data-bs-toggle="modal" data-bs-target="#view_jobtitle"
                                               title="Xem chi tiết"
                                               data-jobtitle-id="{{ $jobTitle->jobtitleid }}"
                                               data-jobtitle-name="{{ htmlspecialchars($jobTitle->jobtitlename, ENT_QUOTES, 'UTF-8') }}"
                                               data-jobtitle-description="{{ htmlspecialchars($jobTitle->jobtitledescription ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-success btn-sm rounded-pill me-1 text-white edit-jobtitle-btn"
                                               data-bs-toggle="modal" data-bs-target="#edit_jobtitle"
                                               title="Chỉnh sửa"
                                               data-jobtitle-id="{{ $jobTitle->jobtitleid }}"
                                               data-jobtitle-name="{{ htmlspecialchars($jobTitle->jobtitlename, ENT_QUOTES, 'UTF-8') }}"
                                               data-jobtitle-description="{{ htmlspecialchars($jobTitle->jobtitledescription ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="#" class="btn btn-danger btn-sm rounded-pill text-white delete-jobtitle-btn"
                                               data-bs-toggle="modal" data-bs-target="#delete_jobtitle"
                                               title="Xóa"
                                               data-jobtitle-id="{{ $jobTitle->jobtitleid }}"
                                               data-jobtitle-name="{{ htmlspecialchars($jobTitle->jobtitlename, ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Chưa có dữ liệu chức danh.</td>
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
<div class="modal fade jobtitle-modal" id="createJobTitleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Thêm chức danh mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('accounting.jobtitle.store') }}" method="post" id="createJobTitleForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên chức danh <span class="text-danger">*</span></label>
                        <input type="text" name="jobtitlename" id="create_jobtitlename" class="form-control" required autofocus
                               value="{{ old('jobtitlename') }}" oninput="validateJobTitleInput(this, 'create')">
                        <div id="create_jobtitlename_error" class="invalid-feedback"></div>
                        @error('jobtitlename')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="jobtitledescription" rows="3" class="form-control" placeholder="Nhập mô tả (không bắt buộc)">{{ old('jobtitledescription') }}</textarea>
                        @error('jobtitledescription')
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
<div class="modal fade jobtitle-modal" id="view_jobtitle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết chức danh</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Mã chức danh</label>
                    <input type="text" id="view_jobtitleid" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tên chức danh</label>
                    <input type="text" id="view_jobtitlename" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea id="view_jobtitledescription" rows="3" class="form-control" readonly></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade jobtitle-modal" id="edit_jobtitle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chỉnh sửa chức danh</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editJobTitleForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên chức danh <span class="text-danger">*</span></label>
                        <input type="text" name="jobtitlename" id="edit_jobtitlename" class="form-control" required
                               oninput="validateJobTitleInput(this, 'edit')">
                        <div id="edit_jobtitlename_error" class="invalid-feedback"></div>
                        @error('jobtitlename')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="jobtitledescription" id="edit_jobtitledescription" rows="3" class="form-control"></textarea>
                        @error('jobtitledescription')
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
<div class="modal fade jobtitle-modal modal-delete" id="delete_jobtitle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xóa chức danh</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteJobTitleForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa chức danh <strong id="delete_jobtitle_name">này</strong> không?</p>
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
    .jobtitle-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        padding: 0.75rem;
    }
    
    .jobtitle-table tbody td {
        font-size: 1rem;
    }

    /* Ẩn biểu tượng sắp xếp trong header */
    .jobtitle-table thead th {
        cursor: default !important;
    }
    
    .jobtitle-table thead th.sorting::before,
    .jobtitle-table thead th.sorting::after,
    .jobtitle-table thead th.sorting_asc::before,
    .jobtitle-table thead th.sorting_asc::after,
    .jobtitle-table thead th.sorting_desc::before,
    .jobtitle-table thead th.sorting_desc::after,
    .jobtitle-table thead th.sorting_asc_disabled::before,
    .jobtitle-table thead th.sorting_asc_disabled::after,
    .jobtitle-table thead th.sorting_desc_disabled::before,
    .jobtitle-table thead th.sorting_desc_disabled::after,
    .jobtitle-table thead th::before,
    .jobtitle-table thead th::after {
        display: none !important;
        content: none !important;
    }

    .jobtitle-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .jobtitle-table td {
        vertical-align: middle;
    }

    .jobtitle-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .jobtitle-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .jobtitle-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .jobtitle-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .jobtitle-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }

    /* Notification Modal Styles */
    .jobtitle-modal.modal-success .modal-header {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .jobtitle-modal.modal-success .modal-footer {
        background: #ecfdf5;
        justify-content: center;
    }

    .jobtitle-modal.modal-success .modal-footer .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
    }

    .jobtitle-modal.modal-success .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #059669, #047857);
    }

    .jobtitle-modal.modal-error .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .jobtitle-modal.modal-error .modal-footer {
        background: #fef2f2;
        justify-content: center;
    }

    .jobtitle-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .jobtitle-modal.modal-delete .modal-footer {
        background: #fef2f2;
    }

    .jobtitle-modal .form-label {
        font-weight: 500;
        color: #1f2937;
    }

    .jobtitle-modal .form-control,
    .jobtitle-modal .form-select {
        border-radius: 0.65rem;
        border-color: #dbe3f2;
        padding: 0.6rem 0.85rem;
        box-shadow: none;
    }

    /* Validation styles */
    .jobtitle-modal .form-control.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .jobtitle-modal .form-control.is-valid {
        border-color: #198754;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .jobtitle-modal .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .jobtitle-modal textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .jobtitle-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    .jobtitle-actions .btn {
        transition: all .2s ease;
    }

    .jobtitle-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(148, 163, 184, 0.25);
    }

    /* Cách trái 30px cho cột Thao tác */
    .jobtitle-table th.text-end,
    .jobtitle-table td.text-end {
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
            lengthChange: false, // Ẩn phần "Show X entries"
            info: false, // Ẩn phần "Showing X to Y of Z entries"
            columnDefs: [
                { orderable: false, targets: '_all' } // Tắt sắp xếp cho tất cả các cột
            ],
            order: [] // Xóa sắp xếp mặc định
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
            if (e.target.closest('.view-jobtitle-btn')) {
                const btn = e.target.closest('.view-jobtitle-btn');
                document.getElementById('view_jobtitleid').value = btn.dataset.jobtitleId || '';
                document.getElementById('view_jobtitlename').value = btn.dataset.jobtitleName || '';
                document.getElementById('view_jobtitledescription').value = btn.dataset.jobtitleDescription || '';
            }
        });

        // Edit button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-jobtitle-btn')) {
                const btn = e.target.closest('.edit-jobtitle-btn');
                const jobtitleId = btn.dataset.jobtitleId;
                const form = document.getElementById('editJobTitleForm');
                form.action = '{{ route("accounting.jobtitle.update", ":id") }}'.replace(':id', jobtitleId);
                document.getElementById('edit_jobtitlename').value = btn.dataset.jobtitleName || '';
                document.getElementById('edit_jobtitledescription').value = btn.dataset.jobtitleDescription || '';
            }
        });

        // Delete button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-jobtitle-btn')) {
                const btn = e.target.closest('.delete-jobtitle-btn');
                const jobtitleId = btn.dataset.jobtitleId;
                const form = document.getElementById('deleteJobTitleForm');
                form.action = '{{ route("accounting.jobtitle.destroy", ":id") }}'.replace(':id', jobtitleId);
                document.getElementById('delete_jobtitle_name').textContent = btn.dataset.jobtitleName || 'này';
            }
        });
    });

    // Danh sách cụm từ hợp lệ cho tên chức danh
    const validPhrases = [
        // Ban giám hiệu
        'hiệu trưởng', 'phó hiệu trưởng', 'ban giám hiệu',
        // Tổ chuyên môn
        'tổ trưởng', 'phó tổ trưởng', 'tổ chuyên môn',
        // Môn học
        'giáo viên toán', 'giáo viên lý', 'giáo viên hóa', 'giáo viên sinh',
        'giáo viên sử', 'giáo viên địa', 'giáo viên văn', 'giáo viên anh',
        'giáo viên công nghệ', 'giáo viên tin học', 'giáo viên gdcd',
        'giáo viên thể dục', 'giáo viên nhạc', 'giáo viên họa',
        // Giáo viên
        'giáo viên', 'giảng viên', 'chủ nhiệm',
        // Chức danh quản lý
        'quản lý', 'phụ trách',
        // Công tác học sinh
        'công tác học sinh', 'tổng phụ trách đội', 'tư vấn tâm lý',
        // Văn phòng
        'văn phòng', 'hành chính', 'kế toán', 'thủ quỹ', 'thiết bị', 'thư viện',
        // Hỗ trợ
        'nhân viên y tế', 'bảo vệ', 'tạp vụ', 'phục vụ', 'kỹ thuật cơ sở',
        // Đoàn thể
        'thanh niên', 'ban đại diện cha mẹ'
    ];

    // Kiểm tra tên chức danh có chứa ít nhất 1 cụm từ hợp lệ và không có ký tự lạ
    function containsValidPhrase(value) {
        const valueLower = value.toLowerCase().trim();
        const valueNormalized = valueLower.replace(/\s+/g, ' '); // Chuẩn hóa khoảng trắng
        
        // Kiểm tra xem có chứa cụm từ hợp lệ không
        let hasValidPhrase = false;
        for (const phrase of validPhrases) {
            if (valueNormalized.includes(phrase)) {
                hasValidPhrase = true;
                break;
            }
        }
        
        if (!hasValidPhrase) {
            return false;
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
        const words = valueNormalized.split(/\s+/);
        const validWords = [];
        
        // Lấy tất cả từ trong các cụm từ hợp lệ
        for (const phrase of validPhrases) {
            const phraseWords = phrase.split(/\s+/);
            validWords.push(...phraseWords);
        }
        const uniqueValidWords = [...new Set(validWords)];
        
        // Kiểm tra từ không hợp lệ (từ dài > 2 ký tự và không nằm trong danh sách)
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

    // Validate tên chức danh real-time
    function validateJobTitleInput(input, formType) {
        const value = input.value.trim();
        const errorDiv = document.getElementById(formType + '_jobtitlename_error');
        let isValid = true;
        let errorMessage = '';

        // Xóa class cũ
        input.classList.remove('is-invalid', 'is-valid');

        // Kiểm tra độ dài
        if (value.length > 0 && value.length < 3) {
            isValid = false;
            errorMessage = 'Tên chức danh phải có ít nhất 3 ký tự';
        } else if (value.length > 150) {
            isValid = false;
            errorMessage = 'Tên chức danh không được vượt quá 150 ký tự';
        }
        // Kiểm tra định dạng (regex)
        else if (value.length > 0 && !/^[\p{L}\p{N}\s,.\-()]+$/u.test(value)) {
            isValid = false;
            errorMessage = 'Tên chức danh chỉ được chứa chữ cái, số, khoảng trắng và các ký tự: , . - ( )';
        }
        // Kiểm tra không được có nhiều khoảng trắng liên tiếp
        else if (value.length > 0 && /\s{2,}/.test(value)) {
            isValid = false;
            errorMessage = 'Tên chức danh không được có nhiều khoảng trắng liên tiếp';
        }
        // Kiểm tra cụm từ hợp lệ (chỉ kiểm tra khi đã nhập đủ 3 ký tự)
        else if (value.length >= 3 && !containsValidPhrase(value)) {
            isValid = false;
            errorMessage = 'Tên chức danh phải chứa ít nhất một cụm từ hợp lệ (ví dụ: hiệu trưởng, giáo viên, phó hiệu trưởng, ...)';
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
            // Nếu trống, xóa tất cả class và message
            input.classList.remove('is-invalid', 'is-valid');
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';
        }
    }

    // Validate form trước khi submit
    document.getElementById('createJobTitleForm')?.addEventListener('submit', function(e) {
        const input = document.getElementById('create_jobtitlename');
        validateJobTitleInput(input, 'create');
        if (input.classList.contains('is-invalid')) {
            e.preventDefault();
            return false;
        }
    });

    document.getElementById('editJobTitleForm')?.addEventListener('submit', function(e) {
        const input = document.getElementById('edit_jobtitlename');
        validateJobTitleInput(input, 'edit');
        if (input.classList.contains('is-invalid')) {
            e.preventDefault();
            return false;
        }
    });
</script>
@endpush
