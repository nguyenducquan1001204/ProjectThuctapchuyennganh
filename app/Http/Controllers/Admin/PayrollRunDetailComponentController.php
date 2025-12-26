<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollRunDetailComponent;
use App\Models\PayrollRunDetail;
use App\Models\PayrollComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PayrollRunDetailComponentController extends Controller
{
    public function index(Request $request)
    {
        $query = PayrollRunDetailComponent::with(['detail.teacher', 'component']);

        if ($request->filled('search_id')) {
            $query->where('detailcomponentid', $request->search_id);
        }

        if ($request->filled('search_detailid')) {
            $query->where('detailid', $request->search_detailid);
        }

        if ($request->filled('search_componentid')) {
            $query->where('componentid', $request->search_componentid);
        }

        if ($request->filled('search_calculatedamount_from')) {
            $query->where('calculatedamount', '>=', $request->search_calculatedamount_from);
        }
        if ($request->filled('search_calculatedamount_to')) {
            $query->where('calculatedamount', '<=', $request->search_calculatedamount_to);
        }

        $payrollRunDetailComponents = $query->orderBy('detailcomponentid', 'desc')->get();

        $allPayrollRunDetails = PayrollRunDetail::with(['payrollRun.unit', 'teacher'])
            ->orderBy('detailid', 'desc')
            ->get();

        $allComponents = PayrollComponent::orderBy('componentname', 'asc')->get();

        return view('admin.payrollrundetailcomponents.index', compact('payrollRunDetailComponents', 'allPayrollRunDetails', 'allComponents'));
    }

    private function getValidationRules(): array
    {
        return [
            'detailid' => [
                'required',
                'integer',
                'exists:payrollrundetail,detailid',
            ],
            'componentid' => [
                'required',
                'integer',
                'exists:payrollcomponent,componentid',
            ],
            'appliedcoefficient' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'appliedpercentage' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'calculatedamount' => [
                'required',
                'numeric',
            ],
            'note' => 'nullable|string|max:65535',
        ];
    }

    private function getValidationMessages(): array
    {
        return [
            'detailid.required' => 'Chi tiết bảng lương là bắt buộc',
            'detailid.integer' => 'Chi tiết bảng lương không hợp lệ',
            'detailid.exists' => 'Chi tiết bảng lương không tồn tại',
            'componentid.required' => 'Thành phần lương là bắt buộc',
            'componentid.integer' => 'Thành phần lương không hợp lệ',
            'componentid.exists' => 'Thành phần lương không tồn tại',
            'appliedcoefficient.numeric' => 'Hệ số đã sử dụng phải là số',
            'appliedcoefficient.min' => 'Hệ số đã sử dụng phải lớn hơn hoặc bằng 0',
            'appliedpercentage.numeric' => 'Tỷ lệ phần trăm đã sử dụng phải là số',
            'appliedpercentage.min' => 'Tỷ lệ phần trăm đã sử dụng phải lớn hơn hoặc bằng 0',
            'appliedpercentage.max' => 'Tỷ lệ phần trăm đã sử dụng không được vượt quá 100',
            'calculatedamount.required' => 'Số tiền đã tính toán là bắt buộc',
            'calculatedamount.numeric' => 'Số tiền đã tính toán phải là số',
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

        $validated['detailid'] = (int)$validated['detailid'];
        $validated['componentid'] = (int)$validated['componentid'];
        $validated['calculatedamount'] = (float)$validated['calculatedamount'];
        
        if (isset($validated['appliedcoefficient'])) {
            $validated['appliedcoefficient'] = (float)$validated['appliedcoefficient'];
        }
        if (isset($validated['appliedpercentage'])) {
            $validated['appliedpercentage'] = (float)$validated['appliedpercentage'];
        }

        PayrollRunDetailComponent::create($validated);

        return redirect()->route('admin.payrollrundetailcomponent.index')
            ->with('success', 'Thêm chi tiết thành phần lương thành công!');
    }

    public function update(Request $request, $id)
    {
        $payrollRunDetailComponent = PayrollRunDetailComponent::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $validated = $validator->validate();

        $validated['detailid'] = (int)$validated['detailid'];
        $validated['componentid'] = (int)$validated['componentid'];
        $validated['calculatedamount'] = (float)$validated['calculatedamount'];
        
        if (isset($validated['appliedcoefficient'])) {
            $validated['appliedcoefficient'] = (float)$validated['appliedcoefficient'];
        }
        if (isset($validated['appliedpercentage'])) {
            $validated['appliedpercentage'] = (float)$validated['appliedpercentage'];
        }

        $payrollRunDetailComponent->update($validated);

        return redirect()->route('admin.payrollrundetailcomponent.index')
            ->with('success', 'Cập nhật chi tiết thành phần lương thành công!');
    }

    public function destroy($id)
    {
        $payrollRunDetailComponent = PayrollRunDetailComponent::findOrFail($id);

        $payrollRunDetailComponent->delete();

        return redirect()->route('admin.payrollrundetailcomponent.index')
            ->with('success', 'Xóa chi tiết thành phần lương thành công!');
    }
}

