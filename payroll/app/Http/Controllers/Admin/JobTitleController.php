<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobTitle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobTitleController extends Controller
{
    public function index()
    {
        $query = JobTitle::orderBy('jobtitleid');

        if ($search = request('search')) {
            $query->where('jobtitlename', 'like', "%{$search}%");
        }

        $jobtitles = $query->get();

        return view('admin.jobtitle', compact('jobtitles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'jobtitlename' => ['required', 'string', 'max:150', 'unique:jobtitle,jobtitlename'],
            'jobtitledescription' => ['nullable', 'string'],
        ]);

        JobTitle::create($data);

        return redirect()->route('admin.roles.index')->with('success', 'Thêm chức danh thành công.');
    }

    public function update(Request $request, JobTitle $jobtitle): RedirectResponse
    {
        $data = $request->validate([
            'jobtitlename' => [
                'required',
                'string',
                'max:150',
                Rule::unique('jobtitle', 'jobtitlename')->ignore($jobtitle->jobtitleid, 'jobtitleid'),
            ],
            'jobtitledescription' => ['nullable', 'string'],
        ]);

        $jobtitle->update($data);

        return redirect()->route('admin.roles.index')->with('success', 'Cập nhật chức danh thành công.');
    }

    public function destroy(JobTitle $jobtitle): RedirectResponse
    {
        $jobtitle->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Đã xóa chức danh.');
    }
}

