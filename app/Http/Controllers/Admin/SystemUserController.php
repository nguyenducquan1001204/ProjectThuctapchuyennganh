<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SystemUserCredentialsMail;
use App\Models\Role;
use App\Models\SystemUser;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class SystemUserController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemUser::query()->with(['teacher', 'role']);

        if ($request->filled('search_username')) {
            $query->where('username', 'like', '%' . $request->search_username . '%');
        }

        if ($request->filled('search_email')) {
            $query->where('email', 'like', '%' . $request->search_email . '%');
        }

        if ($request->filled('search_fullname')) {
            $query->where('fullname', 'like', '%' . $request->search_fullname . '%');
        }

        if ($request->filled('search_status')) {
            $query->where('status', $request->search_status);
        }

        if ($request->filled('search_roleid')) {
            $query->where('roleid', $request->search_roleid);
        }

        $users = $query->orderBy('userid', 'asc')->get();

        $teachersWithAccount = SystemUser::whereNotNull('teacherid')->pluck('teacherid')->toArray();
        $teachers = Teacher::query()
            ->when(!empty($teachersWithAccount), function ($q) use ($teachersWithAccount) {
                $q->whereNotIn('teacherid', $teachersWithAccount);
            })
            ->orderBy('fullname')
            ->get();

        $allTeachers = Teacher::orderBy('fullname')->get();

        $roles = Role::orderBy('rolename')->get();

        return view('admin.systemusers.index', compact('users', 'teachers', 'allTeachers', 'roles'));
    }

    private function getValidationRules($ignoreId = null): array
    {
        $rules = [
            'username' => [
                'required',
                'string',
                'max:60',
                'min:4',
                'regex:/^[A-Za-z0-9_.]+$/',
            ],
            'email' => [
                'nullable',
                'string',
                'max:255',
                'email',
            ],
            'fullname' => [
                'nullable',
                'string',
                'max:255',
            ],
            'status' => [
                'required',
                Rule::in(['active', 'locked']),
            ],
            'teacherid' => [
                'nullable',
                'integer',
                'exists:teacher,teacherid',
            ],
            'roleid' => [
                'required',
                'integer',
                'exists:role,roleid',
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];

        if ($ignoreId) {
            $rules['username'][] = Rule::unique('systemuser', 'username')->ignore($ignoreId, 'userid');
        } else {
            $rules['username'][] = Rule::unique('systemuser', 'username');
        }

        if ($ignoreId) {
            $rules['email'][] = Rule::unique('systemuser', 'email')
                ->ignore($ignoreId, 'userid');
        } else {
            $rules['email'][] = Rule::unique('systemuser', 'email');
        }

        $rules['teacherid'][] = function ($attribute, $value, $fail) use ($ignoreId) {
            if ($value) {
                $query = SystemUser::where('teacherid', $value);
                if ($ignoreId) {
                    $query->where('userid', '<>', $ignoreId);
                }
                if ($query->exists()) {
                    $fail('Giáo viên này đã được gán cho một tài khoản khác.');
                }
            }
        };

        return $rules;
    }

    private function generateUniqueUsernameFromEmail(?string $email): string
    {
        $email = trim((string) $email);

        $localPart = $email !== '' ? (strstr($email, '@', true) ?: $email) : 'user';

        $base = preg_replace('/[^A-Za-z0-9_.]/', '_', $localPart);
        if ($base === '' || $base === null) {
            $base = 'user';
        }

        $base = substr($base, 0, 50);

        $username = $base;
        $suffix = 1;

        while (SystemUser::where('username', $username)->exists()) {
            $username = substr($base, 0, 50) . $suffix;
            $suffix++;
            if (strlen($username) > 60) {
                $username = substr($username, 0, 60);
            }
        }

        return $username;
    }

    private function getValidationMessages(): array
    {
        return [
            'username.required' => 'Tên đăng nhập là bắt buộc',
            'username.min' => 'Tên đăng nhập phải có ít nhất 4 ký tự',
            'username.max' => 'Tên đăng nhập không được vượt quá 60 ký tự',
            'username.regex' => 'Tên đăng nhập chỉ được chứa chữ cái, số, dấu chấm và gạch dưới',
            'username.unique' => 'Tên đăng nhập đã tồn tại',

            'email.email' => 'Email không hợp lệ',
            'email.max' => 'Email không được vượt quá 255 ký tự',
            'email.unique' => 'Email đã được sử dụng cho tài khoản khác',

            'fullname.max' => 'Họ và tên không được vượt quá 255 ký tự',

            'status.required' => 'Trạng thái tài khoản là bắt buộc',
            'status.in' => 'Trạng thái tài khoản không hợp lệ',

            'teacherid.exists' => 'Giáo viên được chọn không hợp lệ',

            'roleid.required' => 'Vai trò là bắt buộc',
            'roleid.exists' => 'Vai trò được chọn không hợp lệ',

            'avatar.image' => 'Ảnh đại diện phải là tệp hình ảnh',
            'avatar.mimes' => 'Ảnh đại diện chỉ chấp nhận các định dạng: jpg, jpeg, png, webp',
            'avatar.max' => 'Ảnh đại diện không được lớn hơn 2MB',
        ];
    }

    public function checkEmail(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'max:255', 'email'],
        ]);

        $email = trim($data['email']);

        if (SystemUser::where('email', $email)->exists()) {
            return response()->json([
                'ok' => false,
                'message' => 'Email đã được sử dụng cho tài khoản khác',
            ], 422);
        }

        $username = $this->generateUniqueUsernameFromEmail($email);

        return response()->json([
            'ok' => true,
            'username' => $username,
        ]);
    }

    public function store(Request $request)
    {
        $email    = $request->email ? trim($request->email) : null;
        $fullname = $request->fullname ? trim($request->fullname) : null;
        $username = $request->username ? trim($request->username) : null;

        if (empty($username)) {
            $username = $this->generateUniqueUsernameFromEmail($email);
        }

        $request->merge([
            'username' => $username,
            'email'    => $email,
            'fullname' => $fullname,
        ]);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        if (!empty($validated['roleid']) && !empty($validated['teacherid'])) {
            $role = Role::find($validated['roleid']);
            $teacher = Teacher::find($validated['teacherid']);

            if ($role && $teacher) {
                $roleText = Str::lower($role->rolename);
                if (Str::contains($roleText, 'giáo viên') || Str::contains($roleText, 'kế toán')) {
                    $validated['fullname'] = $teacher->fullname;
                }
            }
        }

        $plainPassword = Str::random(10);
        $validated['passwordhash'] = Hash::make($plainPassword);

        if (empty($validated['status'])) {
            $validated['status'] = 'active';
        }

        $user = SystemUser::create($validated);

        if (!empty($user->email)) {
            try {
                Mail::to($user->email)->send(new SystemUserCredentialsMail($user, $plainPassword));
            } catch (\Throwable $e) {
            }
        }

        return redirect()->route('admin.systemuser.index')
            ->with('success', 'Tạo tài khoản người dùng hệ thống thành công! Mật khẩu đã được gửi tới email (nếu có).');
    }

    public function update(Request $request, $id)
    {
        $user = SystemUser::findOrFail($id);

        $request->merge([
            'username'  => $user->username,
            'email'     => $request->email ? trim($request->email) : null,
            'fullname'  => $user->fullname,
            'teacherid' => $user->teacherid,
        ]);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($user->userid),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        if (!empty($validated['roleid']) && !empty($validated['teacherid'])) {
            $role = Role::find($validated['roleid']);
            $teacher = Teacher::find($validated['teacherid']);

            if ($role && $teacher) {
                $roleText = Str::lower($role->rolename);
                if (Str::contains($roleText, 'giáo viên') || Str::contains($roleText, 'kế toán')) {
                    $validated['fullname'] = $teacher->fullname;
                }
            }
        }

        unset($validated['passwordhash']);

        $user->update($validated);

        return redirect()->route('admin.systemuser.index')
            ->with('success', 'Cập nhật tài khoản người dùng hệ thống thành công!');
    }

    public function destroy($id)
    {
        $user = SystemUser::findOrFail($id);

        $user->delete();

        return redirect()->route('admin.systemuser.index')
            ->with('success', 'Xóa tài khoản người dùng hệ thống thành công!');
    }
}


