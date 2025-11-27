@extends($layout)

@section('title', 'Thông tin tài khoản')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col">
            <h3 class="page-title">Thông tin tài khoản</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route($dashboardRoute) }}">Trang chủ</a>
                </li>
                <li class="breadcrumb-item active">Thông tin tài khoản</li>
            </ul>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="profile-header">
            <div class="row align-items-center">
                <div class="col-auto profile-image">
                    @php
                        $avatarUrl = $user->avatar
                            ? asset('storage/'.$user->avatar)
                            : asset('assets/img/cropped_circle_image.png');
                    @endphp
                    <a href="#">
                        <img class="rounded-circle" alt="User Image" src="{{ $avatarUrl }}" width="80" height="80">
                    </a>
                </div>
                <div class="col ms-md-n2 profile-user-info">
                    <h4 class="user-name mb-0">{{ $user->fullname ?? $user->username }}</h4>
                    <h6 class="text-muted">{{ optional($user->role)->rolename ?? 'Không có vai trò' }}</h6>
                    <div class="user-Location">
                        <i class="fas fa-envelope"></i> {{ $user->email ?? 'Chưa cập nhật email' }}
                    </div>
                    @if($user->teacher)
                        <div class="about-text">Giáo viên: {{ $user->teacher->fullname }}</div>
                    @endif
                </div>
                <div class="col-auto profile-btn">
                    {{-- Có thể mở rộng nút Edit sau nếu cần --}}
                </div>
            </div>
        </div>

        <div class="profile-menu">
            @php
                $activeTab = session('active_tab', 'info');
            @endphp
            <ul class="nav nav-tabs nav-tabs-solid">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'info' ? 'active' : '' }}" data-bs-toggle="tab" href="#per_details_tab">Thông tin chung</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'password' ? 'active' : '' }}" data-bs-toggle="tab" href="#password_tab">Đổi mật khẩu</a>
                </li>
            </ul>
        </div>

        <div class="tab-content profile-tab-cont">
            <div class="tab-pane fade {{ $activeTab === 'info' ? 'show active' : '' }}" id="per_details_tab">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Chi tiết tài khoản</h5>
                                <div class="row profile-detail-row">
                                    <p class="col-sm-4 text-muted text-sm-end mb-0 mb-sm-3">Mã người dùng</p>
                                    <p class="col-sm-8">{{ $user->userid }}</p>
                                </div>
                                <div class="row profile-detail-row">
                                    <p class="col-sm-4 text-muted text-sm-end mb-0 mb-sm-3">Tên đăng nhập</p>
                                    <p class="col-sm-8">{{ $user->username }}</p>
                                </div>
                                <div class="row profile-detail-row">
                                    <p class="col-sm-4 text-muted text-sm-end mb-0 mb-sm-3">Họ và tên</p>
                                    <p class="col-sm-8">{{ $user->fullname ?? '-' }}</p>
                                </div>
                                <div class="row profile-detail-row">
                                    <p class="col-sm-4 text-muted text-sm-end mb-0 mb-sm-3">Email</p>
                                    <p class="col-sm-8">{{ $user->email ?? '-' }}</p>
                                </div>
                                <div class="row profile-detail-row">
                                    <p class="col-sm-4 text-muted text-sm-end mb-0 mb-sm-3">Vai trò</p>
                                    <p class="col-sm-8">{{ optional($user->role)->rolename ?? '-' }}</p>
                                </div>
                                <div class="row profile-detail-row">
                                    <p class="col-sm-4 text-muted text-sm-end mb-0 mb-sm-3">Trạng thái</p>
                                    <p class="col-sm-8">
                                        @if($user->status === 'active')
                                            <span class="badge bg-success">Đang hoạt động</span>
                                        @else
                                            <span class="badge bg-danger">Đã khóa</span>
                                        @endif
                                    </p>
                                </div>
                                @if($user->teacher)
                                    <div class="row profile-detail-row">
                                        <p class="col-sm-4 text-muted text-sm-end mb-0 mb-sm-3">Giáo viên liên kết</p>
                                        <p class="col-sm-8">{{ $user->teacher->fullname }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Cập nhật email &amp; ảnh đại diện</h5>
                                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                                        @error('email')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Ảnh đại diện</label>
                                        <input type="file" name="avatar" class="form-control" accept="image/*">
                                        <small class="text-muted d-block mt-1">Dung lượng tối đa 2MB. Hỗ trợ: jpg, jpeg, png, webp.</small>
                                        @error('avatar')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button class="btn btn-primary" type="submit">Lưu thông tin</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="tab-pane fade {{ $activeTab === 'password' ? 'show active' : '' }}" id="password_tab">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Đổi mật khẩu</h5>
                        <div class="row">
                            <div class="col-md-10 col-lg-6">
                                <form action="{{ route('profile.updatePassword') }}" method="POST">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label>Mật khẩu hiện tại</label>
                                        <input type="password" name="current_password" class="form-control">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label>Mật khẩu mới</label>
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label>Nhập lại mật khẩu mới</label>
                                        <input type="password" name="password_confirmation" class="form-control">
                                    </div>
                                    <button class="btn btn-primary" type="submit">Lưu thay đổi</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .profile-detail-row p {
        font-size: 1.05rem;
    }
</style>
@endpush
