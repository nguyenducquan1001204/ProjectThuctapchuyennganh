<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>{{ config('app.name', 'Quản lý lương giáo viên') }} - Đăng nhập</title>

    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">

    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;0,900;1,400;1,500;1,700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/icons/flags/flags.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
            <style>
        body {
            background-color: #87CEEB !important;
        }
        .main-wrapper.login-body {
            background-color: #87CEEB !important;
        }
            </style>
    </head>

<body>

    <div class="main-wrapper login-body">
        <div class="login-wrapper">
            <div class="container">
                <div class="loginbox">
                    <div class="login-left">
                        <img class="img-fluid" src="{{ asset('assets/img/piclogin.png') }}" alt="Logo">
                    </div>
                    <div class="login-right">
                        <div class="login-right-wrap">
                            <h1>Chào mừng đến với Website</h1>
                            <h1 style="text-align: center; padding-bottom: 20px;">Quản lý lương giáo viên</h1>
                            <h2 style="text-align: center;">Đăng nhập</h2>

                            {{-- Form đăng nhập --}}
                            <form action="{{ route('login.attempt') }}" method="POST">
                                @csrf

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        {{ $errors->first() }}
                                    </div>
                        @endif
                                <div class="form-group">
                                    <label>Tên đăng nhập <span class="login-danger">*</span></label>
                                    <input class="form-control" type="text" name="username" value="{{ old('username') }}" required>
                                    <span class="profile-views"><i class="fas fa-user-circle"></i></span>
                                </div>
                                <div class="form-group">
                                    <label>Mật khẩu <span class="login-danger">*</span></label>
                                    <input class="form-control pass-input" type="password" name="password" required>
                                    <span class="profile-views feather-eye toggle-password"></span>
                                </div>
                                @if (session('login_attempts', 0) >= 5)
                                    <div class="form-group">
                                        <label>Mã xác thực <span class="login-danger">*</span></label>
                                        <div class="d-flex align-items-center">
                                            <input class="form-control" type="text" name="captcha" style="max-width: 180px;" required>
                                            <span class="ms-3 text-muted">
                                                {{ session('login_captcha_question') }}
                                            </span>
                                        </div>
                                        @error('captcha')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
            @endif
                                <div class="forgotpass">
                                    <div class="remember-me">
                                        <label class="custom_check mr-2 mb-0 d-inline-flex remember-me">
                                            Ghi nhớ đăng nhập
                                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <a href="{{ route('password.request') }}">Quên mật khẩu</a>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-block" type="submit">Đăng nhập</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </div>


    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    </body>

</html>


