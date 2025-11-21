@extends('layouts.admin')

@section('title', 'Quản lý thành phần lương')

@section('content')
<!-- Success Notification Modal -->
@if (session('success'))
    <div class="modal fade payrollcomponent-modal modal-success" id="successNotificationModal" tabindex="-1" aria-hidden="true">
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
    <div class="modal fade payrollcomponent-modal modal-error" id="errorNotificationModal" tabindex="-1" aria-hidden="true">
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
                <h3 class="page-title">Quản lý thành phần lương</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý thành phần lương</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('admin.payrollcomponent.index') }}">
         <div class="row">
             <div class="col-lg-3 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã thành phần</label>
                    <input type="text" name="search_id" class="form-control" placeholder="Tìm kiếm theo mã thành phần ..." value="{{ request('search_id') }}">
                </div>
            </div>
             <div class="col-lg-3 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Tên thành phần</label>
                    <input type="text" name="search_name" class="form-control" placeholder="Tìm kiếm theo tên thành phần ..." value="{{ request('search_name') }}">
                </div>
            </div>
             <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Nhóm thành phần</label>
                    <select name="search_group" class="form-control">
                        <option value="">-- Tất cả --</option>
                        @foreach($groups as $key => $label)
                            <option value="{{ $key }}" {{ request('search_group') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
             </div>
             <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Phương pháp tính</label>
                    <select name="search_method" class="form-control">
                        <option value="">-- Tất cả --</option>
                        @foreach($methods as $key => $label)
                            <option value="{{ $key }}" {{ request('search_method') === $key ? 'selected' : '' }}>
                                {{ $label }}
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
                            <h3 class="page-title">Danh sách thành phần lương</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPayrollComponentModal">
                                <i class="fas fa-plus me-1"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>

                    <div class="table-responsive">
                        <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped payrollcomponent-table">
                            <thead class="student-thread">
                                <tr>
                                    <th class="text-end" style="padding-right: 50px !important;">Mã thành phần</th>
                                    <th>Tên thành phần</th>
                                    <th>Nhóm</th>
                                    <th>Phương pháp tính</th>
                                    <th class="description-column">Mô tả</th>
                                    <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($components as $component)
                                    <tr>
                                        <td style="padding-left: 100px !important;">{{ $component->componentid }}</td>
                                        <td>{{ $component->componentname }}</td>
                                        <td>{{ $component->componentgroup }}</td>
                                        <td>{{ $component->calculationmethod }}</td>
                                        <td class="description-column" title="{{ $component->componentdescription ?? '-' }}">
                                            @if($component->componentdescription)
                                                {{ Str::limit($component->componentdescription, 80, '...') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group payrollcomponent-actions" role="group">
                                                <a href="#"
                                                   class="btn btn-warning btn-sm rounded-pill me-1 text-white view-payrollcomponent-btn"
                                                   data-bs-toggle="modal" data-bs-target="#view_payrollcomponent"
                                                   title="Xem chi tiết"
                                                   data-component-id="{{ $component->componentid }}"
                                                   data-component-name="{{ htmlspecialchars($component->componentname, ENT_QUOTES, 'UTF-8') }}"
                                                   data-component-group="{{ $component->componentgroup }}"
                                                   data-component-method="{{ $component->calculationmethod }}"
                                                   data-component-description="{{ htmlspecialchars($component->componentdescription ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="#"
                                                   class="btn btn-success btn-sm rounded-pill me-1 text-white edit-payrollcomponent-btn"
                                                   data-bs-toggle="modal" data-bs-target="#edit_payrollcomponent"
                                                   title="Chỉnh sửa"
                                                   data-component-id="{{ $component->componentid }}"
                                                   data-component-name="{{ htmlspecialchars($component->componentname, ENT_QUOTES, 'UTF-8') }}"
                                                   data-component-group="{{ $component->componentgroup }}"
                                                   data-component-method="{{ $component->calculationmethod }}"
                                                   data-component-description="{{ htmlspecialchars($component->componentdescription ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <a href="#"
                                                   class="btn btn-danger btn-sm rounded-pill text-white delete-payrollcomponent-btn"
                                                   data-bs-toggle="modal" data-bs-target="#delete_payrollcomponent"
                                                   title="Xóa"
                                                   data-component-id="{{ $component->componentid }}"
                                                   data-component-name="{{ htmlspecialchars($component->componentname, ENT_QUOTES, 'UTF-8') }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Chưa có dữ liệu thành phần lương.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade payrollcomponent-modal" id="createPayrollComponentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Thêm thành phần lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.payrollcomponent.store') }}" method="post" id="createPayrollComponentForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên thành phần <span class="text-danger">*</span></label>
                        <input type="text"
                               name="componentname"
                               id="create_componentname"
                               class="form-control"
                               required
                               value="{{ old('componentname') }}"
                               oninput="validatePayrollComponentName(this, 'create')">
                        <div id="create_componentname_error" class="invalid-feedback"></div>
                        @error('componentname')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nhóm thành phần <span class="text-danger">*</span></label>
                        <select name="componentgroup" class="form-control" required>
                            <option value="">-- Chọn nhóm --</option>
                            @foreach($groups as $key => $label)
                                <option value="{{ $key }}" {{ old('componentgroup') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('componentgroup')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phương pháp tính <span class="text-danger">*</span></label>
                        <select name="calculationmethod" class="form-control" required>
                            <option value="">-- Chọn phương pháp --</option>
                            @foreach($methods as $key => $label)
                                <option value="{{ $key }}" {{ old('calculationmethod') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('calculationmethod')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="componentdescription" rows="3" class="form-control"
                                  placeholder="Nhập mô tả (không bắt buộc)">{{ old('componentdescription') }}</textarea>
                        @error('componentdescription')
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
<div class="modal fade payrollcomponent-modal" id="view_payrollcomponent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết thành phần lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Mã thành phần</label>
                    <input type="text" id="view_componentid" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tên thành phần</label>
                    <input type="text" id="view_componentname" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nhóm thành phần</label>
                    <input type="text" id="view_componentgroup" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phương pháp tính</label>
                    <input type="text" id="view_calculationmethod" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea id="view_componentdescription" rows="3" class="form-control" readonly></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade payrollcomponent-modal" id="edit_payrollcomponent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chỉnh sửa thành phần lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPayrollComponentForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên thành phần <span class="text-danger">*</span></label>
                        <input type="text"
                               name="componentname"
                               id="edit_componentname"
                               class="form-control"
                               required
                               oninput="validatePayrollComponentName(this, 'edit')">
                        <div id="edit_componentname_error" class="invalid-feedback"></div>
                        @error('componentname')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nhóm thành phần <span class="text-danger">*</span></label>
                        <select name="componentgroup" id="edit_componentgroup" class="form-control" required>
                            <option value="">-- Chọn nhóm --</option>
                            @foreach($groups as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('componentgroup')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phương pháp tính <span class="text-danger">*</span></label>
                        <select name="calculationmethod" id="edit_calculationmethod" class="form-control" required>
                            <option value="">-- Chọn phương pháp --</option>
                            @foreach($methods as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('calculationmethod')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="componentdescription" id="edit_componentdescription" rows="3" class="form-control"></textarea>
                        @error('componentdescription')
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
<div class="modal fade payrollcomponent-modal modal-delete" id="delete_payrollcomponent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xóa thành phần lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deletePayrollComponentForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa thành phần lương <strong id="delete_component_name">này</strong> không?</p>
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
    .payrollcomponent-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        padding: 0.75rem;
    }

    .payrollcomponent-table tbody td {
        font-size: 1rem;
    }

    .payrollcomponent-table thead th {
        cursor: default !important;
    }

    .payrollcomponent-table thead th.sorting::before,
    .payrollcomponent-table thead th.sorting::after,
    .payrollcomponent-table thead th.sorting_asc::before,
    .payrollcomponent-table thead th.sorting_asc::after,
    .payrollcomponent-table thead th.sorting_desc::before,
    .payrollcomponent-table thead th.sorting_desc::after,
    .payrollcomponent-table thead th.sorting_asc_disabled::before,
    .payrollcomponent-table thead th.sorting_asc_disabled::after,
    .payrollcomponent-table thead th.sorting_desc_disabled::before,
    .payrollcomponent-table thead th.sorting_desc_disabled::after,
    .payrollcomponent-table thead th::before,
    .payrollcomponent-table thead th::after {
        display: none !important;
        content: none !important;
    }

    .payrollcomponent-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .payrollcomponent-table td {
        vertical-align: middle;
    }

    /* Cột mô tả: rộng 1 nửa và truncate text */
    .payrollcomponent-table th.description-column,
    .payrollcomponent-table td.description-column {
        max-width: 300px;
        width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .payrollcomponent-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .payrollcomponent-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .payrollcomponent-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .payrollcomponent-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .payrollcomponent-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }

    .payrollcomponent-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    .payrollcomponent-modal.modal-success .modal-header {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .payrollcomponent-modal.modal-success .modal-footer {
        background: #ecfdf5;
    }

    .payrollcomponent-modal.modal-error .modal-header {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
    }

    .payrollcomponent-modal.modal-error .modal-footer {
        background: #fef2f2;
    }

    .payrollcomponent-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }

    .payrollcomponent-modal.modal-delete .modal-footer {
        background: #fef2f2;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
<script>
    function validatePayrollComponentName(input, formType) {
        const value = input.value;
        const errorId = formType + '_componentname_error';
        const errorEl = document.getElementById(errorId);

        input.classList.remove('is-invalid', 'is-valid');
        if (errorEl) errorEl.textContent = '';

        const trimmed = value.trim();
        if (!trimmed) {
            input.classList.add('is-invalid');
            if (errorEl) errorEl.textContent = 'Tên thành phần lương là bắt buộc';
            return;
        }

        if (trimmed.length < 3) {
            input.classList.add('is-invalid');
            if (errorEl) errorEl.textContent = 'Tên thành phần lương phải có ít nhất 3 ký tự';
            return;
        }

        if (trimmed.length > 200) {
            input.classList.add('is-invalid');
            if (errorEl) errorEl.textContent = 'Tên thành phần lương không được vượt quá 200 ký tự';
            return;
        }

        if (/\s{2,}/.test(value)) {
            input.classList.add('is-invalid');
            if (errorEl) errorEl.textContent = 'Tên thành phần lương không được có nhiều khoảng trắng liên tiếp';
            return;
        }

        const regex = /^[\p{L}\p{N}\s,.\-%()]+$/u;
        if (!regex.test(value)) {
            input.classList.add('is-invalid');
            if (errorEl) errorEl.textContent = 'Tên thành phần lương chỉ được chứa chữ cái, số, khoảng trắng và các ký tự: , . - ( ) %';
            return;
        }

        input.classList.add('is-valid');
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

        const successModal = document.getElementById('successNotificationModal');
        if (successModal) {
            const modal = new bootstrap.Modal(successModal);
            modal.show();
        }

        const errorModal = document.getElementById('errorNotificationModal');
        if (errorModal) {
            const modal = new bootstrap.Modal(errorModal);
            modal.show();
        }

        // View button handler
        document.addEventListener('click', function (e) {
            if (e.target.closest('.view-payrollcomponent-btn')) {
                const btn = e.target.closest('.view-payrollcomponent-btn');
                document.getElementById('view_componentid').value = btn.dataset.componentId || '';
                document.getElementById('view_componentname').value = btn.dataset.componentName || '';
                document.getElementById('view_componentgroup').value = btn.dataset.componentGroup || '';
                document.getElementById('view_calculationmethod').value = btn.dataset.componentMethod || '';
                document.getElementById('view_componentdescription').value = btn.dataset.componentDescription || '';
            }
        });

        // Edit button handler
        document.addEventListener('click', function (e) {
            if (e.target.closest('.edit-payrollcomponent-btn')) {
                const btn = e.target.closest('.edit-payrollcomponent-btn');
                const id = btn.dataset.componentId;

                const form = document.getElementById('editPayrollComponentForm');
                form.action = '{{ route("admin.payrollcomponent.update", ":id") }}'.replace(':id', id);

                const nameInput = document.getElementById('edit_componentname');
                nameInput.value = btn.dataset.componentName || '';
                nameInput.classList.remove('is-invalid', 'is-valid');
                const nameError = document.getElementById('edit_componentname_error');
                if (nameError) nameError.textContent = '';

                const groupSelect = document.getElementById('edit_componentgroup');
                if (groupSelect) {
                    groupSelect.value = btn.dataset.componentGroup || '';
                }

                const methodSelect = document.getElementById('edit_calculationmethod');
                if (methodSelect) {
                    methodSelect.value = btn.dataset.componentMethod || '';
                }

                const descInput = document.getElementById('edit_componentdescription');
                if (descInput) {
                    descInput.value = btn.dataset.componentDescription || '';
                }
            }
        });

        // Delete button handler
        document.addEventListener('click', function (e) {
            if (e.target.closest('.delete-payrollcomponent-btn')) {
                const btn = e.target.closest('.delete-payrollcomponent-btn');
                const id = btn.dataset.componentId;

                const form = document.getElementById('deletePayrollComponentForm');
                form.action = '{{ route("admin.payrollcomponent.destroy", ":id") }}'.replace(':id', id);

                document.getElementById('delete_component_name').textContent = btn.dataset.componentName || 'này';
            }
        });
    });
</script>
@endpush


