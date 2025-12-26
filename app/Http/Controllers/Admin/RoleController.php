<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    private function getValidPhrases(): array
    {
        return [
            'quản trị viên', 'quản trị', 'admin', 'administrator',
            'kế toán', 'accountant',
            'nhân sự', 'human resources', 'hr',
            'giáo viên', 'teacher',
            'hiệu trưởng', 'principal', 'headmaster',
            'phó hiệu trưởng', 'vice principal', 'deputy principal',
            'quản lý', 'manager',
            'quản lý hệ thống', 'system manager',
            'nhân viên', 'employee', 'staff',
            'nhân viên hành chính', 'administrative staff',
            'nhân viên văn phòng', 'office staff',
            'kiểm tra viên', 'auditor',
        ];
    }

    private function validateRoleKeywords(string $value): bool
    {
        $valueLower = mb_strtolower(trim($value), 'UTF-8');
        $valueNormalized = preg_replace('/\s+/', ' ', $valueLower); 
        $hasValidPhrase = false;
        $matchedPhrase = null;
        foreach ($this->getValidPhrases() as $phrase) {
            if (mb_strpos($valueNormalized, $phrase, 0, 'UTF-8') !== false) {
                $hasValidPhrase = true;
                $matchedPhrase = $phrase;
                break;
            }
        }
        
        if (!$hasValidPhrase) {
            return false;
        }
        
        $words = preg_split('/\s+/', $valueNormalized);
        $previousWord = '';
        foreach ($words as $word) {
            $word = trim($word);
            if ($previousWord && $word === $previousWord && mb_strlen($word) > 2) {
                return false; 
            }
            $previousWord = $word;
        }
        $words = preg_split('/\s+/', $valueNormalized);
        $validWords = [];
        foreach ($this->getValidPhrases() as $phrase) {
            $phraseWords = preg_split('/\s+/', $phrase);
            $validWords = array_merge($validWords, $phraseWords);
        }
        $validWords = array_unique($validWords);
        $lastWordsOfPhrases = [];
        foreach ($this->getValidPhrases() as $phrase) {
            $phraseWords = preg_split('/\s+/', $phrase);
            if (count($phraseWords) > 0) {
                $lastWord = end($phraseWords);
                $lastWordsOfPhrases[] = $lastWord;
            }
        }
        $lastWordsOfPhrases = array_unique($lastWordsOfPhrases);
        
        foreach ($words as $index => $word) {
            $word = trim($word);
            if (in_array($word, $lastWordsOfPhrases) && mb_strlen($word) > 2) {
                $matchedPhraseWords = preg_split('/\s+/', $matchedPhrase);
                $matchedLastWord = end($matchedPhraseWords);
                
                if ($word === $matchedLastWord) {
                    $phrasePos = mb_strpos($valueNormalized, $matchedPhrase, 0, 'UTF-8');
                    $phraseEndPos = $phrasePos + mb_strlen($matchedPhrase, 'UTF-8');
                    $afterPhrase = mb_substr($valueNormalized, $phraseEndPos, null, 'UTF-8');
                    
                    if (trim($afterPhrase) && preg_match('/^\s+' . preg_quote($matchedLastWord, '/') . '\b/u', $afterPhrase)) {
                        return false;
                    }
                }
            }
        }
        
        $testValue = $valueNormalized;
        foreach ($this->getValidPhrases() as $phrase) {
            $testValue = str_replace($phrase, '', $testValue);
        }
        $testValue = preg_replace('/[\p{L}\p{N}\s,.\-()]/u', '', $testValue);
        if (mb_strlen(trim($testValue)) > 0) {
            return false;
        }
        
        $words = preg_split('/\s+/', $valueNormalized);
    
        foreach ($words as $word) {
            $word = trim($word);
            if (mb_strlen($word) > 2 && !in_array($word, $validWords)) {
                $isPartOfPhrase = false;
                foreach ($this->getValidPhrases() as $phrase) {
                    if (mb_strpos($phrase, $word, 0, 'UTF-8') !== false) {
                        $isPartOfPhrase = true;
                        break;
                    }
                }
                if (!$isPartOfPhrase) {
                    return false;
                }
            }
        }
        
        return true;
    }

    public function index(Request $request)
    {
        $query = Role::query();
        
        if ($request->filled('search_id')) {
            $query->where('roleid', $request->search_id);
        }
        
        if ($request->filled('search_name')) {
            $query->where('rolename', 'like', '%' . $request->search_name . '%');
        }
        
        if ($request->filled('search_description')) {
            $query->where('roledescription', 'like', '%' . $request->search_description . '%');
        }
        
        $roles = $query->orderBy('roleid', 'asc')->get();
        
        return view('admin.roles.index', compact('roles'));
    }

    private function getValidationRules($ignoreId = null): array
    {
        $rules = [
            'rolename' => [
                'required',
                'string',
                'max:80',
                'min:2',
                'regex:/^[\p{L}\p{N}\s,.\-()]+$/u',
                function ($attribute, $value, $fail) {
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('Tên vai trò không được có nhiều khoảng trắng liên tiếp');
                    }
                },
                function ($attribute, $value, $fail) {
                    if (!$this->validateRoleKeywords($value)) {
                        $fail('Tên vai trò phải chứa ít nhất một cụm từ hợp lệ (ví dụ: quản trị viên, kế toán, giáo viên, hiệu trưởng, nhân sự, ...)');
                    }
                },
            ],
            'roledescription' => 'nullable|string',
        ];

        if ($ignoreId) {
            $rules['rolename'][] = Rule::unique('role', 'rolename')->ignore($ignoreId, 'roleid');
        } else {
            $rules['rolename'][] = Rule::unique('role', 'rolename');
        }

        return $rules;
    }

    private function getValidationMessages(): array
    {
        return [
            'rolename.required' => 'Tên vai trò là bắt buộc',
            'rolename.min' => 'Tên vai trò phải có ít nhất 2 ký tự',
            'rolename.max' => 'Tên vai trò không được vượt quá 80 ký tự',
            'rolename.regex' => 'Tên vai trò chỉ được chứa chữ cái, số, khoảng trắng và các ký tự: , . - ( )',
            'rolename.unique' => 'Tên vai trò đã tồn tại',
            'rolename.0' => 'Tên vai trò không được có nhiều khoảng trắng liên tiếp',
            'rolename.1' => 'Tên vai trò phải chứa ít nhất một cụm từ hợp lệ (ví dụ: quản trị viên, kế toán, giáo viên, hiệu trưởng, nhân sự, ...)',
        ];
    }

    public function store(Request $request)
    {
        $request->merge([
            'rolename' => trim($request->rolename),
            'roledescription' => $request->roledescription ? trim($request->roledescription) : null,
        ]);

        $validator = Validator::make($request->all(), $this->getValidationRules(), $this->getValidationMessages());

        $validated = $validator->validate();

        Role::create($validated);

        return redirect()->route('admin.role.index')
            ->with('success', 'Thêm vai trò thành công!');
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->merge([
            'rolename' => trim($request->rolename),
            'roledescription' => $request->roledescription ? trim($request->roledescription) : null,
        ]);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($role->roleid),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        $role->update($validated);

        return redirect()->route('admin.role.index')
            ->with('success', 'Cập nhật vai trò thành công!');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        $role->delete();

        return redirect()->route('admin.role.index')
            ->with('success', 'Xóa vai trò thành công!');
    }
}

