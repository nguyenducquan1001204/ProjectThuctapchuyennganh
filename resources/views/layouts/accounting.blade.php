<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Accounting Dashboard')</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;0,900;1,400;1,500;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/icons/flags/flags.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @stack('styles')
</head>
<body>
    @php
        $currentUser  = auth()->user();
        $displayName  = $currentUser?->fullname ?? ($currentUser?->teacher?->fullname ?? 'Tài khoản');
        $displayRole  = $currentUser && $currentUser->role ? $currentUser->role->rolename : 'Vai trò';
    @endphp

    <div class="main-wrapper">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <a href="{{ route('accounting.dashboard') }}" class="logo">
                    <img src="{{ asset('assets/img/logo3.png') }}" alt="Logo">
                </a>
                <a href="{{ route('accounting.dashboard') }}" class="logo logo-small">
                    <img src="{{ asset('assets/img/logo2.png') }}" alt="Logo" width="30" height="30">
                </a>
            </div>
            <div class="menu-toggle">
                <a href="javascript:void(0);" id="toggle_btn">
                    <i class="fas fa-bars"></i>
                </a>
            </div>

            <div class="top-nav-search">
                <form>
                    <input type="text" class="form-control" placeholder="Tìm kiếm...">
                    <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <a class="mobile_btn" id="mobile_btn">
                <i class="fas fa-bars"></i>
            </a>

            <ul class="nav user-menu">
                <li class="nav-item dropdown noti-dropdown me-2">
                    <a href="#" class="dropdown-toggle nav-link header-nav-list" data-bs-toggle="dropdown">
                        <img src="{{ asset('assets/img/icons/header-icon-05.svg') }}" alt="">
                    </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header">
                            <span class="notification-title">Thông báo</span>
                            <a href="javascript:void(0)" class="clear-noti"> Xóa tất cả </a>
                        </div>
                        <div class="noti-content">
                            <ul class="notification-list">
                                <!-- Notifications -->
                            </ul>
                        </div>
                        <div class="topnav-dropdown-footer">
                            <a href="#">Xem tất cả thông báo</a>
                        </div>
                    </div>
                </li>

                <li class="nav-item zoom-screen me-2">
                    <a href="#" class="nav-link header-nav-list win-maximize">
                        <img src="{{ asset('assets/img/icons/header-icon-04.svg') }}" alt="">
                    </a>
                </li>

                <li class="nav-item dropdown has-arrow new-user-menus">
                    <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                        <span class="user-img">
                            @php
                                $avatarUrl = $currentUser && $currentUser->avatar
                                    ? asset('storage/'.$currentUser->avatar)
                                    : asset('assets/img/cropped_circle_image.png');
                            @endphp
                            <img class="rounded-circle" src="{{ $avatarUrl }}" width="31" alt="User">
                            <div class="user-text">
                                <h6>{{ $displayName }}</h6>
                                <p class="text-muted mb-0">{{ $displayRole }}</p>
                            </div>
                        </span>
                    </a>
                    <div class="dropdown-menu">
                        <div class="user-header">
                            <div class="avatar avatar-sm">
                                <img src="{{ $avatarUrl }}" alt="User Image" class="avatar-img rounded-circle">
                            </div>
                            <div class="user-text">
                                <h6>{{ $displayName }}</h6>
                                <p class="text-muted mb-0">{{ $displayRole }}</p>
                            </div>
                        </div>
                        <a class="dropdown-item" href="{{ route('profile.show') }}">Thông tin cá nhân</a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form-accounting').submit();">
                            Đăng xuất
                        </a>
                        <form id="logout-form-accounting" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">
                            <span>Bảng điều khiển</span>
                        </li>
                        @include('accounting.partials.menu')
                    </ul>
                </div>
            </div>
        </div>

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content container-fluid">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    @stack('scripts')
</body>
</html>


