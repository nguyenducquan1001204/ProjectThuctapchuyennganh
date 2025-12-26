<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobTitle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class JobTitleController extends Controller
{
    private function getValidPhrases(): array
    {
        return [
            'hiệu trưởng', 'phó hiệu trưởng', 'ban giám hiệu',
            'tổ trưởng', 'phó tổ trưởng', 'tổ chuyên môn',
            'giáo viên toán', 'giáo viên lý', 'giáo viên hóa', 'giáo viên sinh',
            'giáo viên sử', 'giáo viên địa', 'giáo viên văn', 'giáo viên anh',
            'giáo viên công nghệ', 'giáo viên tin học', 'giáo viên gdcd',
            'giáo viên thể dục', 'giáo viên nhạc', 'giáo viên họa',
            'giáo viên', 'giảng viên', 'chủ nhiệm',
            'quản lý', 'phụ trách',
            'công tác học sinh', 'tổng phụ trách đội', 'tư vấn tâm lý',
            'văn phòng', 'hành chính', 'kế toán', 'thủ quỹ', 'thiết bị', 'thư viện',
            'nhân viên y tế', 'bảo vệ', 'tạp vụ', 'phục vụ', 'kỹ thuật cơ sở',
            'thanh niên', 'ban đại diện cha mẹ'
        ];
    }

    private function validateJobTitleKeywords(string $value): bool
    {
        $valueLower = mb_strtolower(trim($value), 'UTF-8');
        $valueNormalized = preg_replace('/\s+/', ' ', $valueLower);
        
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
        
        $testValue = $valueNormalized;
        
        foreach ($this->getValidPhrases() as $phrase) {
            $testValue = str_replace($phrase, '', $testValue);
        }
        
        $testValue = preg_replace('/[\p{L}\p{N}\s,.\-()]/u', '', $testValue);
        
        if (mb_strlen(trim($testValue)) > 0) {
            return false;
        }
        
        $words = preg_split('/\s+/', $valueNormalized);
        $validWords = [];
        
        foreach ($this->getValidPhrases() as $phrase) {
            $phraseWords = preg_split('/\s+/', $phrase);
            $validWords = array_merge($validWords, $phraseWords);
        }
        $validWords = array_unique($validWords);
        
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
        $query = JobTitle::query();
        
        if ($request->filled('search_id')) {
            $query->where('jobtitleid', $request->search_id);
        }
        
        if ($request->filled('search_name')) {
            $query->where('jobtitlename', 'like', '%' . $request->search_name . '%');
        }
        
        if ($request->filled('search_description')) {
            $query->where('jobtitledescription', 'like', '%' . $request->search_description . '%');
        }
        
        $jobTitles = $query->orderBy('jobtitleid', 'asc')->get();
        
        return view('admin.jobtitles.index', compact('jobTitles'));
    }

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

        if ($ignoreId) {
            $rules['jobtitlename'][] = Rule::unique('jobtitle', 'jobtitlename')->ignore($ignoreId, 'jobtitleid');
        } else {
            $rules['jobtitlename'][] = Rule::unique('jobtitle', 'jobtitlename');
        }

        return $rules;
    }

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

    public function destroy($id)
    {
        $jobTitle = JobTitle::findOrFail($id);

        $jobTitle->delete();

        return redirect()->route('admin.jobtitle.index')
            ->with('success', 'Xóa chức danh thành công!');
    }
}

