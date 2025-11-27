<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Mã xác nhận đặt lại mật khẩu</title>
</head>
<body>
    <p>Xin chào {{ $user->fullname ?? $user->username }},</p>

    <p>Bạn (hoặc ai đó) vừa yêu cầu đặt lại mật khẩu cho tài khoản trên hệ thống quản lý lương giáo viên.</p>

    <p><strong>Mã xác nhận của bạn là:</strong></p>
    <h2 style="letter-spacing: 4px;">{{ $code }}</h2>

    <p>Mã này có hiệu lực trong 15 phút. Nếu bạn không yêu cầu đặt lại mật khẩu, có thể bỏ qua email này.</p>

    <p>Trân trọng,<br>Hệ thống quản lý lương giáo viên</p>
</body>
</html>


