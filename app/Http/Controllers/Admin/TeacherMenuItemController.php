<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherMenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherMenuItemController extends Controller
{
    public function index(Request $request)
    {
        $query = TeacherMenuItem::query();

        if ($request->filled('search_id')) {
            $query->where('id', $request->search_id);
        }

        if ($request->filled('search_title')) {
            $query->where('title', 'like', '%' . $request->search_title . '%');
        }

        if ($request->filled('search_routename')) {
            $query->where('routeName', 'like', '%' . $request->search_routename . '%');
        }

        if ($request->filled('search_isactive')) {
            $query->where('isActive', $request->search_isactive);
        }

        $menuItems = $query->with('parent')->orderBy('parentId', 'asc')->orderBy('orderIndex', 'asc')->get();
        
        $parentOptions = TeacherMenuItem::whereNull('parentId')
            ->orderBy('orderIndex', 'asc')
            ->get();

        return view('admin.teachermenuitems.index', compact('menuItems', 'parentOptions'));
    }

    private function getValidationRules($ignoreId = null): array
    {
        $rules = [
            'title' => 'required|string|max:100',
            'parentId' => 'nullable|exists:teachermenuitems,id',
            'orderIndex' => 'required|integer|min:0',
            'isActive' => 'required|boolean',
            'routeName' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:100',
        ];

        return $rules;
    }

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

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        if (empty($validated['parentId'])) {
            $validated['parentId'] = null;
        }

        $validated['isActive'] = filter_var($validated['isActive'], FILTER_VALIDATE_BOOLEAN);

        $dataToSave = [
            'title' => $validated['title'],
            'parentId' => $validated['parentId'],
            'orderIndex' => $validated['orderIndex'],
            'isActive' => $validated['isActive'],
            'routeName' => $validated['routeName'] ?? null,
            'icon' => $validated['icon'] ?? null,
        ];

        TeacherMenuItem::create($dataToSave);

        return redirect()->route('admin.teachermenuitem.index')
            ->with('success', 'Thêm menu giáo viên thành công!');
    }

    public function update(Request $request, $id)
    {
        $menuItem = TeacherMenuItem::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules($id),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        if (empty($validated['parentId'])) {
            $validated['parentId'] = null;
        } else {
            if ($validated['parentId'] == $id) {
                return redirect()->back()
                    ->withErrors(['parentId' => 'Menu không thể chọn chính nó làm menu cha'])
                    ->withInput();
            }
        }

        $validated['isActive'] = filter_var($validated['isActive'], FILTER_VALIDATE_BOOLEAN);

        $dataToUpdate = [
            'title' => $validated['title'],
            'parentId' => $validated['parentId'],
            'orderIndex' => $validated['orderIndex'],
            'isActive' => $validated['isActive'],
            'routeName' => $validated['routeName'] ?? null,
            'icon' => $validated['icon'] ?? null,
        ];

        $menuItem->update($dataToUpdate);

        return redirect()->route('admin.teachermenuitem.index')
            ->with('success', 'Cập nhật menu giáo viên thành công!');
    }

    public function destroy($id)
    {
        $menuItem = TeacherMenuItem::findOrFail($id);

        $hasChildren = TeacherMenuItem::where('parentId', $id)->exists();
        if ($hasChildren) {
            return redirect()->route('admin.teachermenuitem.index')
                ->with('error', 'Không thể xóa menu này vì còn menu con!');
        }

        $menuItem->delete();

        return redirect()->route('admin.teachermenuitem.index')
            ->with('success', 'Xóa menu giáo viên thành công!');
    }
}

