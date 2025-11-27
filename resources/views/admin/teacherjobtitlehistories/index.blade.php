@extends('layouts.admin')

@section('title', 'Lịch sử chức danh giáo viên')

@section('content')
<!-- Success Notification Modal -->
@if (session('success'))
    <div class="modal fade history-modal modal-success" id="successNotificationModal" tabindex="-1" aria-hidden="true">
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
    <div class="modal fade history-modal modal-error" id="errorNotificationModal" tabindex="-1" aria-hidden="true">
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
                <h3 class="page-title">Lịch sử chức danh giáo viên</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Lịch sử chức danh giáo viên</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('admin.teacherjobtitlehistory.index') }}">
        <div class="row">
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã lịch sử</label>
                    <input type="text" name="search_id" class="form-control" placeholder="Tìm kiếm theo mã ..." value="{{ request('search_id') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Họ và tên</label>
                    <input type="text" name="search_teacher" class="form-control" placeholder="Tìm kiếm theo họ và tên ..." value="{{ request('search_teacher') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Chức danh</label>
                    <input type="text" name="search_jobtitle" class="form-control" placeholder="Tìm kiếm theo chức danh ..." value="{{ request('search_jobtitle') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <label class="form-label small text-muted">Ngày có hiệu lực</label>
                    <input type="date" name="search_effectivedate" class="form-control" value="{{ request('search_effectivedate') }}">
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
                            <h3 class="page-title">Danh sách lịch sử chức danh</h3>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped history-table">
                        <thead class="student-thread">
                            <tr>
                                <th>Mã lịch sử</th>
                                <th>Họ và tên</th>
                                <th>Chức danh</th>
                                <th>Ngày có hiệu lực</th>
                                <th>Ngày kết thúc</th>
                                <th>Ghi chú</th>
                                <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($histories as $history)
                                <tr>
                                    <td style="padding-left: 50px !important;">{{ $history->historyid }}</td>
                                    <td>{{ $history->teacher ? $history->teacher->fullname : '-' }}</td>
                                    <td>{{ $history->jobTitle ? $history->jobTitle->jobtitlename : '-' }}</td>
                                    <td>{{ $history->effectivedate ? \Illuminate\Support\Carbon::parse($history->effectivedate)->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $history->expiredate ? \Illuminate\Support\Carbon::parse($history->expiredate)->format('d/m/Y') : '-' }}</td>
                                    <td>{{ Str::limit($history->note ?? '-', 50) }}</td>
                                    <td class="text-end">
                                        <div class="btn-group history-actions" role="group" style="padding-right: 40px;">
                                            <a href="#" class="btn btn-warning btn-sm rounded-pill text-white view-history-btn"
                                               data-bs-toggle="modal" data-bs-target="#view_history"
                                               title="Xem chi tiết"
                                               data-history-id="{{ $history->historyid }}"
                                               data-teacher-name="{{ $history->teacher ? htmlspecialchars($history->teacher->fullname, ENT_QUOTES, 'UTF-8') : '-' }}"
                                               data-jobtitle-name="{{ $history->jobTitle ? htmlspecialchars($history->jobTitle->jobtitlename, ENT_QUOTES, 'UTF-8') : '-' }}"
                                               data-effectivedate="{{ $history->effectivedate ? \Illuminate\Support\Carbon::parse($history->effectivedate)->format('Y-m-d') : '' }}"
                                               data-expiredate="{{ $history->expiredate ? \Illuminate\Support\Carbon::parse($history->expiredate)->format('Y-m-d') : '' }}"
                                               data-note="{{ htmlspecialchars($history->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Không có dữ liệu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade history-modal" id="view_history" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết lịch sử chức danh</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mã lịch sử</label>
                        <input type="text" id="view_historyid" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Giáo viên</label>
                        <input type="text" id="view_teacher" class="form-control" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Chức danh</label>
                        <input type="text" id="view_jobtitle" class="form-control" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ngày có hiệu lực</label>
                        <input type="text" id="view_effectivedate" class="form-control" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ngày kết thúc</label>
                        <input type="text" id="view_expiredate" class="form-control" readonly>
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

<style>
    .history-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        padding: 0.75rem;
    }
    
    .history-table tbody td {
        font-size: 1rem;
    }
    
    .history-table thead th::before,
    .history-table thead th::after {
        display: none !important;
    }
    
    .history-table thead th {
        cursor: default !important;
    }
    
    .history-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }
    
    .history-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .history-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .history-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .history-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }
    
    .history-modal.modal-success .modal-header {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .history-modal.modal-success .modal-footer {
        background: #ecfdf5;
        justify-content: center;
    }

    .history-modal.modal-success .modal-footer .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
    }

    .history-modal.modal-success .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #059669, #047857);
    }
    
    .history-modal.modal-error .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .history-modal.modal-error .modal-footer {
        background: #fef2f2;
        justify-content: center;
    }

    .history-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .history-modal.modal-delete .modal-footer {
        background: #fef2f2;
    }

    .history-modal .form-label {
        font-weight: 500;
        color: #1f2937;
    }

    .history-modal .form-control,
    .history-modal .form-select {
        border-radius: 0.65rem;
        border-color: #dbe3f2;
        padding: 0.6rem 0.85rem;
        box-shadow: none;
    }

    .history-modal .form-control.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .history-modal .form-control.is-valid {
        border-color: #198754;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .history-modal .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .history-modal textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .history-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    .history-actions .btn {
        transition: all .2s ease;
    }

    .history-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(148, 163, 184, 0.25);
    }

    /* Cách trái 30px cho cột Thao tác */
    .history-table th.text-end,
    .history-table td.text-end {
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
    $(document).ready(function() {
        // Hiển thị modal thông báo thành công/lỗi (nếu có trong DOM)
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

        // DataTables configuration
        if ($('.history-table').length) {
            if ($.fn.DataTable.isDataTable('.history-table')) {
                $('.history-table').DataTable().destroy();
            }
            $('.history-table').DataTable({
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
        if (e.target.closest('.view-history-btn')) {
            const btn = e.target.closest('.view-history-btn');
            document.getElementById('view_historyid').value = btn.dataset.historyId || '';
            document.getElementById('view_teacher').value = btn.dataset.teacherName || '-';
            document.getElementById('view_jobtitle').value = btn.dataset.jobtitleName || '-';
            
            const effectivedate = btn.dataset.effectivedate;
            if (effectivedate) {
                const date = new Date(effectivedate);
                document.getElementById('view_effectivedate').value = date.toLocaleDateString('vi-VN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            } else {
                document.getElementById('view_effectivedate').value = '-';
            }
            
            const expiredate = btn.dataset.expiredate;
            if (expiredate) {
                const date = new Date(expiredate);
                document.getElementById('view_expiredate').value = date.toLocaleDateString('vi-VN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            } else {
                document.getElementById('view_expiredate').value = '-';
            }
            
            document.getElementById('view_note').value = btn.dataset.note || '-';
        }
    });

</script>
@endpush

