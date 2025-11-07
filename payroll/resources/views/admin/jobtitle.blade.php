@extends('layouts.admin')

@php($asset = asset('assets'))

@section('content')
    <!-- Success Notification Modal -->
    @if (session('success'))
        <div class="modal fade jobtitle-modal modal-success" id="successNotificationModal" tabindex="-1" aria-hidden="true">
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
        <div class="modal fade jobtitle-modal modal-error" id="errorNotificationModal" tabindex="-1" aria-hidden="true">
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
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Quản lý chức danh</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý chức danh</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="student-group-form mb-4">
        <form action="{{ route('admin.roles.index') }}" method="get">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Tìm kiếm theo tên chức danh ...">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="search-student-btn">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-white">
            <h5 class="mb-0">Danh sách chức danh</h5>
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createJobTitleModal">
                <i class="fas fa-plus me-1"></i> Thêm mới
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped jobtitle-table">
                    <thead class="student-thread">
                        <tr>
                            <th class="text-center">Mã chức danh</th>
                            <th>Tên chức danh</th>
                            <th class="text-center">Mô tả</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jobtitles as $jobtitle)
                            <tr>
                                <td class="text-center fw-semibold">{{ $jobtitle->jobtitleid }}</td>
                                <td class="fw-semibold">{{ $jobtitle->jobtitlename }}</td>
                                <td>{{ $jobtitle->jobtitledescription ?? '—' }}</td>
                                <td class="text-center">
                                    <div class="btn-group jobtitle-actions" role="group">
                                        <a href="#" class="btn btn-warning btn-sm rounded-pill me-1 text-white view-jobtitle-btn"
                                           data-bs-toggle="modal" data-bs-target="#view_jobtitle"
                                           title="Xem chi tiết"
                                           data-jobtitle-id="{{ $jobtitle->jobtitleid }}"
                                           data-jobtitle-name="{{ htmlspecialchars($jobtitle->jobtitlename, ENT_QUOTES, 'UTF-8') }}"
                                           data-jobtitle-description="{{ htmlspecialchars($jobtitle->jobtitledescription ?? '', ENT_QUOTES, 'UTF-8') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-success btn-sm rounded-pill me-1 text-white edit-jobtitle-btn"
                                           data-bs-toggle="modal" data-bs-target="#edit_jobtitle"
                                           title="Chỉnh sửa"
                                           data-jobtitle-id="{{ $jobtitle->jobtitleid }}"
                                           data-jobtitle-name="{{ htmlspecialchars($jobtitle->jobtitlename, ENT_QUOTES, 'UTF-8') }}"
                                           data-jobtitle-description="{{ htmlspecialchars($jobtitle->jobtitledescription ?? '', ENT_QUOTES, 'UTF-8') }}">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a href="#" class="btn btn-danger btn-sm rounded-pill text-white delete-jobtitle-btn"
                                           data-bs-toggle="modal" data-bs-target="#delete_jobtitle"
                                           title="Xóa"
                                           data-jobtitle-id="{{ $jobtitle->jobtitleid }}"
                                           data-jobtitle-name="{{ htmlspecialchars($jobtitle->jobtitlename, ENT_QUOTES, 'UTF-8') }}">
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

    <!-- Create Modal -->
    <div class="modal fade jobtitle-modal" id="createJobTitleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title">Thêm chức danh mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.roles.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tên chức danh <span class="text-danger">*</span></label>
                            <input type="text" name="jobtitlename" class="form-control" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="jobtitledescription" rows="3" class="form-control" placeholder="Nhập mô tả (không bắt buộc)"></textarea>
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
                            <input type="text" name="jobtitlename" id="edit_jobtitlename" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="jobtitledescription" id="edit_jobtitledescription" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-success">Lưu thay đổi</button>
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
<link rel="stylesheet" href="{{ $asset }}/plugins/datatables/datatables.min.css">
<style>
    .jobtitle-table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: .05rem;
        color: #64748b;
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
        background: linear-gradient(135deg, #60a5fa, #2563eb);
    }

    .jobtitle-modal.modal-success .modal-footer {
        background: #eff6ff;
        justify-content: center;
    }

    .jobtitle-modal.modal-success .modal-footer .btn-primary {
        background: linear-gradient(135deg, #60a5fa, #2563eb);
        border: none;
    }

    .jobtitle-modal.modal-success .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #3b82f6, #1e40af);
    }

    .jobtitle-modal.modal-error .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .jobtitle-modal.modal-error .modal-footer {
        background: #fef2f2;
        justify-content: center;
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

    .jobtitle-modal textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .jobtitle-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    .jobtitle-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .jobtitle-modal.modal-delete .modal-footer {
        background: #fef2f2;
    }

    .jobtitle-actions .btn {
        transition: all .2s ease;
    }

    .jobtitle-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(148, 163, 184, 0.25);
    }
</style>
@endpush

@push('scripts')
<script src="{{ $asset }}/plugins/datatables/datatables.min.js"></script>
<script>
    (function() {
        const updateRouteTemplate = '{{ route("admin.roles.update", ["jobtitle" => "__ID__"]) }}';
        const deleteRouteTemplate = '{{ route("admin.roles.destroy", ["jobtitle" => "__ID__"]) }}';

        // View button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.view-jobtitle-btn')) {
                const btn = e.target.closest('.view-jobtitle-btn');
                const data = {
                    jobtitleid: btn.dataset.jobtitleId,
                    jobtitlename: btn.dataset.jobtitleName || '',
                    jobtitledescription: btn.dataset.jobtitleDescription || ''
                };
                fillViewJobTitle(data);
            }
        });

        // Edit button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-jobtitle-btn')) {
                const btn = e.target.closest('.edit-jobtitle-btn');
                const data = {
                    jobtitleid: btn.dataset.jobtitleId,
                    jobtitlename: btn.dataset.jobtitleName || '',
                    jobtitledescription: btn.dataset.jobtitleDescription || ''
                };
                fillEditJobTitle(data);
            }
        });

        // Delete button handler
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-jobtitle-btn')) {
                const btn = e.target.closest('.delete-jobtitle-btn');
                setDeleteJobTitleId(btn.dataset.jobtitleId, btn.dataset.jobtitleName || '');
            }
        });

        function fillViewJobTitle(data) {
            document.getElementById('view_jobtitleid').value = data.jobtitleid ?? '';
            document.getElementById('view_jobtitlename').value = data.jobtitlename ?? '';
            document.getElementById('view_jobtitledescription').value = data.jobtitledescription ?? '';
        }

        function fillEditJobTitle(data) {
            const form = document.getElementById('editJobTitleForm');
            form.action = updateRouteTemplate.replace('__ID__', data.jobtitleid);
            document.getElementById('edit_jobtitlename').value = data.jobtitlename ?? '';
            document.getElementById('edit_jobtitledescription').value = data.jobtitledescription ?? '';
        }

        function setDeleteJobTitleId(id, name) {
            const form = document.getElementById('deleteJobTitleForm');
            form.action = deleteRouteTemplate.replace('__ID__', id);
            document.getElementById('delete_jobtitle_name').textContent = name || 'này';
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            tooltipTriggerList.forEach(function (el) {
                new bootstrap.Tooltip(el);
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
        });
    })();
</script>
@endpush

