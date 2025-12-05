<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingMenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountingMenuItemController extends Controller
{
    /**
     * Hiển thị danh sách menu items
     */
    public function index(Request $request)
    {
        $query = AccountingMenuItem::query();

        // Tìm kiếm theo ID
        if ($request->filled('search_id')) {
            $query->where('id', $request->search_id);
        }

        // Tìm kiếm theo tiêu đề
        if ($request->filled('search_title')) {
            $query->where('title', 'like', '%' . $request->search_title . '%');
        }

        // Tìm kiếm theo route name
        if ($request->filled('search_routename')) {
            $query->where('routeName', 'like', '%' . $request->search_routename . '%');
        }

        // Lọc theo trạng thái
        if ($request->filled('search_isactive')) {
            $query->where('isActive', $request->search_isactive);
        }

        $menuItems = $query->with('parent')->orderBy('parentId', 'asc')->orderBy('orderIndex', 'asc')->get();
        
        // Lấy danh sách menu items để làm parent options (chỉ menu cha - không có parentId)
        $parentOptions = AccountingMenuItem::whereNull('parentId')
            ->orderBy('orderIndex', 'asc')
            ->get();

        return view('admin.accountingmenuitems.index', compact('menuItems', 'parentOptions'));
    }

    /**
     * Validation rules
     */
    private function getValidationRules($ignoreId = null): array
    {
        $rules = [
            'title' => 'required|string|max:100',
            'parentId' => 'nullable|exists:accountingmenuitems,id',
            'orderIndex' => 'required|integer|min:0',
            'isActive' => 'required|boolean',
            'routeName' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:100',
        ];

        return $rules;
    }

    /**
     * Validation messages
     */
    private function getValidationMessages(): array
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc',
            'title.max' => 'Tiêu đề không được vượt quá 100 ký tự',
            'parentId.exists' => 'Menu cha không tồn tại',
            'orderIndex.required' => 'Thứ tự sắp xếp là bắt buộc',
            'orderIndex.integer' => 'Thứ tự sắp xếp phải là số nguyên',
            'orderIndex.min' => 'Thứ tự sắp xếp phải lớn hơn hoặc bằng 0',
            'isActive.required' => 'Trạng thái là bắt buộc',
            'isActive.boolean' => 'Trạng thái không hợp lệ',
        ];
    }

    /**
     * Lưu menu item mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        // Xử lý parentId: null nếu là chuỗi rỗng
        if (empty($validated['parentId'])) {
            $validated['parentId'] = null;
        }

        // Xử lý isActive
        $validated['isActive'] = filter_var($validated['isActive'], FILTER_VALIDATE_BOOLEAN);

        // Chỉ lưu các trường có trong bảng
        $dataToSave = [
            'title' => $validated['title'],
            'parentId' => $validated['parentId'],
            'orderIndex' => $validated['orderIndex'],
            'isActive' => $validated['isActive'],
            'routeName' => $validated['routeName'] ?? null,
            'icon' => $validated['icon'] ?? null,
        ];

        AccountingMenuItem::create($dataToSave);

        return redirect()->route('admin.accountingmenuitem.index')
            ->with('success', 'Thêm menu kế toán thành công!');
    }

    /**
     * Cập nhật menu item
     */
    public function update(Request $request, $id)
    {
        $menuItem = AccountingMenuItem::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($id),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        // Xử lý parentId: null nếu là chuỗi rỗng, và không được chọn chính nó
        if (empty($validated['parentId'])) {
            $validated['parentId'] = null;
        } else {
            // Kiểm tra không được chọn chính nó làm parent
            if ($validated['parentId'] == $id) {
                return redirect()->back()
                    ->withErrors(['parentId' => 'Menu không thể chọn chính nó làm menu cha'])
                    ->withInput();
            }
        }

        // Xử lý isActive
        $validated['isActive'] = filter_var($validated['isActive'], FILTER_VALIDATE_BOOLEAN);

        // Chỉ cập nhật các trường có trong bảng
        $dataToUpdate = [
            'title' => $validated['title'],
            'parentId' => $validated['parentId'],
            'orderIndex' => $validated['orderIndex'],
            'isActive' => $validated['isActive'],
            'routeName' => $validated['routeName'] ?? null,
            'icon' => $validated['icon'] ?? null,
        ];

        $menuItem->update($dataToUpdate);

        return redirect()->route('admin.accountingmenuitem.index')
            ->with('success', 'Cập nhật menu kế toán thành công!');
    }

    /**
     * Xóa menu item
     */
    public function destroy($id)
    {
        $menuItem = AccountingMenuItem::findOrFail($id);

        // Kiểm tra nếu có menu con thì không cho xóa
        $hasChildren = AccountingMenuItem::where('parentId', $id)->exists();
        if ($hasChildren) {
            return redirect()->route('admin.accountingmenuitem.index')
                ->with('error', 'Không thể xóa menu này vì còn menu con!');
        }

        $menuItem->delete();

        return redirect()->route('admin.accountingmenuitem.index')
            ->with('success', 'Xóa menu kế toán thành công!');
    }
}

