<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quên mật khẩu - Quản lý lương giáo viên</title>

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
                            <h1>Quên mật khẩu</h1>
                            <p class="account-subtitle">Vui lòng nhập email đã đăng ký để nhận mã xác nhận và đặt lại mật khẩu.</p>

                            <div id="forgot_alert" class="alert d-none" role="alert"></div>

                            {{-- Giao diện + gọi AJAX tới backend để gửi mã và xác nhận mã --}}
                            <form action="javascript:void(0);" id="forgotPasswordForm">
                                <div class="form-group" id="forgot_email_group">
                                    <label>Nhập email đã đăng ký <span class="login-danger">*</span></label>
                                    <input class="form-control" type="email" name="email" required>
                                    <span class="profile-views"><i class="fas fa-envelope"></i></span>
                                </div>
                                <div class="form-group" id="verification_code_group" style="display: none;">
                                    <label>Nhập mã xác nhận đã gửi tới email <span class="login-danger">*</span></label>
                                    <input class="form-control" type="text" name="verification_code">
                                    <span class="profile-views"><i class="fas fa-key"></i></span>
                                </div>
                                <div class="form-group" id="new_password_group" style="display: none;">
                                    <label>Mật khẩu mới <span class="login-danger">*</span></label>
                                    <input class="form-control" type="password" name="password">
                                    <span class="profile-views"><i class="fas fa-lock"></i></span>
                                </div>
                                <div class="form-group" id="confirm_password_group" style="display: none;">
                                    <label>Nhập lại mật khẩu mới <span class="login-danger">*</span></label>
                                    <input class="form-control" type="password" name="password_confirmation">
                                    <span class="profile-views"><i class="fas fa-lock"></i></span>
                                </div>
                                <div class="form-group">
                                    <button id="send_reset_btn" class="btn btn-primary btn-block" type="button">
                                        Gửi yêu cầu đặt lại mật khẩu
                                    </button>
                                    <button id="verify_code_btn" class="btn btn-primary btn-block" type="button" style="display: none;">
                                        Xác nhận mã
                                    </button>
                                    <button id="reset_password_btn" class="btn btn-primary btn-block" type="button" style="display: none;">
                                        Đặt lại mật khẩu
                                    </button>
                                </div>
                                <div class="form-group mb-0">
                                    <a class="btn btn-primary primary-reset btn-block" href="{{ route('login') }}">
                                        Quay lại trang đăng nhập
                                    </a>
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

    <script>
        (function () {
            const form = document.getElementById('forgotPasswordForm');
            const emailInput = form.querySelector('input[name="email"]');
            const codeInput = form.querySelector('input[name="verification_code"]');
            const passwordInput = form.querySelector('input[name="password"]');
            const confirmInput = form.querySelector('input[name="password_confirmation"]');

            const codeGroup = document.getElementById('verification_code_group');
            const newPasswordGroup = document.getElementById('new_password_group');
            const confirmPasswordGroup = document.getElementById('confirm_password_group');

            const sendBtn = document.getElementById('send_reset_btn');
            const verifyBtn = document.getElementById('verify_code_btn');
            const resetBtn = document.getElementById('reset_password_btn');

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const alertBox = document.getElementById('forgot_alert');

            function showAlert(type, message) {
                alertBox.className = 'alert alert-' + type;
                alertBox.textContent = message;
                alertBox.classList.remove('d-none');
            }

            // Bước 1: Gửi mã xác nhận
            sendBtn.addEventListener('click', function () {
                const email = emailInput.value.trim();
                if (!email) {
                    emailInput.classList.add('is-invalid');
                    return;
                }
                emailInput.classList.remove('is-invalid');

                sendBtn.disabled = true;
                const oldText = sendBtn.innerHTML;
                sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang gửi...';

                fetch("{{ route('password.sendCode') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email: email }),
                })
                    .then(async (res) => {
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok || !data.ok) {
                            showAlert('danger', data.message || 'Không thể gửi mã xác nhận. Vui lòng thử lại.');
                            return;
                        }

                        showAlert('success', data.message || 'Đã gửi mã xác nhận tới email của bạn.');

                        // Chỉ hiển thị ô nhập mã và nút xác nhận
                        codeGroup.style.display = 'block';
                        sendBtn.style.display = 'none';
                        verifyBtn.style.display = 'block';
                        emailInput.readOnly = true;
                    })
                    .catch(() => {
                        showAlert('danger', 'Không thể gửi mã xác nhận. Vui lòng thử lại sau.');
                    })
                    .finally(() => {
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = oldText;
                    });
            });

            // Bước 2: Xác nhận mã
            verifyBtn.addEventListener('click', function () {
                const email = emailInput.value.trim();
                const code = codeInput.value.trim();

                if (!code) {
                    codeInput.classList.add('is-invalid');
                    return;
                }
                codeInput.classList.remove('is-invalid');

                verifyBtn.disabled = true;
                const oldText = verifyBtn.innerHTML;
                verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang kiểm tra...';

                fetch("{{ route('password.verifyCode') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email: email, code: code }),
                })
                    .then(async (res) => {
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok || !data.ok) {
                            showAlert('danger', data.message || 'Mã xác nhận không hợp lệ.');
                            return;
                        }

                        showAlert('success', data.message || 'Mã xác nhận chính xác. Vui lòng nhập mật khẩu mới.');

                        // Cho phép nhập mật khẩu mới
                        newPasswordGroup.style.display = 'block';
                        confirmPasswordGroup.style.display = 'block';
                        verifyBtn.style.display = 'none';
                        resetBtn.style.display = 'block';
                    })
                    .catch(() => {
                        showAlert('danger', 'Không thể xác nhận mã. Vui lòng thử lại sau.');
                    })
                    .finally(() => {
                        verifyBtn.disabled = false;
                        verifyBtn.innerHTML = oldText;
                    });
            });

            // Bước 3: Đặt lại mật khẩu
            resetBtn.addEventListener('click', function () {
                const email = emailInput.value.trim();
                const password = passwordInput.value.trim();
                const passwordConfirm = confirmInput.value.trim();

                if (!password || password.length < 8) {
                    passwordInput.classList.add('is-invalid');
                    showAlert('danger', 'Mật khẩu mới phải có ít nhất 8 ký tự.');
                    return;
                }
                passwordInput.classList.remove('is-invalid');

                if (password !== passwordConfirm) {
                    confirmInput.classList.add('is-invalid');
                    showAlert('danger', 'Mật khẩu xác nhận không trùng khớp.');
                    return;
                }
                confirmInput.classList.remove('is-invalid');

                resetBtn.disabled = true;
                const oldText = resetBtn.innerHTML;
                resetBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang đặt lại...';

                fetch("{{ route('password.reset') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password,
                        password_confirmation: passwordConfirm,
                    }),
                })
                    .then(async (res) => {
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok || !data.ok) {
                            showAlert('danger', data.message || 'Không thể đặt lại mật khẩu.');
                            return;
                        }

                        showAlert('success', data.message || 'Đặt lại mật khẩu thành công. Bạn có thể đăng nhập bằng mật khẩu mới.');
                        window.location.href = "{{ route('login') }}";
                    })
                    .catch(() => {
                        showAlert('danger', 'Không thể đặt lại mật khẩu. Vui lòng thử lại sau.');
                    })
                    .finally(() => {
                        resetBtn.disabled = false;
                        resetBtn.innerHTML = oldText;
                    });
            });
        })();
    </script>
</body>

</html>


