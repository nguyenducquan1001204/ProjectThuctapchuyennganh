<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetSpendingUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class BudgetSpendingUnitController extends Controller
{
    /**
     * Hiển thị danh sách đơn vị
     */
    public function index(Request $request)
    {
        $query = BudgetSpendingUnit::query();
        
        // Tìm kiếm theo ID
        if ($request->filled('search_id')) {
            $query->where('unitid', $request->search_id);
        }
        
        // Tìm kiếm theo tên
        if ($request->filled('search_name')) {
            $query->where('unitname', 'like', '%' . $request->search_name . '%');
        }
        
        // Tìm kiếm theo địa chỉ
        if ($request->filled('search_address')) {
            $query->where('address', 'like', '%' . $request->search_address . '%');
        }
        
        // Tìm kiếm theo mã số thuế
        if ($request->filled('search_taxnumber')) {
            $query->where('taxnumber', 'like', '%' . $request->search_taxnumber . '%');
        }
        
        $units = $query->orderBy('unitid', 'asc')->get();
        
        return view('admin.budgetspendingunits.index', compact('units'));
    }

    /**
     * Validation rules cho đơn vị
     */
    private function getValidationRules($ignoreId = null): array
    {
        $rules = [
            'unitname' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[\p{L}\p{N}\s,.\-()]+$/u', // Chỉ cho phép chữ cái, số, khoảng trắng và các ký tự: , . - ( )
                function ($attribute, $value, $fail) {
                    // Kiểm tra không được có nhiều khoảng trắng liên tiếp
                    if (preg_match('/\s{2,}/', $value)) {
                        $fail('Tên đơn vị không được có nhiều khoảng trắng liên tiếp');
                    }
                },
                function ($attribute, $value, $fail) {
                    if (!$this->isValidText($value)) {
                        $fail('Tên đơn vị phải có ý nghĩa, không được chứa các chuỗi vô nghĩa.');
                    }
                },
            ],
            'address' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}\s,.\-()]+$/u', // Chỉ cho phép chữ cái, số, khoảng trắng và các ký tự: , . - ( )
                function ($attribute, $value, $fail) {
                    if ($value) {
                        // Kiểm tra không được có nhiều khoảng trắng liên tiếp
                        if (preg_match('/\s{2,}/', $value)) {
                            $fail('Địa chỉ không được có nhiều khoảng trắng liên tiếp');
                        }
                    }
                },
                function ($attribute, $value, $fail) {
                    if ($value && !$this->isValidText($value)) {
                        $fail('Địa chỉ phải có ý nghĩa, không được chứa các chuỗi vô nghĩa.');
                    }
                },
            ],
            'taxnumber' => [
                'required',
                'string',
                'size:10',
                'regex:/^\d{10}$/',
            ],
            'note' => 'nullable|string',
        ];

        // Thêm unique rule cho unitname
        if ($ignoreId) {
            $rules['unitname'][] = Rule::unique('budgetspendingunit', 'unitname')->ignore($ignoreId, 'unitid');
        } else {
            $rules['unitname'][] = Rule::unique('budgetspendingunit', 'unitname');
        }

        // Thêm unique rule cho taxnumber
        if ($ignoreId) {
            $rules['taxnumber'][] = Rule::unique('budgetspendingunit', 'taxnumber')->ignore($ignoreId, 'unitid');
        } else {
            $rules['taxnumber'][] = Rule::unique('budgetspendingunit', 'taxnumber');
        }

        return $rules;
    }

    /**
     * Kiểm tra xem chuỗi có ý nghĩa hay không
     */
    private function isValidText($text): bool
    {
        if (empty($text)) {
            return true;
        }

        // Loại bỏ các ký tự đặc biệt và số để chỉ kiểm tra chữ cái
        $cleanedText = preg_replace('/[^a-zA-ZàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ\s]/u', '', $text);
        
        // Nếu chỉ còn số hoặc ký tự đặc biệt, không hợp lệ
        if (empty($cleanedText)) {
            return false;
        }

        // Tách thành các từ
        $words = preg_split('/\s+/', trim($cleanedText));
        
        $validWordsCount = 0;
        
        foreach ($words as $word) {
            $word = trim($word);
            if (empty($word)) {
                continue;
            }

            // Bỏ qua các từ quá ngắn (dưới 2 ký tự) hoặc là số
            if (mb_strlen($word, 'UTF-8') < 2 || is_numeric($word)) {
                continue;
            }

            $wordLength = mb_strlen($word, 'UTF-8');

            // Kiểm tra xem từ có phải là viết tắt không (tất cả chữ hoa, không có dấu, từ 2-10 ký tự)
            $isAbbreviation = (
                mb_strtoupper($word, 'UTF-8') === $word && 
                !preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/iu', $word) &&
                preg_match('/[A-Z]{2,}/u', $word) &&
                $wordLength <= 10
            );

            // Nếu là viết tắt hợp lệ (như THCS, ABC), cho phép
            if ($isAbbreviation) {
                // Kiểm tra xem có phải là mẫu lặp lại vô nghĩa không (như "ABAB", "CDCD")
                if ($wordLength >= 4 && $wordLength % 2 == 0) {
                    $half = mb_substr($word, 0, $wordLength / 2, 'UTF-8');
                    $secondHalf = mb_substr($word, $wordLength / 2, null, 'UTF-8');
                    // Nếu hai nửa giống hệt nhau và không phải là một ký tự lặp lại
                    if ($half === $secondHalf && $half !== mb_substr($word, 0, 1, 'UTF-8') . mb_substr($word, 0, 1, 'UTF-8')) {
                        // Kiểm tra xem có phải là "ABAB" thực sự vô nghĩa không
                        if (mb_substr($half, 0, 1, 'UTF-8') !== mb_substr($half, 1, 1, 'UTF-8')) {
                            // Có thể là "ABAB" - cho phép vì có thể là viết tắt hợp lệ
                        }
                    }
                }
                $validWordsCount++;
                continue;
            }

            // Kiểm tra các ký tự lặp lại liên tiếp (như "fgfg" hoặc "dfdf")
            if (preg_match('/(.)\1{2,}/u', $word)) {
                return false; // Có ký tự lặp lại 3 lần trở lên
            }

            // Kiểm tra các mẫu lặp lại (như "dgfgdfgd" có mẫu "dgf" lặp lại)
            // Chỉ kiểm tra cho từ không phải viết tắt
            if ($wordLength >= 6) {
                // Kiểm tra các chuỗi con có lặp lại không
                for ($i = 2; $i <= floor($wordLength / 2); $i++) {
                    $pattern = mb_substr($word, 0, $i, 'UTF-8');
                    $rest = mb_substr($word, $i, null, 'UTF-8');
                    if (mb_strpos($rest, $pattern, 0, 'UTF-8') !== false) {
                        // Kiểm tra xem có phải là lặp lại hoàn toàn không
                        $repeated = str_repeat($pattern, ceil($wordLength / $i));
                        if (mb_substr($repeated, 0, $wordLength, 'UTF-8') === $word) {
                            return false;
                        }
                    }
                }
            }

            // Kiểm tra xem từ có chứa ít nhất một nguyên âm không (a, e, i, o, u, y và các biến thể có dấu)
            $vowels = '/[aeiouyàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđAEIOUYÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ]/iu';
            if (!preg_match($vowels, $word)) {
                // Không có nguyên âm - có thể là viết tắt hoặc từ đặc biệt
                // Cho phép từ ngắn (2-3 ký tự) không có nguyên âm
                if ($wordLength <= 3) {
                    $validWordsCount++;
                    continue;
                }
                // Từ dài hơn 3 ký tự không có nguyên âm có khả năng là vô nghĩa
                // Nhưng không từ chối ngay, xem xét kỹ hơn
            }

            // Kiểm tra các phụ âm liên tiếp quá nhiều (nhiều hơn 4 cho từ dài)
            // Chỉ kiểm tra cho từ không phải viết tắt
            if (preg_match('/[bcdfghjklmnpqrstvwxzBCDFGHJKLMNPQRSTVWXZ]{5,}/u', $word)) {
                return false;
            }

            $validWordsCount++;
        }

        // Phải có ít nhất một từ hợp lệ
        return $validWordsCount > 0;
    }

    /**
     * Validation messages
     */
    private function getValidationMessages(): array
    {
        return [
            'unitname.required' => 'Tên đơn vị là bắt buộc',
            'unitname.min' => 'Tên đơn vị phải có ít nhất 3 ký tự',
            'unitname.max' => 'Tên đơn vị không được vượt quá 255 ký tự',
            'unitname.regex' => 'Tên đơn vị chỉ được chứa chữ cái, số, khoảng trắng và các ký tự: , . - ( )',
            'unitname.unique' => 'Tên đơn vị đã tồn tại',
            'unitname.0' => 'Tên đơn vị không được có nhiều khoảng trắng liên tiếp',
            'unitname.1' => 'Tên đơn vị phải có ý nghĩa, không được chứa các chuỗi vô nghĩa.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự',
            'address.regex' => 'Địa chỉ chỉ được chứa chữ cái, số, khoảng trắng và các ký tự: , . - ( )',
            'address.0' => 'Địa chỉ phải có ý nghĩa, không được chứa các chuỗi vô nghĩa.',
            'taxnumber.required' => 'Mã số thuế là bắt buộc',
            'taxnumber.size' => 'Mã số thuế phải có đúng 10 chữ số',
            'taxnumber.regex' => 'Mã số thuế phải là số có đúng 10 chữ số',
            'taxnumber.unique' => 'Mã số thuế đã tồn tại',
        ];
    }

    /**
     * Lưu đơn vị mới
     */
    public function store(Request $request)
    {
        $request->merge([
            'unitname' => trim($request->unitname),
            'address' => $request->address ? trim($request->address) : null,
            'taxnumber' => trim($request->taxnumber),
        ]);

        $validator = Validator::make($request->all(), $this->getValidationRules(), $this->getValidationMessages());

        $validated = $validator->validate();

        BudgetSpendingUnit::create($validated);

        return redirect()->route('admin.budgetspendingunit.index')
            ->with('success', 'Thêm đơn vị thành công!');
    }

    /**
     * Cập nhật đơn vị
     */
    public function update(Request $request, $id)
    {
        $unit = BudgetSpendingUnit::findOrFail($id);

        $request->merge([
            'unitname' => trim($request->unitname),
            'address' => $request->address ? trim($request->address) : null,
            'taxnumber' => trim($request->taxnumber),
        ]);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($unit->unitid),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        $unit->update($validated);

        return redirect()->route('admin.budgetspendingunit.index')
            ->with('success', 'Cập nhật đơn vị thành công!');
    }

    /**
     * Xóa đơn vị
     */
    public function destroy($id)
    {
        $unit = BudgetSpendingUnit::findOrFail($id);
        
        // TODO: Kiểm tra quan hệ với các bảng khác trước khi xóa
        
        $unit->delete();

        return redirect()->route('admin.budgetspendingunit.index')
            ->with('success', 'Xóa đơn vị thành công!');
    }
}

