<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin tài khoản hệ thống</title>
</head>
<body>
    <p>Xin chào {{ $user->fullname ?? $user->username }},</p>

    <p>Tài khoản của bạn trên hệ thống quản lý lương giáo viên đã được tạo.</p>

    <p><strong>Thông tin đăng nhập:</strong></p>
    <ul>
        <li><strong>Tên đăng nhập:</strong> {{ $user->username }}</li>
        <li><strong>Mật khẩu tạm thời:</strong> {{ $plainPassword }}</li>
    </ul>

    <p>Vui lòng đăng nhập vào hệ thống và đổi mật khẩu ngay sau khi đăng nhập lần đầu để đảm bảo an toàn.</p>

    <p>Trân trọng,</p>
    <p>Hệ thống quản lý lương giáo viên</p>
</body>
</html>


