<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SystemUserCredentialsMail;
use App\Mail\SystemUserResetCodeMail;
use App\Models\SystemUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }
    public function sendResetCode(Request $request)
    {
        $data = $request->validate(
            [
                'email' => ['required', 'email'],
            ],
            [
                'email.required' => 'Vui lòng nhập email.',
                'email.email'    => 'Email không hợp lệ.',
            ]
        );

        $email = trim($data['email']);

        $user = SystemUser::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'Email này không tồn tại trong hệ thống.',
            ], 404);
        }

        $code = (string) random_int(100000, 999999);

        $cacheKey = 'password_reset:' . $email;
        Cache::put($cacheKey, Hash::make($code), now()->addMinutes(15));

        try {
            Mail::to($email)->send(new SystemUserResetCodeMail($user, $code));
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Không thể gửi email mã xác nhận. Vui lòng thử lại sau.',
            ], 500);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Đã gửi mã xác nhận tới email của bạn. Vui lòng kiểm tra hộp thư.',
        ]);
    }

    public function verifyResetCode(Request $request)
    {
        $data = $request->validate(
            [
                'email' => ['required', 'email'],
                'code'  => ['required', 'digits:6'],
            ],
            [
                'email.required' => 'Vui lòng nhập email.',
                'email.email'    => 'Email không hợp lệ.',
                'code.required'  => 'Vui lòng nhập mã xác nhận.',
                'code.digits'    => 'Mã xác nhận phải gồm 6 chữ số.',
            ]
        );

        $email = trim($data['email']);
        $code  = $data['code'];

        $user = SystemUser::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'Email này không tồn tại trong hệ thống.',
            ], 404);
        }

        $cacheKey = 'password_reset:' . $email;
        $hashedCode = Cache::get($cacheKey);

        if (!$hashedCode) {
            return response()->json([
                'ok' => false,
                'message' => 'Mã xác nhận không hợp lệ hoặc đã hết hạn.',
            ], 422);
        }

        if (!Hash::check($code, $hashedCode)) {
            return response()->json([
                'ok' => false,
                'message' => 'Mã xác nhận không chính xác.',
            ], 422);
        }

        Cache::put('password_reset_verified:' . $email, true, now()->addMinutes(15));

        return response()->json([
            'ok' => true,
            'message' => 'Mã xác nhận chính xác. Vui lòng nhập mật khẩu mới.',
        ]);
    }


    public function resetPassword(Request $request)
    {
        $data = $request->validate(
            [
                'email' => ['required', 'email'],
                'password' => ['required', 'string', 'min:8'],
                'password_confirmation' => ['required', 'same:password'],
            ],
            [
                'email.required' => 'Vui lòng nhập email.',
                'email.email'    => 'Email không hợp lệ.',
                'password.required' => 'Vui lòng nhập mật khẩu mới.',
                'password.min'      => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
                'password_confirmation.required' => 'Vui lòng nhập lại mật khẩu mới.',
                'password_confirmation.same'     => 'Mật khẩu xác nhận không trùng khớp.',
            ]
        );

        $email = trim($data['email']);

        $user = SystemUser::findOrFail(
            SystemUser::where('email', $email)->value('userid')
        );

        $verified = Cache::get('password_reset_verified:' . $email);
        if (!$verified) {
            return response()->json([
                'ok' => false,
                'message' => 'Bạn cần nhập và xác nhận mã trước khi đặt lại mật khẩu.',
            ], 422);
        }

        $user->passwordhash = Hash::make($data['password']);
        $user->save();

        Cache::forget('password_reset:' . $email);
        Cache::forget('password_reset_verified:' . $email);

        return response()->json([
            'ok' => true,
            'message' => 'Đặt lại mật khẩu thành công. Bạn có thể đăng nhập bằng mật khẩu mới.',
        ]);
    }
}


