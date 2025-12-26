<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetSpendingUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class BudgetSpendingUnitController extends Controller
{
    public function index(Request $request)
    {
        $query = BudgetSpendingUnit::query();
        
        if ($request->filled('search_id')) {
            $query->where('unitid', $request->search_id);
        }
        
        if ($request->filled('search_name')) {
            $query->where('unitname', 'like', '%' . $request->search_name . '%');
        }
        
        if ($request->filled('search_address')) {
            $query->where('address', 'like', '%' . $request->search_address . '%');
        }
        
        if ($request->filled('search_taxnumber')) {
            $query->where('taxnumber', 'like', '%' . $request->search_taxnumber . '%');
        }
        
        $units = $query->orderBy('unitid', 'asc')->get();
        
        return view('admin.budgetspendingunits.index', compact('units'));
    }

    private function getValidationRules($ignoreId = null): array
    {
        $rules = [
            'unitname' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[\p{L}\p{N}\s,.\-()]+$/u',
                function ($attribute, $value, $fail) {
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
                'regex:/^[\p{L}\p{N}\s,.\-()]+$/u',
                function ($attribute, $value, $fail) {
                    if ($value) {
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

        if ($ignoreId) {
            $rules['unitname'][] = Rule::unique('budgetspendingunit', 'unitname')->ignore($ignoreId, 'unitid');
        } else {
            $rules['unitname'][] = Rule::unique('budgetspendingunit', 'unitname');
        }

        if ($ignoreId) {
            $rules['taxnumber'][] = Rule::unique('budgetspendingunit', 'taxnumber')->ignore($ignoreId, 'unitid');
        } else {
            $rules['taxnumber'][] = Rule::unique('budgetspendingunit', 'taxnumber');
        }

        return $rules;
    }

    private function isValidText($text): bool
    {
        if (empty($text)) {
            return true;
        }

        $cleanedText = preg_replace('/[^a-zA-ZàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ\s]/u', '', $text);
        
        if (empty($cleanedText)) {
            return false;
        }

        $words = preg_split('/\s+/', trim($cleanedText));
        
        $validWordsCount = 0;
        
        foreach ($words as $word) {
            $word = trim($word);
            if (empty($word)) {
                continue;
            }

            if (mb_strlen($word, 'UTF-8') < 2 || is_numeric($word)) {
                continue;
            }

            $wordLength = mb_strlen($word, 'UTF-8');

            $isAbbreviation = (
                mb_strtoupper($word, 'UTF-8') === $word && 
                !preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/iu', $word) &&
                preg_match('/[A-Z]{2,}/u', $word) &&
                $wordLength <= 10
            );

            if ($isAbbreviation) {
                if ($wordLength >= 4 && $wordLength % 2 == 0) {
                    $half = mb_substr($word, 0, $wordLength / 2, 'UTF-8');
                    $secondHalf = mb_substr($word, $wordLength / 2, null, 'UTF-8');
                    if ($half === $secondHalf && $half !== mb_substr($word, 0, 1, 'UTF-8') . mb_substr($word, 0, 1, 'UTF-8')) {
                        if (mb_substr($half, 0, 1, 'UTF-8') !== mb_substr($half, 1, 1, 'UTF-8')) {
                        }
                    }
                }
                $validWordsCount++;
                continue;
            }

            if (preg_match('/(.)\1{2,}/u', $word)) {
                return false;
            }

            if ($wordLength >= 6) {
                for ($i = 2; $i <= floor($wordLength / 2); $i++) {
                    $pattern = mb_substr($word, 0, $i, 'UTF-8');
                    $rest = mb_substr($word, $i, null, 'UTF-8');
                    if (mb_strpos($rest, $pattern, 0, 'UTF-8') !== false) {
                        $repeated = str_repeat($pattern, ceil($wordLength / $i));
                        if (mb_substr($repeated, 0, $wordLength, 'UTF-8') === $word) {
                            return false;
                        }
                    }
                }
            }

            $vowels = '/[aeiouyàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđAEIOUYÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ]/iu';
            if (!preg_match($vowels, $word)) {
                if ($wordLength <= 3) {
                    $validWordsCount++;
                    continue;
                }
            }

            if (preg_match('/[bcdfghjklmnpqrstvwxzBCDFGHJKLMNPQRSTVWXZ]{5,}/u', $word)) {
                return false;
            }

            $validWordsCount++;
        }

        return $validWordsCount > 0;
    }

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

    public function destroy($id)
    {
        $unit = BudgetSpendingUnit::findOrFail($id);
        
        $unit->delete();

        return redirect()->route('admin.budgetspendingunit.index')
            ->with('success', 'Xóa đơn vị thành công!');
    }
}

