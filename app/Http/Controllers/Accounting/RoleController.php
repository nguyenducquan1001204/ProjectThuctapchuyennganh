<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Danh sách cụm từ hợp lệ cho tên vai trò
     */
    private function getValidPhrases(): array
    {
        return [
            // Quản trị
            'quản trị viên', 'quản trị', 'admin', 'administrator',
            // Kế toán
            'kế toán', 'accountant',
            // Nhân sự
            'nhân sự', 'human resources', 'hr',
            // Giáo viên
            'giáo viên', 'teacher',
            // Ban giám hiệu
            'hiệu trưởng', 'principal', 'headmaster',
            'phó hiệu trưởng', 'vice principal', 'deputy principal',
            // Quản lý
            'quản lý', 'manager',
            'quản lý hệ thống', 'system manager',
            // Nhân viên
            'nhân viên', 'employee', 'staff',
            'nhân viên hành chính', 'administrative staff',
            'nhân viên văn phòng', 'office staff',
            // Kiểm tra
            'kiểm tra viên', 'auditor',
        ];
    }

    /**
     * Kiểm tra tên vai trò có chứa ít nhất 1 cụm từ hợp lệ và không có ký tự lạ
     */
    private function validateRoleKeywords(string $value): bool
    {
        $valueLower = mb_strtolower(trim($value), 'UTF-8');
        $valueNormalized = preg_replace('/\s+/', ' ', $valueLower); // Chuẩn hóa khoảng trắng
        
        // Kiểm tra xem có chứa cụm từ hợp lệ không
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
        
        // Kiểm tra từ lặp lại (ví dụ: "Giáo viên viên", "Kế toán toán")
        $words = preg_split('/\s+/', $valueNormalized);
        $previousWord = '';
        foreach ($words as $word) {
            $word = trim($word);
            // Nếu từ hiện tại giống từ trước đó, có thể là lặp lại không hợp lệ
            if ($previousWord && $word === $previousWord && mb_strlen($word) > 2) {
                return false; // Từ lặp lại
            }
            $previousWord = $word;
        }
        
        // Kiểm tra xem có từ nào là phần cuối của cụm từ hợp lệ nhưng bị lặp lại không
        // Ví dụ: "giáo viên viên" - từ "viên" là phần cuối của "giáo viên" nhưng bị lặp
        $words = preg_split('/\s+/', $valueNormalized);
        $validWords = [];
        
        // Lấy tất cả từ trong các cụm từ hợp lệ
        foreach ($this->getValidPhrases() as $phrase) {
            $phraseWords = preg_split('/\s+/', $phrase);
            $validWords = array_merge($validWords, $phraseWords);
        }
        $validWords = array_unique($validWords);
        
        // Kiểm tra từ cuối của mỗi cụm từ hợp lệ
        $lastWordsOfPhrases = [];
        foreach ($this->getValidPhrases() as $phrase) {
            $phraseWords = preg_split('/\s+/', $phrase);
            if (count($phraseWords) > 0) {
                $lastWord = end($phraseWords);
                $lastWordsOfPhrases[] = $lastWord;
            }
        }
        $lastWordsOfPhrases = array_unique($lastWordsOfPhrases);
        
        // Kiểm tra xem có từ nào trong chuỗi nằm trong lastWordsOfPhrases nhưng không phải là phần của cụm từ hợp lệ đã match
        foreach ($words as $index => $word) {
            $word = trim($word);
            if (in_array($word, $lastWordsOfPhrases) && mb_strlen($word) > 2) {
                // Kiểm tra xem từ này có phải là phần cuối của cụm từ hợp lệ đã match không
                // Nếu có, kiểm tra xem có từ nào sau đó giống với từ này không (lặp lại)
                $matchedPhraseWords = preg_split('/\s+/', $matchedPhrase);
                $matchedLastWord = end($matchedPhraseWords);
                
                if ($word === $matchedLastWord) {
                    // Kiểm tra xem từ này có xuất hiện sau cụm từ hợp lệ không (có thể là lặp lại)
                    // Tìm vị trí của cụm từ hợp lệ trong chuỗi
                    $phrasePos = mb_strpos($valueNormalized, $matchedPhrase, 0, 'UTF-8');
                    $phraseEndPos = $phrasePos + mb_strlen($matchedPhrase, 'UTF-8');
                    $afterPhrase = mb_substr($valueNormalized, $phraseEndPos, null, 'UTF-8');
                    
                    // Kiểm tra xem phần sau cụm từ có chứa từ cuối của cụm từ không (lặp lại)
                    if (trim($afterPhrase) && preg_match('/^\s+' . preg_quote($matchedLastWord, '/') . '\b/u', $afterPhrase)) {
                        return false; // Từ lặp lại sau cụm từ hợp lệ
                    }
                }
            }
        }
        
        // Kiểm tra xem tên vai trò có chứa ký tự/chuỗi không hợp lệ không
        // Loại bỏ các cụm từ hợp lệ và các ký tự hợp lệ, xem còn gì không
        $testValue = $valueNormalized;
        
        // Loại bỏ tất cả cụm từ hợp lệ
        foreach ($this->getValidPhrases() as $phrase) {
            $testValue = str_replace($phrase, '', $testValue);
        }
        
        // Loại bỏ các ký tự hợp lệ: chữ cái, số, khoảng trắng, dấu phẩy, chấm, gạch ngang, ngoặc
        $testValue = preg_replace('/[\p{L}\p{N}\s,.\-()]/u', '', $testValue);
        
        // Nếu còn ký tự nào sau khi loại bỏ, nghĩa là có ký tự lạ
        if (mb_strlen(trim($testValue)) > 0) {
            return false;
        }
        
        // Kiểm tra xem có từ đơn lẻ không hợp lệ không (từ không nằm trong cụm từ hợp lệ)
        $words = preg_split('/\s+/', $valueNormalized);
        
        // Kiểm tra xem có từ nào không nằm trong danh sách từ hợp lệ không
        // Nhưng cho phép các từ ngắn (1-2 ký tự) vì có thể là từ viết tắt hoặc số
        foreach ($words as $word) {
            $word = trim($word);
            if (mb_strlen($word) > 2 && !in_array($word, $validWords)) {
                // Kiểm tra xem từ này có nằm trong cụm từ hợp lệ nào không (substring)
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

    /**
     * Hiển thị danh sách vai trò
     */
    public function index(Request $request)
    {
        $query = Role::query();
        
        // Tìm kiếm theo ID
        if ($request->filled('search_id')) {
            $query->where('roleid', $request->search_id);
        }
        
        // Tìm kiếm theo tên
        if ($request->filled('search_name')) {
            $query->where('rolename', 'like', '%' . $request->search_name . '%');
        }
        
        // Tìm kiếm theo mô tả
        if ($request->filled('search_description')) {
            $query->where('roledescription', 'like', '%' . $request->search_description . '%');
        }
        
        $roles = $query->orderBy('roleid', 'asc')->get();
        
        return view('accounting.roles.index', compact('roles'));
    }

    /**
     * Validation rules cho vai trò
     */
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
                    // Kiểm tra không được có nhiều khoảng trắng liên tiếp
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

        // Thêm unique rule cho rolename
        if ($ignoreId) {
            $rules['rolename'][] = Rule::unique('role', 'rolename')->ignore($ignoreId, 'roleid');
        } else {
            $rules['rolename'][] = Rule::unique('role', 'rolename');
        }

        return $rules;
    }

    /**
     * Validation messages
     */
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

    /**
     * Lưu vai trò mới
     */
    public function store(Request $request)
    {
        $request->merge([
            'rolename' => trim($request->rolename),
            'roledescription' => $request->roledescription ? trim($request->roledescription) : null,
        ]);

        $validator = Validator::make($request->all(), $this->getValidationRules(), $this->getValidationMessages());

        $validated = $validator->validate();

        Role::create($validated);

        return redirect()->route('accounting.role.index')
            ->with('success', 'Thêm vai trò thành công!');
    }

    /**
     * Cập nhật vai trò
     */
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

        return redirect()->route('accounting.role.index')
            ->with('success', 'Cập nhật vai trò thành công!');
    }

    /**
     * Xóa vai trò
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // TODO: Kiểm tra quan hệ với các bảng khác trước khi xóa
        
        $role->delete();

        return redirect()->route('accounting.role.index')
            ->with('success', 'Xóa vai trò thành công!');
    }
}

