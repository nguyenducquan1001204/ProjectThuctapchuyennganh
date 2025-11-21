<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang thông tin tài khoản.
     */
    public function show()
    {
        $user = Auth::user();

        $roleName = Str::lower(optional($user->role)->rolename ?? '');
        $layout = 'layouts.admin';
        $dashboardRoute = 'admin.dashboard';

        if (Str::contains($roleName, 'kế toán')) {
            $layout = 'layouts.accounting';
            $dashboardRoute = 'accounting.dashboard';
        } elseif (Str::contains($roleName, 'giáo viên')) {
            $layout = 'layouts.teacher';
            $dashboardRoute = 'teacher.dashboard';
        }

        return view('auth.profile', [
            'user'           => $user,
            'layout'         => $layout,
            'dashboardRoute' => $dashboardRoute,
        ]);
    }

    /**
     * Đổi mật khẩu từ trang thông tin tài khoản.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate(
            [
                'current_password'      => ['required'],
                'password'              => ['required', 'string', 'min:8'],
                'password_confirmation' => ['required', 'same:password'],
            ],
            [
                'current_password.required'      => 'Vui lòng nhập mật khẩu hiện tại.',
                'password.required'              => 'Vui lòng nhập mật khẩu mới.',
                'password.min'                   => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
                'password_confirmation.required' => 'Vui lòng nhập lại mật khẩu mới.',
                'password_confirmation.same'     => 'Mật khẩu xác nhận không trùng khớp.',
            ]
        );

        if (!Hash::check($data['current_password'], $user->passwordhash)) {
            return back()
                ->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.'])
                ->with('active_tab', 'password');
        }

        $user->passwordhash = Hash::make($data['password']);
        $user->save();

        return back()
            ->with('success', 'Đổi mật khẩu thành công.')
            ->with('active_tab', 'password');
    }

    /**
     * Cập nhật email và avatar.
     */
    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate(
            [
                'email'  => ['nullable', 'string', 'max:255', 'email', 'unique:systemuser,email,' . $user->userid . ',userid'],
                'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ],
            [
                'email.email'    => 'Email không hợp lệ.',
                'email.max'      => 'Email không được vượt quá 255 ký tự.',
                'email.unique'   => 'Email này đã được sử dụng cho tài khoản khác.',
                'avatar.image'   => 'Ảnh đại diện phải là tệp hình ảnh.',
                'avatar.mimes'   => 'Ảnh đại diện chỉ hỗ trợ các định dạng: jpg, jpeg, png, webp.',
                'avatar.max'     => 'Ảnh đại diện không được lớn hơn 2MB.',
            ]
        );

        // Cập nhật email
        $user->email = $data['email'] ?? null;

        // Xử lý avatar nếu có
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu tồn tại
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return back()
            ->with('success', 'Cập nhật thông tin tài khoản thành công.')
            ->with('active_tab', 'info');
    }
}


