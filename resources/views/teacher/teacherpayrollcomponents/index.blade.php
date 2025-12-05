@extends('layouts.teacher')

@section('title', 'Thành phần lương của tôi')

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
                <h3 class="page-title">Thành phần lương của tôi</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Thành phần lương của tôi</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('teacher.teacherpayrollcomponent.index') }}">
        <div class="row">
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã cấu hình</label>
                    <input type="text" name="search_id" class="form-control" placeholder="Tìm kiếm theo mã ..." value="{{ request('search_id') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Thành phần lương</label>
                    <select name="search_componentid" class="form-control">
                        <option value="">-- Tất cả --</option>
                        @foreach($allComponents as $component)
                            <option value="{{ $component->componentid }}" {{ (string)request('search_componentid') === (string)$component->componentid ? 'selected' : '' }}>
                                {{ $component->componentname }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Ngày hiệu lực</label>
                    <input type="date" name="search_effectivedate" class="form-control" value="{{ request('search_effectivedate') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Trạng thái</label>
                    <select name="search_status" class="form-control">
                        <option value="">-- Tất cả --</option>
                        <option value="active" {{ request('search_status') === 'active' ? 'selected' : '' }}>Đang hiệu lực</option>
                        <option value="expired" {{ request('search_status') === 'expired' ? 'selected' : '' }}>Đã hết hạn</option>
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
                            <h3 class="page-title">Danh sách thành phần lương của tôi</h3>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped teacherpayrollcomponent-table">
                        <thead class="student-thread">
                            <tr>
                                <th>Mã cấu hình</th>
                                <th>Thành phần lương</th>
                                <th>Ngày hiệu lực</th>
                                <th>Ngày hết hạn</th>
                                <th class="text-center">Hệ số điều chỉnh</th>
                                <th class="text-center">Phần trăm điều chỉnh</th>
                                <th class="description-column">Ghi chú</th>
                                <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($configs as $config)
                                <tr>
                                    <td class="text-end" style="padding-right: 100px !important;">{{ $config->teachercomponentid }}</td>
                                    <td>{{ $config->component->componentname ?? '-' }}</td>
                                    <td>{{ $config->effectivedate ? $config->effectivedate->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($config->expirationdate)
                                            {{ $config->expirationdate->format('d/m/Y') }}
                                        @else
                                            <span class="badge bg-success">Đang hiệu lực</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $config->adjustcustomcoefficient ? number_format($config->adjustcustomcoefficient, 4, '.', '') : '-' }}</td>
                                    <td class="text-center">{{ $config->adjustcustompercentage ? number_format($config->adjustcustompercentage, 4, '.', '') : '-' }}</td>
                                    <td class="description-column" title="{{ $config->note ?? '-' }}">
                                        @if($config->note)
                                            {{ Str::limit($config->note, 50, '...') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group teacherpayrollcomponent-actions" role="group">
                                            <a href="#"
                                               class="btn btn-warning btn-sm rounded-pill me-1 text-white view-teacherpayrollcomponent-btn"
                                               data-bs-toggle="modal" data-bs-target="#view_teacherpayrollcomponent"
                                               title="Xem chi tiết"
                                               data-config-id="{{ $config->teachercomponentid }}"
                                               data-component-id="{{ $config->componentid }}"
                                               data-component-name="{{ htmlspecialchars($config->component->componentname ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-component-method="{{ htmlspecialchars($config->component->calculationmethod ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-effective-date="{{ $config->effectivedate ? $config->effectivedate->format('Y-m-d') : '' }}"
                                               data-expiration-date="{{ $config->expirationdate ? $config->expirationdate->format('Y-m-d') : '' }}"
                                               data-adjust-custom-coefficient="{{ $config->adjustcustomcoefficient ?? '' }}"
                                               data-adjust-custom-percentage="{{ $config->adjustcustompercentage ?? '' }}"
                                               data-note="{{ htmlspecialchars($config->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Chưa có dữ liệu thành phần lương.</td>
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
<div class="modal fade teacherpayrollcomponent-modal" id="view_teacherpayrollcomponent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết cấu hình thành phần lương</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Mã cấu hình</label>
                    <input type="text" id="view_configid" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Thành phần lương</label>
                    <input type="text" id="view_componentname" class="form-control" readonly>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Ngày hiệu lực</label>
                            <input type="text" id="view_effectivedate" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Ngày hết hạn</label>
                            <input type="text" id="view_expirationdate" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                
                <!-- Các trường điều chỉnh -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Hệ số cá nhân</label>
                            <input type="text" id="view_adjustcustomcoefficient" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tỷ lệ phần trăm cá nhân (%)</label>
                            <input type="text" id="view_adjustcustompercentage" class="form-control" readonly>
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
    .teacherpayrollcomponent-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        padding: 0.75rem;
    }

    .teacherpayrollcomponent-table tbody td {
        font-size: 1rem;
    }

    .teacherpayrollcomponent-table thead th {
        cursor: default !important;
    }

    .teacherpayrollcomponent-table thead th::before,
    .teacherpayrollcomponent-table thead th::after {
        display: none !important;
    }

    .teacherpayrollcomponent-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .teacherpayrollcomponent-table td {
        vertical-align: middle;
    }

    .teacherpayrollcomponent-table th.description-column,
    .teacherpayrollcomponent-table td.description-column {
        max-width: 200px;
        width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .teacherpayrollcomponent-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .teacherpayrollcomponent-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .teacherpayrollcomponent-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .teacherpayrollcomponent-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .teacherpayrollcomponent-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }

    .teacherpayrollcomponent-modal .form-label {
        font-weight: 500;
        color: #1f2937;
    }

    .teacherpayrollcomponent-modal .form-control,
    .teacherpayrollcomponent-modal .form-select {
        border-radius: 0.65rem;
        border-color: #dbe3f2;
        padding: 0.6rem 0.85rem;
        box-shadow: none;
    }

    .teacherpayrollcomponent-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }

    .teacherpayrollcomponent-actions .btn {
        transition: all .2s ease;
    }

    .teacherpayrollcomponent-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(148, 163, 184, 0.25);
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
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
        document.addEventListener('click', async function (e) {
            if (e.target.closest('.view-teacherpayrollcomponent-btn')) {
                const btn = e.target.closest('.view-teacherpayrollcomponent-btn');
                
                // Set thông tin cơ bản
                document.getElementById('view_configid').value = btn.dataset.configId || '';
                document.getElementById('view_componentname').value = btn.dataset.componentName || '';
                
                // Format ngày
                const effectiveDate = btn.dataset.effectiveDate || '';
                document.getElementById('view_effectivedate').value = effectiveDate ? new Date(effectiveDate + 'T00:00:00').toLocaleDateString('vi-VN') : '-';
                
                const expirationDate = btn.dataset.expirationDate || '';
                document.getElementById('view_expirationdate').value = expirationDate ? new Date(expirationDate + 'T00:00:00').toLocaleDateString('vi-VN') : 'Đang hiệu lực';

                // Hiển thị giá trị điều chỉnh (giá trị tuyệt đối, không có dấu)
                const adjustCoeff = parseFloat(btn.dataset.adjustCustomCoefficient || 0);
                const adjustPercent = parseFloat(btn.dataset.adjustCustomPercentage || 0);
                
                const viewAdjustCoeffInput = document.getElementById('view_adjustcustomcoefficient');
                if (viewAdjustCoeffInput) {
                    viewAdjustCoeffInput.value = adjustCoeff !== 0 ? Math.abs(adjustCoeff).toFixed(4) : '-';
                }
                
                const viewAdjustPercentInput = document.getElementById('view_adjustcustompercentage');
                if (viewAdjustPercentInput) {
                    viewAdjustPercentInput.value = adjustPercent !== 0 ? Math.abs(adjustPercent).toFixed(4) : '-';
                }

                document.getElementById('view_note').value = btn.dataset.note || '';
            }
        });
    });
</script>
@endpush

