@extends('layouts.admin')

@section('title', 'Quản lý menu kế toán')

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
                <h3 class="page-title">Quản lý menu kế toán</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý menu kế toán</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('admin.accountingmenuitem.index') }}">
        <div class="row">
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã menu</label>
                    <input type="text" name="search_id" class="form-control" placeholder="Tìm kiếm theo mã menu ..." value="{{ request('search_id') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Tiêu đề</label>
                    <input type="text" name="search_title" class="form-control" placeholder="Tìm kiếm theo tiêu đề ..." value="{{ request('search_title') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Tên route</label>
                    <input type="text" name="search_routename" class="form-control" placeholder="Tìm kiếm theo tên route ..." value="{{ request('search_routename') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Trạng thái</label>
                    <select name="search_isactive" class="form-control">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('search_isactive') == '1' ? 'selected' : '' }}>Kích hoạt</option>
                        <option value="0" {{ request('search_isactive') == '0' ? 'selected' : '' }}>Vô hiệu</option>
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
                            <h3 class="page-title">Danh sách menu kế toán</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMenuItemModal">
                                <i class="fas fa-plus me-1"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped menuitem-table">
                        <thead class="student-thread">
                            <tr>
                                <th>Mã menu</th>
                                <th>Tiêu đề</th>
                                <th>Thứ tự</th>
                                <th>Tên route</th>
                                <th>Icon</th>
                                <th>Trạng thái</th>
                                <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menuItems as $item)
                                <tr>
                                    <td class="text-end" style="padding-right: 100px !important;">{{ $item->id }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td class="text-center">{{ $item->orderIndex }}</td>
                                    <td>
                                        @if($item->routeName)
                                            <span class="text-dark">{{ $item->routeName }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->icon)
                                            <i class="{{ $item->icon }}"></i>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->isActive)
                                            <span class="badge bg-success">Kích hoạt</span>
                                        @else
                                            <span class="badge bg-secondary">Vô hiệu</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group menuitem-actions" role="group">
                                            <a href="#" class="btn btn-warning btn-sm rounded-pill me-1 text-white view-menuitem-btn"
                                               data-bs-toggle="modal" data-bs-target="#view_menuitem"
                                               title="Xem chi tiết"
                                               data-item-id="{{ $item->id }}"
                                               data-item-title="{{ htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8') }}"
                                               data-item-parent="{{ $item->parent ? htmlspecialchars($item->parent->title, ENT_QUOTES, 'UTF-8') : '-' }}"
                                               data-item-order="{{ $item->orderIndex }}"
                                               data-item-isactive="{{ $item->isActive ? '1' : '0' }}"
                                               data-item-routename="{{ htmlspecialchars($item->routeName ?? '', ENT_QUOTES, 'UTF-8') }}"
                                               data-item-icon="{{ htmlspecialchars($item->icon ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-success btn-sm rounded-pill me-1 text-white edit-menuitem-btn"
                                               data-bs-toggle="modal" data-bs-target="#edit_menuitem"
                                               title="Chỉnh sửa"
                                               data-item-id="{{ $item->id }}"
                                               data-item-title="{{ htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8') }}"
                                               data-item-parentid="{{ $item->parentId ?? '' }}"
                                               data-item-order="{{ $item->orderIndex }}"
                                               data-item-isactive="{{ $item->isActive ? '1' : '0' }}"
                                               data-item-routename="{{ htmlspecialchars($item->routeName ?? '', ENT_QUOTES, 'UTF-8') }}"
                                               data-item-icon="{{ htmlspecialchars($item->icon ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="#" class="btn btn-danger btn-sm rounded-pill text-white delete-menuitem-btn"
                                               data-bs-toggle="modal" data-bs-target="#delete_menuitem"
                                               title="Xóa"
                                               data-item-id="{{ $item->id }}"
                                               data-item-title="{{ htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Chưa có dữ liệu menu.</td>
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
<div class="modal fade menuitem-modal" id="createMenuItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Thêm menu kế toán mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.accountingmenuitem.store') }}" method="post" id="createMenuItemForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
                                @error('title')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Menu cha</label>
                                <select name="parentId" class="form-control">
                                    <option value="">-- Không có menu cha --</option>
                                    @foreach($parentOptions as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parentId') == $parent->id ? 'selected' : '' }}>{{ $parent->title }}</option>
                                    @endforeach
                                </select>
                                @error('parentId')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Thứ tự <span class="text-danger">*</span></label>
                                <input type="number" name="orderIndex" class="form-control" required min="0" value="{{ old('orderIndex', 0) }}">
                                @error('orderIndex')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="isActive" class="form-control" required>
                                    <option value="1" {{ old('isActive', '1') == '1' ? 'selected' : '' }}>Kích hoạt</option>
                                    <option value="0" {{ old('isActive') == '0' ? 'selected' : '' }}>Vô hiệu</option>
                                </select>
                                @error('isActive')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên route</label>
                        <input type="text" name="routeName" class="form-control" placeholder="accounting.dashboard" value="{{ old('routeName') }}">
                        <small class="text-muted">Ví dụ: accounting.dashboard</small>
                        @error('routeName')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon</label>
                        <input type="text" name="icon" class="form-control" placeholder="fas fa-home" value="{{ old('icon') }}">
                        <small class="text-muted">Ví dụ: fas fa-home, feather-grid</small>
                        @error('icon')
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
<div class="modal fade menuitem-modal" id="view_menuitem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết menu kế toán</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Mã menu</label>
                            <input type="text" id="view_item_id" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tiêu đề</label>
                            <input type="text" id="view_item_title" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Menu cha</label>
                            <input type="text" id="view_item_parent" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Thứ tự</label>
                            <input type="text" id="view_item_order" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tên route</label>
                    <input type="text" id="view_item_routename" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Icon</label>
                    <input type="text" id="view_item_icon" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <input type="text" id="view_item_isactive" class="form-control" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade menuitem-modal" id="edit_menuitem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chỉnh sửa menu kế toán</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMenuItemForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="edit_item_title" class="form-control" required>
                                @error('title')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Menu cha</label>
                                <select name="parentId" id="edit_item_parentid" class="form-control">
                                    <option value="">-- Không có menu cha --</option>
                                    @foreach($parentOptions as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                                    @endforeach
                                </select>
                                @error('parentId')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Thứ tự <span class="text-danger">*</span></label>
                                <input type="number" name="orderIndex" id="edit_item_order" class="form-control" required min="0">
                                @error('orderIndex')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select name="isActive" id="edit_item_isactive" class="form-control" required>
                                    <option value="1">Kích hoạt</option>
                                    <option value="0">Vô hiệu</option>
                                </select>
                                @error('isActive')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên route</label>
                        <input type="text" name="routeName" id="edit_item_routename" class="form-control" placeholder="accounting.dashboard">
                        <small class="text-muted">Ví dụ: accounting.dashboard</small>
                        @error('routeName')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon</label>
                        <input type="text" name="icon" id="edit_item_icon" class="form-control" placeholder="fas fa-home">
                        <small class="text-muted">Ví dụ: fas fa-home, feather-grid</small>
                        @error('icon')
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
<div class="modal fade menuitem-modal modal-delete" id="delete_menuitem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xóa menu kế toán</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteMenuItemForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa menu <strong id="delete_item_title">này</strong> không?</p>
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
    .menuitem-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        padding: 0.75rem;
    }
    
    .menuitem-table tbody td {
        font-size: 1rem;
    }

    .menuitem-table thead th {
        cursor: default !important;
    }
    
    .menuitem-table thead th.sorting::before,
    .menuitem-table thead th.sorting::after,
    .menuitem-table thead th.sorting_asc::before,
    .menuitem-table thead th.sorting_asc::after,
    .menuitem-table thead th.sorting_desc::before,
    .menuitem-table thead th.sorting_desc::after,
    .menuitem-table thead th.sorting_asc_disabled::before,
    .menuitem-table thead th.sorting_asc_disabled::after,
    .menuitem-table thead th.sorting_desc_disabled::before,
    .menuitem-table thead th.sorting_desc_disabled::after,
    .menuitem-table thead th::before,
    .menuitem-table thead th::after {
        display: none !important;
        content: none !important;
    }

    .menuitem-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .menuitem-table td {
        vertical-align: middle;
    }

    .menuitem-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .menuitem-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .menuitem-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .menuitem-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .menuitem-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }

    .menuitem-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .menuitem-modal.modal-delete .modal-footer {
        background: #fef2f2;
    }

    .menuitem-modal .form-label {
        font-weight: 500;
        color: #1f2937;
    }

    .menuitem-modal .form-control,
    .menuitem-modal .form-select {
        border-radius: 0.65rem;
        border-color: #dbe3f2;
        padding: 0.6rem 0.85rem;
        box-shadow: none;
    }

    .menuitem-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    .menuitem-actions .btn {
        transition: all .2s ease;
    }

    .menuitem-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(148, 163, 184, 0.25);
    }

    .menuitem-table th.text-end,
    .menuitem-table td.text-end {
        padding-left: 30px !important;
    }

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

        document.addEventListener('click', function(e) {
            if (e.target.closest('.view-menuitem-btn')) {
                const btn = e.target.closest('.view-menuitem-btn');
                document.getElementById('view_item_id').value = btn.dataset.itemId || '';
                document.getElementById('view_item_title').value = btn.dataset.itemTitle || '';
                document.getElementById('view_item_parent').value = btn.dataset.itemParent || '-';
                document.getElementById('view_item_order').value = btn.dataset.itemOrder || '';
                document.getElementById('view_item_routename').value = btn.dataset.itemRoutename || '-';
                document.getElementById('view_item_icon').value = btn.dataset.itemIcon || '-';
                document.getElementById('view_item_isactive').value = btn.dataset.itemIsactive == '1' ? 'Kích hoạt' : 'Vô hiệu';
            }
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-menuitem-btn')) {
                const btn = e.target.closest('.edit-menuitem-btn');
                const itemId = btn.dataset.itemId;
                const form = document.getElementById('editMenuItemForm');
                form.action = '{{ route("admin.accountingmenuitem.update", ":id") }}'.replace(':id', itemId);
                document.getElementById('edit_item_title').value = btn.dataset.itemTitle || '';
                document.getElementById('edit_item_parentid').value = btn.dataset.itemParentid || '';
                document.getElementById('edit_item_order').value = btn.dataset.itemOrder || '';
                document.getElementById('edit_item_isactive').value = btn.dataset.itemIsactive || '1';
                document.getElementById('edit_item_routename').value = btn.dataset.itemRoutename || '';
                document.getElementById('edit_item_icon').value = btn.dataset.itemIcon || '';
            }
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-menuitem-btn')) {
                const btn = e.target.closest('.delete-menuitem-btn');
                const itemId = btn.dataset.itemId;
                const form = document.getElementById('deleteMenuItemForm');
                form.action = '{{ route("admin.accountingmenuitem.destroy", ":id") }}'.replace(':id', itemId);
                document.getElementById('delete_item_title').textContent = btn.dataset.itemTitle || 'này';
            }
        });
    });
</script>
@endpush

