@extends('layouts.teacher')

@section('title', 'Quyết định nâng lương của tôi')

@section('content')
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
                <h3 class="page-title">Quyết định nâng lương của tôi</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quyết định nâng lương của tôi</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('teacher.salaryincreasedecision.index') }}">
        <div class="row">
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã quyết định</label>
                    <input type="text" name="search_id" class="form-control" placeholder="Tìm kiếm theo mã ..." value="{{ request('search_id') }}">
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
                            <h3 class="page-title">Danh sách quyết định nâng lương của tôi</h3>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped salaryincreasedecision-table">
                        <thead class="student-thread">
                            <tr>
                                <th>Mã quyết định</th>
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
                                    <td class="text-end" style="padding-right: 100px !important;">{{ $decision->decisionid }}</td>
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
                                               data-decision-date="{{ $decision->decisiondate ? $decision->decisiondate->format('Y-m-d') : '' }}"
                                               data-apply-date="{{ $decision->applydate ? $decision->applydate->format('Y-m-d') : '' }}"
                                               data-old-coefficient="{{ $decision->oldcoefficient ?? '' }}"
                                               data-new-coefficient="{{ $decision->newcoefficient ?? '' }}"
                                               data-note="{{ htmlspecialchars($decision->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Chưa có dữ liệu quyết định nâng lương.</td>
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

    .salaryincreasedecision-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
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

    .notification-modal.modal-error .modal-footer {
        background: #fef2f2;
    }

    .notification-modal .btn {
        border-radius: 999px;
        padding-inline: 1.5rem;
        font-weight: 500;
        min-width: 120px;
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
    });
</script>
@endpush

