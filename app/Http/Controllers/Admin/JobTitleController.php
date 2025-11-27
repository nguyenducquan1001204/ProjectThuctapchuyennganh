<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobTitle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class JobTitleController extends Controller
{
    /**
     * Danh sách cụm từ hợp lệ cho tên chức danh
     */
    private function getValidPhrases(): array
    {
        return [
            // Ban giám hiệu
            'hiệu trưởng', 'phó hiệu trưởng', 'ban giám hiệu',
            // Tổ chuyên môn
            'tổ trưởng', 'phó tổ trưởng', 'tổ chuyên môn',
            // Môn học
            'giáo viên toán', 'giáo viên lý', 'giáo viên hóa', 'giáo viên sinh',
            'giáo viên sử', 'giáo viên địa', 'giáo viên văn', 'giáo viên anh',
            'giáo viên công nghệ', 'giáo viên tin học', 'giáo viên gdcd',
            'giáo viên thể dục', 'giáo viên nhạc', 'giáo viên họa',
            // Giáo viên
            'giáo viên', 'giảng viên', 'chủ nhiệm',
            // Chức danh quản lý
            'quản lý', 'phụ trách',
            // Công tác học sinh
            'công tác học sinh', 'tổng phụ trách đội', 'tư vấn tâm lý',
            // Văn phòng
            'văn phòng', 'hành chính', 'kế toán', 'thủ quỹ', 'thiết bị', 'thư viện',
            // Hỗ trợ
            'nhân viên y tế', 'bảo vệ', 'tạp vụ', 'phục vụ', 'kỹ thuật cơ sở',
            // Đoàn thể
            'thanh niên', 'ban đại diện cha mẹ'
        ];
    }

    /**
     * Kiểm tra tên chức danh có chứa ít nhất 1 cụm từ hợp lệ và không có ký tự lạ
     */
    private function validateJobTitleKeywords(string $value): bool
    {
        $valueLower = mb_strtolower(trim($value), 'UTF-8');
        $valueNormalized = preg_replace('/\s+/', ' ', $valueLower); // Chuẩn hóa khoảng trắng
        
        // Kiểm tra xem có chứa cụm từ hợp lệ không
        $hasValidPhrase = false;
        foreach ($this->getValidPhrases() as $phrase) {
            if (mb_strpos($valueNormalized, $phrase, 0, 'UTF-8') !== false) {
                $hasValidPhrase = true;
                break;
            }
        }
        
        if (!$hasValidPhrase) {
            return false;
        }
        
        // Kiểm tra xem tên chức danh có chứa ký tự/chuỗi không hợp lệ không
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
        $validWords = [];
        
        // Lấy tất cả từ trong các cụm từ hợp lệ
        foreach ($this->getValidPhrases() as $phrase) {
            $phraseWords = preg_split('/\s+/', $phrase);
            $validWords = array_merge($validWords, $phraseWords);
        }
        $validWords = array_unique($validWords);
        
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
     * Hiển thị danh sách chức danh
     */
    public function index(Request $request)
    {
        $query = JobTitle::query();
        
        // Tìm kiếm theo ID
        if ($request->filled('search_id')) {
            $query->where('jobtitleid', $request->search_id);
        }
        
        // Tìm kiếm theo tên
        if ($request->filled('search_name')) {
            $query->where('jobtitlename', 'like', '%' . $request->search_name . '%');
        }
        
        // Tìm kiếm theo mô tả
        if ($request->filled('search_description')) {
            $query->where('jobtitledescription', 'like', '%' . $request->search_description . '%');
        }
        
        $jobTitles = $query->orderBy('jobtitleid', 'asc')->get();
        
        return view('admin.jobtitles.index', compact('jobTitles'));
    }

    /**
     * Validation rules cho tên chức danh
     */
    private function getValidationRules($ignoreId = null): array
    {
        $rules = [
            'jobtitlename' => [
                'required',
                'string',
                'min:3',
                'max:150',
                'regex:/^[\p{L}\p{N}\s,.\-()]+$/u',
                function ($attribute, $value, $fail) {
                    // Kiểm tra không được có nhiều khoảng trắng liên tiếp
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('Tên chức danh không được có nhiều khoảng trắng liên tiếp');
                    }
                },
                function ($attribute, $value, $fail) {
                    if (!$this->validateJobTitleKeywords($value)) {
                        $fail('Tên chức danh phải chứa ít nhất một cụm từ hợp lệ (ví dụ: hiệu trưởng, giáo viên, phó hiệu trưởng, ...)');
                    }
                },
            ],
            'jobtitledescription' => 'nullable|string|max:65535',
        ];

        // Thêm unique rule
        if ($ignoreId) {
            $rules['jobtitlename'][] = Rule::unique('jobtitle', 'jobtitlename')->ignore($ignoreId, 'jobtitleid');
        } else {
            $rules['jobtitlename'][] = Rule::unique('jobtitle', 'jobtitlename');
        }

        return $rules;
    }

    /**
     * Validation messages
     */
    private function getValidationMessages(): array
    {
        return [
            'jobtitlename.required' => 'Tên chức danh là bắt buộc',
            'jobtitlename.min' => 'Tên chức danh phải có ít nhất 3 ký tự',
            'jobtitlename.max' => 'Tên chức danh không được vượt quá 150 ký tự',
            'jobtitlename.regex' => 'Tên chức danh chỉ được chứa chữ cái, số, khoảng trắng và các ký tự: , . - ( )',
            'jobtitlename.unique' => 'Tên chức danh đã tồn tại',
        ];
    }

    /**
     * Lưu chức danh mới
     */
    public function store(Request $request)
    {
        $request->merge(['jobtitlename' => trim($request->jobtitlename)]);

        $validator = Validator::make($request->all(), $this->getValidationRules(), $this->getValidationMessages());

        $validator->after(function ($validator) use ($request) {
            if (!$this->validateJobTitleKeywords($request->jobtitlename)) {
                $validator->errors()->add(
                    'jobtitlename',
                    'Tên chức danh phải chứa ít nhất một cụm từ hợp lệ (ví dụ: hiệu trưởng, giáo viên, phó hiệu trưởng, ...)'
                );
            }
        });

        $validated = $validator->validate();

        JobTitle::create($validated);

        return redirect()->route('admin.jobtitle.index')
            ->with('success', 'Thêm chức danh thành công!');
    }

    /**
     * Cập nhật chức danh
     */
    public function update(Request $request, $id)
    {
        $jobTitle = JobTitle::findOrFail($id);

        $request->merge(['jobtitlename' => trim($request->jobtitlename)]);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($jobTitle->jobtitleid),
            $this->getValidationMessages()
        );

        $validator->after(function ($validator) use ($request) {
            if (!$this->validateJobTitleKeywords($request->jobtitlename)) {
                $validator->errors()->add(
                    'jobtitlename',
                    'Tên chức danh phải chứa ít nhất một cụm từ hợp lệ (ví dụ: hiệu trưởng, giáo viên, phó hiệu trưởng, ...)'
                );
            }
        });

        $validated = $validator->validate();

        $jobTitle->update($validated);

        return redirect()->route('admin.jobtitle.index')
            ->with('success', 'Cập nhật chức danh thành công!');
    }

    /**
     * Xóa chức danh
     */
    public function destroy($id)
    {
        $jobTitle = JobTitle::findOrFail($id);
        
        // TODO: Kiểm tra quan hệ với bảng teacher trước khi xóa
        
        $jobTitle->delete();

        return redirect()->route('admin.jobtitle.index')
            ->with('success', 'Xóa chức danh thành công!');
    }
}

