<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{

    public function showLoginForm()
    {
        $attempts = session('login_attempts', 0);

        if ($attempts >= 5 && !session()->has('login_captcha_question')) {
            $code = Str::random(6);
            session([
                'login_captcha_question' => "Nhập mã: {$code}",
                'login_captcha_answer'   => $code,
            ]);
        }

        return view('welcome');
    }

    public function login(Request $request)
    {
        $attempts = $request->session()->get('login_attempts', 0);

        $rules = [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
        $messages = [
            'username.required' => 'Vui lòng nhập tên đăng nhập',
            'password.required' => 'Vui lòng nhập mật khẩu',
        ];

        if ($attempts >= 5) {
            $rules['captcha'] = ['required', function ($attribute, $value, $fail) {
                $answer = session('login_captcha_answer');
                if (!$answer || strtoupper(trim($value)) !== strtoupper((string) $answer)) {
                    $fail('Mã xác thực không đúng. Vui lòng thử lại.');
                }
            }];
            $messages['captcha.required'] = 'Vui lòng nhập mã xác thực.';
        }

        $credentials = $request->validate($rules, $messages);

        $remember = $request->boolean('remember');

        $loginData = [
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'status'   => 'active',
        ];

        if (Auth::attempt($loginData, $remember)) {
            $request->session()->regenerate();

            $request->session()->forget(['login_attempts', 'login_captcha_question', 'login_captcha_answer']);

            $user = Auth::user();
            $roleName = $user->role ? Str::lower($user->role->rolename) : '';

            if (Str::contains($roleName, ['quản trị', 'admin'])) {
                return redirect()->intended(route('admin.dashboard'));
            }

            if (Str::contains($roleName, 'kế toán')) {
                return redirect()->intended(route('accounting.dashboard'));
            }

            if (Str::contains($roleName, 'giáo viên')) {
                return redirect()->intended(route('teacher.dashboard'));
            }

            Auth::logout();

            return back()
                ->withInput($request->only('username', 'remember'))
                ->withErrors([
                    'username' => 'Vai trò tài khoản không hợp lệ, vui lòng liên hệ quản trị hệ thống.',
                ]);
        }

        $attempts++;
        $request->session()->put('login_attempts', $attempts);

        if ($attempts >= 5) {
            $code = Str::random(6);
            $request->session()->put('login_captcha_question', "Nhập mã: {$code}");
            $request->session()->put('login_captcha_answer', $code);
        }

        return back()
            ->withInput($request->only('username', 'remember'))
            ->withErrors([
                'username' => 'Tên đăng nhập hoặc mật khẩu của bạn không đúng.',
            ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}


