@extends('layouts.accounting')

@section('title', 'Quản lý cấu hình thành phần lương theo giáo viên')

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
                <h3 class="page-title">Quản lý cấu hình thành phần lương theo giáo viên</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('accounting.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Quản lý cấu hình thành phần lương theo giáo viên</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="student-group-form mb-4">
    <form method="GET" action="{{ route('accounting.teacherpayrollcomponent.index') }}">
        <div class="row">
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Mã cấu hình</label>
                    <input type="text" name="search_id" class="form-control" placeholder="Tìm kiếm theo mã ..." value="{{ request('search_id') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label class="form-label small text-muted">Giáo viên</label>
                    <select name="search_teacherid" class="form-control">
                        <option value="">-- Tất cả --</option>
                        @foreach($allTeachers as $teacher)
                            <option value="{{ $teacher->teacherid }}" {{ (string)request('search_teacherid') === (string)$teacher->teacherid ? 'selected' : '' }}>
                                {{ $teacher->fullname }}
                            </option>
                        @endforeach
                    </select>
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
                            <h3 class="page-title">Danh sách cấu hình thành phần lương theo giáo viên</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTeacherPayrollComponentModal" onclick="resetCreateForm()">
                                <i class="fas fa-plus me-1"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped teacherpayrollcomponent-table">
                        <thead class="student-thread">
                            <tr>
                                <th class="text-center" style="padding-right: 50px !important;">Mã cấu hình</th>
                                <th>Giáo viên</th>
                                <th>Thành phần lương</th>
                                <th>Ngày hiệu lực</th>
                                <th>Ngày hết hạn</th>
                                <th class="description-column">Ghi chú</th>
                                <th class="text-end" style="padding-right: 50px !important;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($configs as $config)
                                <tr>
                                    <td style="padding-left: 80px !important;">{{ $config->teachercomponentid }}</td>
                                    <td>{{ $config->teacher->fullname ?? '-' }}</td>
                                    <td>{{ $config->component->componentname ?? '-' }}</td>
                                    <td>{{ $config->effectivedate ? $config->effectivedate->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($config->expirationdate)
                                            {{ $config->expirationdate->format('d/m/Y') }}
                                        @else
                                            <span class="badge bg-success">Đang hiệu lực</span>
                                        @endif
                                    </td>
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
                                               data-teacher-id="{{ $config->teacherid }}"
                                               data-component-id="{{ $config->componentid }}"
                                               data-teacher-name="{{ htmlspecialchars($config->teacher->fullname ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-component-name="{{ htmlspecialchars($config->component->componentname ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-component-method="{{ htmlspecialchars($config->component->calculationmethod ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-effective-date="{{ $config->effectivedate ? $config->effectivedate->format('Y-m-d') : '' }}"
                                               data-expiration-date="{{ $config->expirationdate ? $config->expirationdate->format('Y-m-d') : '' }}"
                                               data-adjust-custom-coefficient="{{ $config->adjustcustomcoefficient ?? '' }}"
                                               data-adjust-custom-percentage="{{ $config->adjustcustompercentage ?? '' }}"
                                               data-note="{{ htmlspecialchars($config->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#"
                                               class="btn btn-success btn-sm rounded-pill me-1 text-white edit-teacherpayrollcomponent-btn"
                                               data-bs-toggle="modal" data-bs-target="#edit_teacherpayrollcomponent"
                                               title="Chỉnh sửa"
                                               data-config-id="{{ $config->teachercomponentid }}"
                                               data-teacher-id="{{ $config->teacherid }}"
                                               data-component-id="{{ $config->componentid }}"
                                               data-teacher-name="{{ htmlspecialchars($config->teacher->fullname ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-component-name="{{ htmlspecialchars($config->component->componentname ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-component-method="{{ htmlspecialchars($config->component->calculationmethod ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-effective-date="{{ $config->effectivedate ? $config->effectivedate->format('Y-m-d') : '' }}"
                                               data-expiration-date="{{ $config->expirationdate ? $config->expirationdate->format('Y-m-d') : '' }}"
                                               data-custom-coefficient="{{ $config->customcoefficient ?? '' }}"
                                               data-adjust-custom-coefficient="{{ $config->adjustcustomcoefficient ?? '' }}"
                                               data-custom-percentage="{{ $config->custompercentage ?? '' }}"
                                               data-adjust-custom-percentage="{{ $config->adjustcustompercentage ?? '' }}"
                                               data-note="{{ htmlspecialchars($config->note ?? '', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="#"
                                               class="btn btn-danger btn-sm rounded-pill text-white delete-teacherpayrollcomponent-btn"
                                               data-bs-toggle="modal" data-bs-target="#delete_teacherpayrollcomponent"
                                               title="Xóa"
                                               data-config-id="{{ $config->teachercomponentid }}"
                                               data-teacher-name="{{ htmlspecialchars($config->teacher->fullname ?? '-', ENT_QUOTES, 'UTF-8') }}"
                                               data-component-name="{{ htmlspecialchars($config->component->componentname ?? '-', ENT_QUOTES, 'UTF-8') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Chưa có dữ liệu cấu hình thành phần lương theo giáo viên.</td>
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
<div class="modal fade teacherpayrollcomponent-modal" id="createTeacherPayrollComponentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Thêm cấu hình thành phần lương theo giáo viên</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('accounting.teacherpayrollcomponent.store') }}" method="post" id="createTeacherPayrollComponentForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Giáo viên <span class="text-danger">*</span></label>
                                <select name="teacherid" id="create_teacherid" class="form-control" required onchange="filterComponentsByTeacher(this.value)">
                                    <option value="">-- Chọn giáo viên --</option>
                                    @foreach($allTeachers as $teacher)
                                        <option value="{{ $teacher->teacherid }}" {{ old('teacherid') == $teacher->teacherid ? 'selected' : '' }}>
                                            {{ $teacher->fullname }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('teacherid')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Thành phần lương <span class="text-danger">*</span></label>
                                <div class="border rounded p-3" style="background-color: #fff;">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="select_all_components" onchange="toggleAllComponents(this)">
                                        <label class="form-check-label fw-bold" for="select_all_components">
                                            Chọn tất cả
                                        </label>
                                    </div>
                                    <hr class="my-2">
                                    <div id="components_checkbox_list">
                                        @foreach($allComponents as $component)
                                            <div class="form-check component-item">
                                                <input class="form-check-input component-checkbox" 
                                                       type="checkbox" 
                                                       name="componentids[]" 
                                                       id="component_{{ $component->componentid }}" 
                                                       value="{{ $component->componentid }}"
                                                       data-method="{{ $component->calculationmethod }}"
                                                       onchange="updateValueFieldsForMultiple('create')"
                                                       {{ old('componentids') && in_array($component->componentid, old('componentids')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="component_{{ $component->componentid }}">
                                                    {{ $component->componentname }} <span class="text-muted">({{ $component->calculationmethod }})</span>
                                                </label>
                                            </div>
                                        @endforeach
                                        <div style="height: 10px;"></div>
                                    </div>
                                </div>
                                <small class="text-muted">Chọn một hoặc nhiều thành phần lương</small>
                                @error('componentids')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                @error('componentids.*')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày hiệu lực <span class="text-danger">*</span></label>
                                <input type="date" name="effectivedate" id="create_effectivedate" class="form-control" required value="{{ old('effectivedate') }}">
                                @error('effectivedate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày hết hạn</label>
                                <input type="date" name="expirationdate" id="create_expirationdate" class="form-control" value="{{ old('expirationdate') }}">
                                @error('expirationdate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden inputs để lưu giá trị điều chỉnh (tự động lấy từ cấu hình đơn vị) -->
                    <input type="hidden" name="adjustcustomcoefficient" id="create_adjustcustomcoefficient" value="0">
                    <input type="hidden" name="adjustcustompercentage" id="create_adjustcustompercentage" value="0">
                    <input type="hidden" name="customcoefficient" id="create_customcoefficient" value="">
                    <input type="hidden" name="custompercentage" id="create_custompercentage" value="">
                    <input type="hidden" name="customfixedamount" id="create_customfixedamount" value="">
                    
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="note" id="create_note" rows="3" class="form-control" placeholder="Nhập ghi chú (không bắt buộc)">{{ old('note') }}</textarea>
                        @error('note')
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
<div class="modal fade teacherpayrollcomponent-modal" id="view_teacherpayrollcomponent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chi tiết cấu hình thành phần lương theo giáo viên</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Mã cấu hình</label>
                    <input type="text" id="view_configid" class="form-control" readonly>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Giáo viên</label>
                            <input type="text" id="view_teachername" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Thành phần lương</label>
                            <input type="text" id="view_componentname" class="form-control" readonly>
                        </div>
                    </div>
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

<!-- Edit Modal -->
<div class="modal fade teacherpayrollcomponent-modal" id="edit_teacherpayrollcomponent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Chỉnh sửa cấu hình thành phần lương theo giáo viên</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTeacherPayrollComponentForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Giáo viên <span class="text-danger">*</span></label>
                                <select name="teacherid" id="edit_teacherid" class="form-control" required>
                                    <option value="">-- Chọn giáo viên --</option>
                                    @foreach($allTeachers as $teacher)
                                        <option value="{{ $teacher->teacherid }}">
                                            {{ $teacher->fullname }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('teacherid')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Thành phần lương <span class="text-danger">*</span></label>
                                <select name="componentid" id="edit_componentid" class="form-control" required onchange="updateValueFields('edit', this.value)">
                                    <option value="">-- Chọn thành phần lương --</option>
                                    @foreach($allComponents as $component)
                                        <option value="{{ $component->componentid }}" data-method="{{ $component->calculationmethod }}">
                                            {{ $component->componentname }} ({{ $component->calculationmethod }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('componentid')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày hiệu lực <span class="text-danger">*</span></label>
                                <input type="date" name="effectivedate" id="edit_effectivedate" class="form-control" required>
                                @error('effectivedate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày hết hạn</label>
                                <input type="date" name="expirationdate" id="edit_expirationdate" class="form-control">
                                @error('expirationdate')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Các trường điều chỉnh -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hệ số cá nhân</label>
                                <input type="number" name="adjustcustomcoefficient" id="edit_adjustcustomcoefficient" class="form-control" step="0.0001" min="-9999.9999" max="9999.9999" value="0">
                                <small class="text-muted">Nhập số dương để tăng, số âm để giảm (ví dụ: +0.2000 hoặc -0.1000). Nếu để trống hoặc 0, sẽ tự động lấy từ cấu hình đơn vị.</small>
                                @error('adjustcustomcoefficient')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tỷ lệ phần trăm cá nhân (%)</label>
                                <input type="number" name="adjustcustompercentage" id="edit_adjustcustompercentage" class="form-control" step="0.0001" min="-9999.9999" max="9999.9999" value="0">
                                <small class="text-muted">Nhập số dương để tăng, số âm để giảm (ví dụ: +5.0000 hoặc -2.0000). Nếu để trống hoặc 0, sẽ tự động lấy từ cấu hình đơn vị.</small>
                                @error('adjustcustompercentage')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden inputs khác -->
                    <input type="hidden" name="customcoefficient" id="edit_customcoefficient" value="">
                    <input type="hidden" name="custompercentage" id="edit_custompercentage" value="">
                    <input type="hidden" name="customfixedamount" id="edit_customfixedamount" value="">
                    
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="note" id="edit_note" rows="3" class="form-control"></textarea>
                        @error('note')
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
<div class="modal fade teacherpayrollcomponent-modal modal-delete" id="delete_teacherpayrollcomponent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xóa cấu hình thành phần lương theo giáo viên</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteTeacherPayrollComponentForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa cấu hình thành phần lương theo giáo viên <strong id="delete_config_name">này</strong> không?</p>
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

    .teacherpayrollcomponent-modal.modal-success .modal-header {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .teacherpayrollcomponent-modal.modal-success .modal-footer {
        background: #ecfdf5;
        justify-content: center;
    }

    .teacherpayrollcomponent-modal.modal-success .modal-footer .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
    }

    .teacherpayrollcomponent-modal.modal-success .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #059669, #047857);
    }

    .teacherpayrollcomponent-modal.modal-error .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .teacherpayrollcomponent-modal.modal-error .modal-footer {
        background: #fef2f2;
        justify-content: center;
    }

    .teacherpayrollcomponent-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .teacherpayrollcomponent-modal.modal-delete .modal-footer {
        background: #fef2f2;
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

    /* Style cho checkbox list thành phần lương */
    #components_checkbox_list {
        max-height: 250px;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 0.5rem 0.5rem 1rem 0.5rem;
        margin-bottom: 0;
        box-sizing: border-box;
    }

    #components_checkbox_list::-webkit-scrollbar {
        width: 8px;
    }

    #components_checkbox_list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    #components_checkbox_list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    #components_checkbox_list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    #components_checkbox_list .component-item {
        padding: 0.5rem 0.75rem;
        margin-bottom: 0.25rem;
        border-radius: 0.375rem;
        transition: background-color 0.2s;
        min-height: 2.5rem;
        display: flex;
        align-items: flex-start;
        box-sizing: border-box;
    }

    #components_checkbox_list .component-item:hover {
        background-color: #f8fafc;
    }

    #components_checkbox_list .component-item .form-check-label {
        cursor: pointer;
        user-select: none;
        word-wrap: break-word;
        white-space: normal;
        line-height: 1.5;
        margin-left: 0.5rem;
        flex: 1;
        padding-top: 0.125rem;
    }

    #components_checkbox_list .component-item .form-check-input {
        margin-top: 0;
        margin-left: 0;
        flex-shrink: 0;
        width: 1.25em;
        height: 1.25em;
    }

    #select_all_components {
        cursor: pointer;
    }

    .border.rounded {
        border: 1px solid #dee2e6 !important;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
<script>
    // Hàm kiểm tra ngày hết hạn phải sau ngày hiệu lực
    function validateExpirationDate(formType) {
        const effectiveDateInput = document.getElementById(formType + '_effectivedate');
        const expirationDateInput = document.getElementById(formType + '_expirationdate');
        
        if (!effectiveDateInput || !expirationDateInput) return;
        
        const effectiveDate = effectiveDateInput.value;
        const expirationDate = expirationDateInput.value;
        
        expirationDateInput.classList.remove('is-invalid', 'is-valid');
        
        if (expirationDate && effectiveDate) {
            const effective = new Date(effectiveDate);
            const expiration = new Date(expirationDate);
            
            if (expiration <= effective) {
                expirationDateInput.classList.add('is-invalid');
                const errorDiv = expirationDateInput.parentElement.querySelector('.expiration-date-error');
                if (errorDiv) {
                    errorDiv.textContent = 'Ngày hết hạn phải sau ngày hiệu lực';
                } else {
                    const errorEl = document.createElement('div');
                    errorEl.className = 'text-danger small expiration-date-error';
                    errorEl.textContent = 'Ngày hết hạn phải sau ngày hiệu lực';
                    expirationDateInput.parentElement.appendChild(errorEl);
                }
            } else {
                expirationDateInput.classList.add('is-valid');
                const errorDiv = expirationDateInput.parentElement.querySelector('.expiration-date-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        }
    }

    // Hàm reset form create
    function resetCreateForm() {
        const createTeacherSelect = document.getElementById('create_teacherid');
        const componentItems = document.querySelectorAll('.component-item');
        const selectAllCheckbox = document.getElementById('select_all_components');
        
        // Reset giáo viên
        if (createTeacherSelect) {
            createTeacherSelect.value = '';
        }
        
        // Reset và hiển thị tất cả component
        if (componentItems) {
            componentItems.forEach(item => {
                item.style.display = '';
                const checkbox = item.querySelector('.component-checkbox');
                if (checkbox) {
                    checkbox.checked = false;
                }
            });
        }
        
        // Reset "Chọn tất cả"
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
        }
    }

    // Hàm lọc component theo giáo viên - ẩn các component đã có cấu hình (cho form create)
    async function filterComponentsByTeacher(teacherId) {
        const componentItems = document.querySelectorAll('.component-item');
        if (!componentItems || componentItems.length === 0) return;

        // Reset về mặc định - hiển thị tất cả
        componentItems.forEach(item => {
            item.style.display = '';
        });

        // Nếu không chọn giáo viên, hiển thị tất cả
        if (!teacherId || teacherId === '') {
            // Reset "Chọn tất cả" checkbox
            const selectAllCheckbox = document.getElementById('select_all_components');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
            }
            return;
        }

        // Gọi API để lấy danh sách component đã được cấu hình
        try {
            const response = await fetch(`{{ url('admin/teacherpayrollcomponent/get-used-components') }}/${teacherId}`);
            const data = await response.json();

            if (data.success && data.used_component_ids) {
                const usedComponentIds = data.used_component_ids;
                
                // Ẩn các component đã được cấu hình
                componentItems.forEach(item => {
                    const checkbox = item.querySelector('.component-checkbox');
                    if (checkbox) {
                        const componentId = parseInt(checkbox.value);
                        if (usedComponentIds.includes(componentId)) {
                            item.style.display = 'none';
                            // Bỏ chọn checkbox nếu đang được chọn
                            checkbox.checked = false;
                        }
                    }
                });

                // Cập nhật trạng thái "Chọn tất cả"
                updateSelectAllCheckbox();
            }
        } catch (error) {
            console.error('Error filtering components:', error);
        }
    }

    // Hàm lọc component theo giáo viên - ẩn các component đã có cấu hình (cho form edit)
    async function filterEditComponentsByTeacher(teacherId, excludeComponentId = null) {
        const editComponentSelect = document.getElementById('edit_componentid');
        if (!editComponentSelect) return;

        // Nếu không chọn giáo viên, hiển thị tất cả
        if (!teacherId || teacherId === '') {
            // Hiển thị tất cả các option
            Array.from(editComponentSelect.options).forEach(option => {
                if (option.value !== '') {
                    option.style.display = '';
                }
            });
            return;
        }

        // Gọi API để lấy danh sách component đã được cấu hình
        try {
            const response = await fetch(`{{ url('admin/teacherpayrollcomponent/get-used-components') }}/${teacherId}`);
            const data = await response.json();

            if (data.success && data.used_component_ids) {
                const usedComponentIds = data.used_component_ids;
                
                // Ẩn các component đã được cấu hình (trừ component hiện tại đang chỉnh sửa)
                Array.from(editComponentSelect.options).forEach(option => {
                    if (option.value === '') {
                        // Giữ option "-- Chọn thành phần lương --" luôn hiển thị
                        option.style.display = '';
                    } else {
                        const componentId = parseInt(option.value);
                        // Ẩn nếu đã có cấu hình và không phải component hiện tại
                        if (usedComponentIds.includes(componentId) && componentId !== excludeComponentId) {
                            option.style.display = 'none';
                        } else {
                            option.style.display = '';
                        }
                    }
                });
            }
        } catch (error) {
            console.error('Error filtering edit components:', error);
        }
    }

    // Hàm cập nhật trạng thái checkbox "Chọn tất cả"
    function updateSelectAllCheckbox() {
        const selectAllCheckbox = document.getElementById('select_all_components');
        const visibleCheckboxes = Array.from(document.querySelectorAll('.component-checkbox')).filter(cb => {
            const item = cb.closest('.component-item');
            return item && item.style.display !== 'none';
        });
        const checkedVisible = visibleCheckboxes.filter(cb => cb.checked);
        
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = visibleCheckboxes.length > 0 && checkedVisible.length === visibleCheckboxes.length;
        }
    }

    // Hàm chọn/bỏ chọn tất cả thành phần lương
    function toggleAllComponents(checkbox) {
        const componentCheckboxes = document.querySelectorAll('.component-checkbox');
        componentCheckboxes.forEach(cb => {
            const item = cb.closest('.component-item');
            // Chỉ toggle các checkbox đang hiển thị
            if (item && item.style.display !== 'none') {
                cb.checked = checkbox.checked;
            }
        });
        updateValueFieldsForMultiple('create');
    }

    // Hàm lấy giá trị cơ bản từ API
    async function loadBaseValues(formType, componentId, teacherId, effectiveDate) {
        if (!componentId || !teacherId) {
            return;
        }

        try {
            const url = `{{ url('admin/teacherpayrollcomponent/get-base-values') }}?componentid=${componentId}&teacherid=${teacherId}&effectivedate=${effectiveDate || ''}`;
            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                // Điền giá trị cơ bản
                if (data.base_coefficient !== null && data.base_coefficient !== undefined) {
                    const baseCoeffInput = document.getElementById(formType + '_customcoefficient');
                    if (baseCoeffInput) {
                        baseCoeffInput.value = parseFloat(data.base_coefficient).toFixed(4);
                    }
                }

                if (data.base_percentage !== null && data.base_percentage !== undefined) {
                    const basePercentageInput = document.getElementById(formType + '_custompercentage');
                    if (basePercentageInput) {
                        basePercentageInput.value = parseFloat(data.base_percentage).toFixed(4);
                    }
                }

                if (data.base_fixed_amount !== null && data.base_fixed_amount !== undefined) {
                    const baseFixedInput = document.getElementById(formType + '_customfixedamount');
                    if (baseFixedInput) {
                        baseFixedInput.value = parseFloat(data.base_fixed_amount).toFixed(2);
                    }
                }

                // Tự động set giá trị điều chỉnh từ unit config vào input (chỉ khi input rỗng hoặc bằng 0)
                if (data.unit_adjust_coefficient !== null && data.unit_adjust_coefficient !== undefined) {
                    const adjustCoefficientInput = document.getElementById(formType + '_adjustcustomcoefficient');
                    if (adjustCoefficientInput) {
                        const currentValue = parseFloat(adjustCoefficientInput.value || 0);
                        // Chỉ set nếu giá trị hiện tại là 0 hoặc rỗng
                        if (currentValue === 0 || adjustCoefficientInput.value === '' || adjustCoefficientInput.value === null) {
                            adjustCoefficientInput.value = parseFloat(data.unit_adjust_coefficient).toFixed(4);
                        }
                    }
                }

                // Tự động set giá trị điều chỉnh phần trăm từ unit config vào input (chỉ khi input rỗng hoặc bằng 0)
                if (data.unit_adjust_percentage !== null && data.unit_adjust_percentage !== undefined) {
                    const adjustPercentageInput = document.getElementById(formType + '_adjustcustompercentage');
                    if (adjustPercentageInput) {
                        const currentValue = parseFloat(adjustPercentageInput.value || 0);
                        // Chỉ set nếu giá trị hiện tại là 0 hoặc rỗng
                        if (currentValue === 0 || adjustPercentageInput.value === '' || adjustPercentageInput.value === null) {
                            adjustPercentageInput.value = parseFloat(data.unit_adjust_percentage).toFixed(4);
                        }
                    }
                }
            }
        } catch (error) {
            console.error('Error loading base values:', error);
        }
    }

    // Hàm cập nhật hiển thị trường giá trị khi chọn nhiều thành phần
    async function updateValueFieldsForMultiple(formType) {
        const checkedComponents = document.querySelectorAll('.component-checkbox:checked');
        const teacherId = document.getElementById('create_teacherid')?.value;
        const effectiveDate = document.getElementById('create_effectivedate')?.value;
        
        if (checkedComponents.length === 0) {
            updateSelectAllCheckbox();
            return;
        }
        
        // Nếu chọn đúng 1 component, tự động load giá trị từ API vào hidden inputs
        if (checkedComponents.length === 1 && teacherId && effectiveDate) {
            const selectedComponent = checkedComponents[0];
            const componentId = selectedComponent.value;
            await loadBaseValues('create', componentId, teacherId, effectiveDate);
        }
        
        // Cập nhật trạng thái "Chọn tất cả"
        updateSelectAllCheckbox();
        
        // Nếu chỉ chọn 1 component, lấy giá trị cơ bản từ API
        if (checkedComponents.length === 1 && teacherId && effectiveDate) {
            const componentId = checkedComponents[0].value;
            await loadBaseValues(formType, componentId, teacherId, effectiveDate);
        }
        
        // Không cần tính toán giá trị cuối cùng nữa
    }

    // Hàm cập nhật hiển thị trường giá trị theo calculationmethod (cho form edit)
    function updateValueFields(formType, componentId) {
        const select = document.getElementById(formType + '_componentid');
        if (!select || !componentId) {
            // Reset về mặc định - cho phép nhập cả 2 ô
            const adjustCoeffInput = document.getElementById(formType + '_adjustcustomcoefficient');
            const adjustPercentInput = document.getElementById(formType + '_adjustcustompercentage');
            if (adjustCoeffInput) adjustCoeffInput.disabled = false;
            if (adjustPercentInput) adjustPercentInput.disabled = false;
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const method = selectedOption ? selectedOption.getAttribute('data-method') : '';

        // Lấy các input điều chỉnh
        const adjustCoeffInput = document.getElementById(formType + '_adjustcustomcoefficient');
        const adjustPercentInput = document.getElementById(formType + '_adjustcustompercentage');

        // Reset về mặc định - cho phép nhập cả 2 ô
        if (adjustCoeffInput) adjustCoeffInput.disabled = false;
        if (adjustPercentInput) adjustPercentInput.disabled = false;

        // Khóa các ô không liên quan dựa trên loại thành phần
        if (method === 'Hệ số') {
            // Nếu là Hệ số → khóa ô Tỷ lệ phần trăm
            if (adjustPercentInput) {
                adjustPercentInput.disabled = true;
                adjustPercentInput.value = '0'; // Reset về 0 khi khóa
            }
        } else if (method === 'Phần trăm') {
            // Nếu là Phần trăm → khóa ô Hệ số
            if (adjustCoeffInput) {
                adjustCoeffInput.disabled = true;
                adjustCoeffInput.value = '0'; // Reset về 0 khi khóa
            }
        } else if (method === 'Cố định') {
            // Nếu là Cố định → khóa cả 2 ô
            if (adjustCoeffInput) {
                adjustCoeffInput.disabled = true;
                adjustCoeffInput.value = '0';
            }
            if (adjustPercentInput) {
                adjustPercentInput.disabled = true;
                adjustPercentInput.value = '0';
            }
        }
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

        // Event listeners cho các input điều chỉnh (form create)
        const createAdjustCoefficientInput = document.getElementById('create_adjustcustomcoefficient');
        const createAdjustPercentageInput = document.getElementById('create_adjustcustompercentage');
        
        if (createAdjustCoefficientInput) {
            createAdjustCoefficientInput.addEventListener('input', function() {
                // Không cần tính toán gì, chỉ lưu giá trị
            });
        }
        
        if (createAdjustPercentageInput) {
            createAdjustPercentageInput.addEventListener('input', function() {
                // Không cần tính toán gì, chỉ lưu giá trị
            });
        }

        // Event listeners cho các input điều chỉnh (form edit)
        const editAdjustCoefficientInput = document.getElementById('edit_adjustcustomcoefficient');
        const editAdjustPercentageInput = document.getElementById('edit_adjustcustompercentage');
        
        if (editAdjustCoefficientInput) {
            editAdjustCoefficientInput.addEventListener('input', function() {
                // Không cần tính toán gì, chỉ lưu giá trị
            });
        }
        
        if (editAdjustPercentageInput) {
            editAdjustPercentageInput.addEventListener('input', function() {
                // Không cần tính toán gì, chỉ lưu giá trị
            });
        }

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

        // Khởi tạo hiển thị trường giá trị cho form create (nếu đã chọn component)
        updateValueFieldsForMultiple('create');
        
        // Cập nhật trạng thái "Chọn tất cả" khi các checkbox thay đổi
        const componentCheckboxes = document.querySelectorAll('.component-checkbox');
        const selectAllCheckbox = document.getElementById('select_all_components');
        if (componentCheckboxes.length > 0 && selectAllCheckbox) {
            componentCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const allChecked = Array.from(componentCheckboxes).every(c => c.checked);
                    const someChecked = Array.from(componentCheckboxes).some(c => c.checked);
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                    updateValueFieldsForMultiple('create');
                });
            });
        }

        // Thêm validation cho ngày hết hạn (form create)
        const createEffectiveDate = document.getElementById('create_effectivedate');
        const createExpirationDate = document.getElementById('create_expirationdate');
        if (createEffectiveDate) {
            createEffectiveDate.addEventListener('change', function() {
                validateExpirationDate('create');
                // Khi thay đổi ngày hiệu lực, lấy lại giá trị cơ bản nếu đã chọn component
                const checkedComponents = document.querySelectorAll('.component-checkbox:checked');
                const teacherId = document.getElementById('create_teacherid')?.value;
                if (checkedComponents.length === 1 && teacherId && createEffectiveDate.value) {
                    const componentId = checkedComponents[0].value;
                    loadBaseValues('create', componentId, teacherId, createEffectiveDate.value);
                }
            });
        }
        if (createExpirationDate) {
            createExpirationDate.addEventListener('change', function() {
                validateExpirationDate('create');
            });
        }
        
        // Khi chọn giáo viên thay đổi, lấy lại giá trị cơ bản nếu đã chọn component và ngày hiệu lực
        const createTeacherSelect = document.getElementById('create_teacherid');
        if (createTeacherSelect) {
            createTeacherSelect.addEventListener('change', async function() {
                await filterComponentsByTeacher(this.value);
                // Sau khi filter xong, nếu có component được chọn, lấy giá trị cơ bản
                const checkedComponents = document.querySelectorAll('.component-checkbox:checked');
                const effectiveDate = document.getElementById('create_effectivedate')?.value;
                if (checkedComponents.length === 1 && this.value && effectiveDate) {
                    const componentId = checkedComponents[0].value;
                    loadBaseValues('create', componentId, this.value, effectiveDate);
                }
            });
        }

        // Thêm validation cho ngày hết hạn (form edit)
        const editEffectiveDate = document.getElementById('edit_effectivedate');
        const editExpirationDate = document.getElementById('edit_expirationdate');
        if (editEffectiveDate) {
            editEffectiveDate.addEventListener('change', function() {
                validateExpirationDate('edit');
                // Khi thay đổi ngày hiệu lực, lấy lại giá trị cơ bản
                const componentId = document.getElementById('edit_componentid')?.value;
                const teacherId = document.getElementById('edit_teacherid')?.value;
                if (componentId && teacherId && editEffectiveDate.value) {
                    loadBaseValues('edit', componentId, teacherId, editEffectiveDate.value);
                }
            });
        }
        if (editExpirationDate) {
            editExpirationDate.addEventListener('change', function() {
                validateExpirationDate('edit');
            });
        }
        
        // Khi chọn component trong form edit, lấy giá trị cơ bản
        const editComponentSelect = document.getElementById('edit_componentid');
        if (editComponentSelect) {
            editComponentSelect.addEventListener('change', function() {
                updateValueFields('edit', this.value);
                // Lấy giá trị cơ bản từ API
                const teacherId = document.getElementById('edit_teacherid')?.value;
                const effectiveDate = document.getElementById('edit_effectivedate')?.value;
                if (this.value && teacherId && effectiveDate) {
                    loadBaseValues('edit', this.value, teacherId, effectiveDate);
                }
            });
        }
        
        // Khi chọn giáo viên trong form edit, filter component và lấy lại giá trị cơ bản
        const editTeacherSelect = document.getElementById('edit_teacherid');
        if (editTeacherSelect) {
            editTeacherSelect.addEventListener('change', async function() {
                const currentComponentId = document.getElementById('edit_componentid')?.value;
                const effectiveDate = document.getElementById('edit_effectivedate')?.value;
                
                // Filter các component đã có của giáo viên này (trừ component hiện tại)
                if (this.value) {
                    await filterEditComponentsByTeacher(this.value, currentComponentId ? parseInt(currentComponentId) : null);
                }
                
                // Lấy lại giá trị cơ bản nếu có component và ngày hiệu lực
                if (currentComponentId && this.value && effectiveDate) {
                    loadBaseValues('edit', currentComponentId, this.value, effectiveDate);
                }
            });
        }

        // View button handler
        document.addEventListener('click', async function (e) {
            if (e.target.closest('.view-teacherpayrollcomponent-btn')) {
                const btn = e.target.closest('.view-teacherpayrollcomponent-btn');
                const componentMethod = btn.dataset.componentMethod || '';
                const componentId = btn.dataset.componentId || '';
                const teacherId = btn.dataset.teacherId || '';
                const effectiveDate = btn.dataset.effectiveDate || '';
                
                // Set thông tin cơ bản
                document.getElementById('view_configid').value = btn.dataset.configId || '';
                document.getElementById('view_teachername').value = btn.dataset.teacherName || '';
                document.getElementById('view_componentname').value = btn.dataset.componentName || '';
                
                // Format ngày
                document.getElementById('view_effectivedate').value = effectiveDate ? new Date(effectiveDate + 'T00:00:00').toLocaleDateString('vi-VN') : '-';
                
                const expirationDate = btn.dataset.expirationDate || '';
                document.getElementById('view_expirationdate').value = expirationDate ? new Date(expirationDate + 'T00:00:00').toLocaleDateString('vi-VN') : 'Đang hiệu lực';

                // Hiển thị giá trị điều chỉnh (giá trị tuyệt đối, không có dấu)
                const adjustCoeff = parseFloat(btn.dataset.adjustCustomCoefficient || 0);
                const adjustPercent = parseFloat(btn.dataset.adjustCustomPercentage || 0);
                
                const viewAdjustCoeffInput = document.getElementById('view_adjustcustomcoefficient');
                if (viewAdjustCoeffInput) {
                    viewAdjustCoeffInput.value = Math.abs(adjustCoeff).toFixed(4);
                }
                
                const viewAdjustPercentInput = document.getElementById('view_adjustcustompercentage');
                if (viewAdjustPercentInput) {
                    viewAdjustPercentInput.value = Math.abs(adjustPercent).toFixed(4);
                }

                document.getElementById('view_note').value = btn.dataset.note || '';
            }
        });

        // Edit button handler
        document.addEventListener('click', function (e) {
            if (e.target.closest('.edit-teacherpayrollcomponent-btn')) {
                const btn = e.target.closest('.edit-teacherpayrollcomponent-btn');
                const configId = btn.dataset.configId;
                const currentTeacherId = btn.dataset.teacherId || '';
                const currentComponentId = btn.dataset.componentId || '';
                const currentComponentName = btn.dataset.componentName || '';
                const currentComponentMethod = btn.dataset.componentMethod || '';
                
                const form = document.getElementById('editTeacherPayrollComponentForm');
                if (form) {
                    form.action = '{{ route("accounting.teacherpayrollcomponent.update", ":id") }}'.replace(':id', configId);
                }

                // Đợi modal hiển thị trước khi set giá trị
                const editModal = document.getElementById('edit_teacherpayrollcomponent');
                if (editModal) {
                    editModal.addEventListener('shown.bs.modal', async function modalShown() {
                        // Set giáo viên
                        const teacherSelect = document.getElementById('edit_teacherid');
                        if (teacherSelect) {
                            teacherSelect.value = currentTeacherId;
                        }
                        
                        // Filter các component đã có của giáo viên này (trừ component hiện tại)
                        if (currentTeacherId) {
                            await filterEditComponentsByTeacher(currentTeacherId, parseInt(currentComponentId));
                        }
                        
                        // Đảm bảo component hiện tại có trong dropdown
                        const editComponentSelect = document.getElementById('edit_componentid');
                        if (editComponentSelect && currentComponentId) {
                            // Kiểm tra xem component đã có trong dropdown chưa
                            const existingOption = editComponentSelect.querySelector(`option[value="${currentComponentId}"]`);
                            if (!existingOption && currentComponentName && currentComponentMethod) {
                                // Thêm component hiện tại vào dropdown
                                const newOption = document.createElement('option');
                                newOption.value = currentComponentId;
                                newOption.setAttribute('data-method', currentComponentMethod);
                                newOption.textContent = currentComponentName + ' (' + currentComponentMethod + ')';
                                editComponentSelect.appendChild(newOption);
                            }
                            // Set component (sau khi đảm bảo nó có trong dropdown)
                            editComponentSelect.value = currentComponentId;
                        }
                        
                        const effectivedateInput = document.getElementById('edit_effectivedate');
                        if (effectivedateInput) {
                            effectivedateInput.value = btn.dataset.effectiveDate || '';
                        }
                        
                        const expirationdateInput = document.getElementById('edit_expirationdate');
                        if (expirationdateInput) {
                            expirationdateInput.value = btn.dataset.expirationDate || '';
                        }
                        
                        // Set giá trị điều chỉnh
                        const adjustCustomCoefficientInput = document.getElementById('edit_adjustcustomcoefficient');
                        if (adjustCustomCoefficientInput) {
                            adjustCustomCoefficientInput.value = btn.dataset.adjustCustomCoefficient || '';
                        }
                        
                        const adjustCustomPercentageInput = document.getElementById('edit_adjustcustompercentage');
                        if (adjustCustomPercentageInput) {
                            adjustCustomPercentageInput.value = btn.dataset.adjustCustomPercentage || '';
                        }
                        
                        const noteInput = document.getElementById('edit_note');
                        if (noteInput) {
                            noteInput.value = btn.dataset.note || '';
                        }
                        
                        // Khóa các ô không liên quan dựa trên loại thành phần
                        updateValueFields('edit', currentComponentId);
                        
                        // Lấy giá trị cơ bản từ API (theo ngày hiệu lực mới nếu có)
                        const effectiveDate = effectivedateInput ? effectivedateInput.value : btn.dataset.effectiveDate || '';
                        if (currentComponentId && currentTeacherId && effectiveDate) {
                            loadBaseValues('edit', currentComponentId, currentTeacherId, effectiveDate);
                        }
                        
                        // Sau khi load giá trị, cần khóa lại các ô không liên quan
                        setTimeout(() => {
                            updateValueFields('edit', currentComponentId);
                        }, 100);
                        
                        // Không cần tính toán giá trị cuối cùng nữa
                        
                        // Xóa event listener sau khi chạy một lần
                        editModal.removeEventListener('shown.bs.modal', modalShown);
                    }, { once: true });
                }
            }
        });

        // Delete button handler
        document.addEventListener('click', function (e) {
            if (e.target.closest('.delete-teacherpayrollcomponent-btn')) {
                const btn = e.target.closest('.delete-teacherpayrollcomponent-btn');
                const configId = btn.dataset.configId;
                const form = document.getElementById('deleteTeacherPayrollComponentForm');
                form.action = '{{ route("accounting.teacherpayrollcomponent.destroy", ":id") }}'.replace(':id', configId);
                const teacherName = btn.dataset.teacherName || '';
                const componentName = btn.dataset.componentName || '';
                document.getElementById('delete_config_name').textContent = (teacherName && componentName) ? `${teacherName} - ${componentName}` : 'này';
            }
        });
        
        // Các trường giá trị riêng đã được ẩn - không cần xử lý nữa
    });
</script>
@endpush

