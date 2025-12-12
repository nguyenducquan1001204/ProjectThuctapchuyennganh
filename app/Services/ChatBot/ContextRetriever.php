<?php

namespace App\Services\ChatBot;

use App\Models\Teacher;
use App\Models\PayrollComponent;
use App\Models\PayrollRun;
use App\Models\BaseSalary;
use App\Models\SalaryIncreaseDecision;
use App\Models\JobTitle;
use App\Models\BudgetSpendingUnit;
use App\Models\SystemUser;
use Illuminate\Support\Facades\DB;

class ContextRetriever
{
    /**
     * Lấy thông tin context từ database dựa trên câu hỏi
     *
     * @param string $question
     * @return array
     */
    public function getContext(string $question): array
    {
        $context = [];
        $questionLower = mb_strtolower($question, 'UTF-8');

        // Cache schema và relationships (không thay đổi thường xuyên)
        $context['database_schema'] = cache()->remember('chatbot_database_schema', 3600, function() {
            return $this->getDatabaseSchema();
        });
        
        $context['database_relationships'] = cache()->remember('chatbot_database_relationships', 3600, function() {
            return $this->getDatabaseRelationships();
        });
        
        // Chỉ lấy statistics nếu cần (khi có từ khóa thống kê)
        if ($this->containsKeywords($questionLower, ['thống kê', 'statistics', 'tổng quan', 'overview', 'có bao nhiêu', 'số lượng'])) {
            $context['database_statistics'] = $this->getDatabaseStatistics();
        }

        // Kiểm tra các từ khóa liên quan đến giáo viên
        if ($this->containsKeywords($questionLower, ['giáo viên', 'teacher', 'giáo viên nào', 'có bao nhiêu giáo viên', 'danh sách giáo viên', 'tất cả giáo viên'])) {
            try {
                $context['teacher_count'] = Teacher::count();
                // Lấy dữ liệu chi tiết nếu có từ khóa danh sách, list, tất cả, chi tiết
                if ($this->containsKeywords($questionLower, ['danh sách', 'list', 'chi tiết', 'thông tin', 'tất cả', 'all'])) {
                    $context['teacher_list'] = Teacher::with(['jobTitle', 'unit'])
                        ->orderBy('teacherid', 'desc')
                        ->limit(50) // Tăng limit để lấy nhiều hơn
                        ->get(['teacherid', 'fullname', 'birthdate', 'gender', 'jobtitleid', 'unitid', 'startdate', 'currentcoefficient', 'status'])
                        ->map(function($teacher) {
                            return [
                                'teacherid' => $teacher->teacherid,
                                'fullname' => $teacher->fullname,
                                'gender' => $teacher->gender,
                                'jobtitle' => $teacher->jobTitle ? $teacher->jobTitle->jobtitlename : null,
                                'unit' => $teacher->unit ? $teacher->unit->unitname : null,
                                'currentcoefficient' => $teacher->currentcoefficient,
                                'status' => $teacher->status,
                            ];
                        })
                        ->toArray();
                }
            } catch (\Exception $e) {
                \Log::error('Error getting teacher data', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Vẫn lấy được count nếu có lỗi
                $context['teacher_count'] = 0;
            }
        }

        // Kiểm tra các từ khóa liên quan đến thành phần lương
        if ($this->containsKeywords($questionLower, ['thành phần lương', 'payroll component', 'component'])) {
            $context['payroll_component_count'] = PayrollComponent::count();
            if ($this->containsKeywords($questionLower, ['danh sách', 'list', 'chi tiết'])) {
                $context['payroll_components'] = PayrollComponent::select('componentid', 'componentname', 'componentgroup', 'calculationmethod')
                    ->limit(10)
                    ->get()
                    ->toArray();
            }
        }

        // Kiểm tra các từ khóa liên quan đến kỳ tính lương
        if ($this->containsKeywords($questionLower, ['kỳ tính lương', 'payroll run', 'bảng lương', 'tính lương'])) {
            $context['payroll_run_count'] = PayrollRun::count();
            $context['payroll_run_recent'] = PayrollRun::with(['unit', 'baseSalary'])
                ->orderBy('payrollrunid', 'desc')
                ->limit(10)
                ->get(['payrollrunid', 'unitid', 'basesalaryid', 'payrollperiod', 'status', 'createdat', 'approvedat'])
                ->toArray();
        }

        // Kiểm tra các từ khóa liên quan đến mức lương cơ bản
        if ($this->containsKeywords($questionLower, ['mức lương cơ bản', 'base salary', 'lương cơ bản'])) {
            $context['base_salary_count'] = BaseSalary::count();
            $context['base_salary_recent'] = BaseSalary::with('unit')
                ->orderBy('basesalaryid', 'desc')
                ->limit(10)
                ->get(['basesalaryid', 'unitid', 'basesalaryamount', 'effectivedate', 'expirationdate'])
                ->toArray();
        }

        // Kiểm tra các từ khóa liên quan đến quyết định nâng lương
        if ($this->containsKeywords($questionLower, ['quyết định nâng lương', 'salary increase', 'nâng lương'])) {
            $context['salary_increase_count'] = SalaryIncreaseDecision::count();
            $context['salary_increase_recent'] = SalaryIncreaseDecision::with('teacher')
                ->orderBy('decisionid', 'desc')
                ->limit(10)
                ->get(['decisionid', 'teacherid', 'decisiondate', 'oldcoefficient', 'newcoefficient', 'applydate'])
                ->toArray();
        }

        // Kiểm tra các từ khóa liên quan đến chức danh
        if ($this->containsKeywords($questionLower, ['chức danh', 'job title', 'jobtitle'])) {
            $context['job_title_count'] = JobTitle::count();
            $context['job_titles'] = JobTitle::select('jobtitleid', 'jobtitlename', 'jobtitledescription')
                ->limit(20)
                ->get()
                ->toArray();
        }

        // Kiểm tra các từ khóa liên quan đến đơn vị
        if ($this->containsKeywords($questionLower, ['đơn vị', 'unit', 'budget spending unit'])) {
            $context['unit_count'] = BudgetSpendingUnit::count();
            $context['units'] = BudgetSpendingUnit::select('unitid', 'unitname', 'unitdescription')
                ->limit(20)
                ->get()
                ->toArray();
        }

        // Kiểm tra các từ khóa liên quan đến người dùng
        if ($this->containsKeywords($questionLower, ['người dùng', 'user', 'system user', 'tài khoản'])) {
            $context['user_count'] = SystemUser::count();
            $context['user_recent'] = SystemUser::with(['role', 'teacher'])
                ->orderBy('userid', 'desc')
                ->limit(10)
                ->get(['userid', 'username', 'email', 'fullname', 'status', 'teacherid', 'roleid'])
                ->toArray();
        }

        return $context;
    }

    /**
     * Lấy thống kê tổng quan về database
     *
     * @return array
     */
    private function getDatabaseStatistics(): array
    {
        return [
            'total_teachers' => Teacher::count(),
            'total_payroll_components' => PayrollComponent::count(),
            'total_payroll_runs' => PayrollRun::count(),
            'total_base_salaries' => BaseSalary::count(),
            'total_salary_increases' => SalaryIncreaseDecision::count(),
            'total_job_titles' => JobTitle::count(),
            'total_units' => BudgetSpendingUnit::count(),
            'total_users' => SystemUser::count(),
        ];
    }

    /**
     * Kiểm tra xem câu hỏi có chứa các từ khóa không
     *
     * @param string $text
     * @param array $keywords
     * @return bool
     */
    private function containsKeywords(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (mb_strpos($text, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Lấy schema của database (cấu trúc các bảng)
     *
     * @return array
     */
    public function getDatabaseSchema(): array
    {
        $tables = [
            'teacher' => [
                'description' => 'Bảng lưu thông tin giáo viên',
                'primary_key' => 'teacherid',
                'columns' => [
                    'teacherid' => 'INTEGER - ID giáo viên (Primary Key)',
                    'fullname' => 'STRING - Họ và tên giáo viên',
                    'birthdate' => 'DATE - Ngày sinh',
                    'gender' => 'STRING - Giới tính',
                    'jobtitleid' => 'INTEGER - ID chức danh (Foreign Key -> jobtitle.jobtitleid)',
                    'unitid' => 'INTEGER - ID đơn vị (Foreign Key -> budgetspendingunit.unitid)',
                    'startdate' => 'DATE - Ngày bắt đầu làm việc',
                    'currentcoefficient' => 'DECIMAL(4,2) - Hệ số lương hiện tại',
                    'coefficient_history' => 'JSON - Lịch sử hệ số lương',
                    'status' => 'STRING - Trạng thái'
                ]
            ],
            'payrollcomponent' => [
                'description' => 'Bảng lưu các thành phần lương (thu nhập, khấu trừ, đóng góp)',
                'primary_key' => 'componentid',
                'columns' => [
                    'componentid' => 'INTEGER - ID thành phần lương (Primary Key)',
                    'componentname' => 'STRING - Tên thành phần lương',
                    'componentgroup' => 'STRING - Nhóm thành phần (Thu nhập, Khấu trừ, Đóng góp)',
                    'calculationmethod' => 'STRING - Phương pháp tính (Hệ số, Phần trăm, Cố định)',
                    'componentdescription' => 'TEXT - Mô tả thành phần lương'
                ]
            ],
            'payrollrun' => [
                'description' => 'Bảng lưu các kỳ tính lương',
                'primary_key' => 'payrollrunid',
                'columns' => [
                    'payrollrunid' => 'INTEGER - ID kỳ tính lương (Primary Key)',
                    'unitid' => 'INTEGER - ID đơn vị (Foreign Key -> budgetspendingunit.unitid)',
                    'basesalaryid' => 'INTEGER - ID mức lương cơ bản (Foreign Key -> basesalary.basesalaryid)',
                    'payrollperiod' => 'STRING - Kỳ tính lương (ví dụ: 2024-01)',
                    'status' => 'STRING - Trạng thái (draft, approved)',
                    'createdat' => 'DATETIME - Ngày tạo',
                    'approvedat' => 'DATETIME - Ngày chốt',
                    'note' => 'TEXT - Ghi chú'
                ]
            ],
            'payrollrundetail' => [
                'description' => 'Bảng lưu chi tiết lương từng giáo viên trong mỗi kỳ',
                'primary_key' => 'detailid',
                'columns' => [
                    'detailid' => 'INTEGER - ID chi tiết (Primary Key)',
                    'payrollrunid' => 'INTEGER - ID kỳ tính lương (Foreign Key -> payrollrun.payrollrunid)',
                    'teacherid' => 'INTEGER - ID giáo viên (Foreign Key -> teacher.teacherid)',
                    'totalincome' => 'DECIMAL - Tổng thu nhập',
                    'totalemployeedeductions' => 'DECIMAL - Tổng khấu trừ người lao động',
                    'totalemployercontributions' => 'DECIMAL - Tổng đóng góp người sử dụng lao động',
                    'netpay' => 'DECIMAL - Lương thực lĩnh',
                    'totalcost' => 'DECIMAL - Tổng chi phí',
                    'note' => 'TEXT - Ghi chú'
                ]
            ],
            'payrollrundetailcomponent' => [
                'description' => 'Bảng lưu chi tiết từng thành phần lương trong bảng lương giáo viên',
                'primary_key' => 'componentdetailid',
                'columns' => [
                    'componentdetailid' => 'INTEGER - ID chi tiết thành phần (Primary Key)',
                    'detailid' => 'INTEGER - ID chi tiết bảng lương (Foreign Key -> payrollrundetail.detailid)',
                    'componentid' => 'INTEGER - ID thành phần lương (Foreign Key -> payrollcomponent.componentid)',
                    'amount' => 'DECIMAL - Số tiền'
                ]
            ],
            'basesalary' => [
                'description' => 'Bảng lưu mức lương cơ bản theo đơn vị và thời gian',
                'primary_key' => 'basesalaryid',
                'columns' => [
                    'basesalaryid' => 'INTEGER - ID mức lương cơ bản (Primary Key)',
                    'unitid' => 'INTEGER - ID đơn vị (Foreign Key -> budgetspendingunit.unitid)',
                    'effectivedate' => 'DATE - Ngày có hiệu lực',
                    'expirationdate' => 'DATE - Ngày hết hiệu lực',
                    'basesalaryamount' => 'DECIMAL - Mức lương cơ bản',
                    'note' => 'TEXT - Ghi chú'
                ]
            ],
            'salaryincreasedecision' => [
                'description' => 'Bảng lưu quyết định nâng lương cho giáo viên',
                'primary_key' => 'decisionid',
                'columns' => [
                    'decisionid' => 'INTEGER - ID quyết định (Primary Key)',
                    'teacherid' => 'INTEGER - ID giáo viên (Foreign Key -> teacher.teacherid)',
                    'decisiondate' => 'DATE - Ngày ký quyết định',
                    'oldcoefficient' => 'DECIMAL(4,4) - Hệ số cũ',
                    'newcoefficient' => 'DECIMAL(4,4) - Hệ số mới',
                    'applydate' => 'DATE - Ngày áp dụng',
                    'note' => 'TEXT - Ghi chú'
                ]
            ],
            'payrollcomponentconfig' => [
                'description' => 'Bảng lưu cấu hình thành phần lương theo thời gian',
                'primary_key' => 'configid',
                'columns' => [
                    'configid' => 'INTEGER - ID cấu hình (Primary Key)',
                    'componentid' => 'INTEGER - ID thành phần lương (Foreign Key -> payrollcomponent.componentid)',
                    'effectivedate' => 'DATE - Ngày có hiệu lực',
                    'expirationdate' => 'DATE - Ngày hết hiệu lực',
                    'defaultcoefficient' => 'DECIMAL(4,4) - Hệ số mặc định',
                    'percentagevalue' => 'DECIMAL(4,4) - Giá trị phần trăm',
                    'fixedamount' => 'DECIMAL - Số tiền cố định',
                    'note' => 'TEXT - Ghi chú'
                ]
            ],
            'payrollcomponentunitconfig' => [
                'description' => 'Bảng lưu cấu hình thành phần lương theo đơn vị',
                'primary_key' => 'unitconfigid',
                'columns' => [
                    'unitconfigid' => 'INTEGER - ID cấu hình (Primary Key)',
                    'unitid' => 'INTEGER - ID đơn vị (Foreign Key -> budgetspendingunit.unitid)',
                    'componentid' => 'INTEGER - ID thành phần lương (Foreign Key -> payrollcomponent.componentid)',
                    'effectivedate' => 'DATE - Ngày có hiệu lực',
                    'expirationdate' => 'DATE - Ngày hết hiệu lực',
                    'defaultcoefficient' => 'DECIMAL(4,4) - Hệ số mặc định',
                    'percentagevalue' => 'DECIMAL(4,4) - Giá trị phần trăm',
                    'fixedamount' => 'DECIMAL - Số tiền cố định',
                    'note' => 'TEXT - Ghi chú'
                ]
            ],
            'teacherpayrollcomponent' => [
                'description' => 'Bảng lưu cấu hình thành phần lương theo từng giáo viên',
                'primary_key' => 'teachercomponentid',
                'columns' => [
                    'teachercomponentid' => 'INTEGER - ID cấu hình (Primary Key)',
                    'teacherid' => 'INTEGER - ID giáo viên (Foreign Key -> teacher.teacherid)',
                    'componentid' => 'INTEGER - ID thành phần lương (Foreign Key -> payrollcomponent.componentid)',
                    'effectivedate' => 'DATE - Ngày có hiệu lực',
                    'expirationdate' => 'DATE - Ngày hết hiệu lực',
                    'adjustcustomcoefficient' => 'DECIMAL(4,4) - Hệ số điều chỉnh tùy chỉnh',
                    'adjustcustompercentage' => 'DECIMAL(4,4) - Phần trăm điều chỉnh tùy chỉnh',
                    'note' => 'TEXT - Ghi chú'
                ]
            ],
            'jobtitle' => [
                'description' => 'Bảng lưu chức danh',
                'primary_key' => 'jobtitleid',
                'columns' => [
                    'jobtitleid' => 'INTEGER - ID chức danh (Primary Key)',
                    'jobtitlename' => 'STRING - Tên chức danh',
                    'jobtitledescription' => 'TEXT - Mô tả chức danh'
                ]
            ],
            'teacherjobtitlehistory' => [
                'description' => 'Bảng lưu lịch sử chức danh của giáo viên',
                'primary_key' => 'historyid',
                'columns' => [
                    'historyid' => 'INTEGER - ID lịch sử (Primary Key)',
                    'teacherid' => 'INTEGER - ID giáo viên (Foreign Key -> teacher.teacherid)',
                    'jobtitleid' => 'INTEGER - ID chức danh (Foreign Key -> jobtitle.jobtitleid)',
                    'effectivedate' => 'DATE - Ngày có hiệu lực',
                    'expirationdate' => 'DATE - Ngày hết hiệu lực',
                    'note' => 'TEXT - Ghi chú'
                ]
            ],
            'budgetspendingunit' => [
                'description' => 'Bảng lưu đơn vị chi ngân sách',
                'primary_key' => 'unitid',
                'columns' => [
                    'unitid' => 'INTEGER - ID đơn vị (Primary Key)',
                    'unitname' => 'STRING - Tên đơn vị',
                    'unitdescription' => 'TEXT - Mô tả đơn vị'
                ]
            ],
            'employmentcontract' => [
                'description' => 'Bảng lưu hợp đồng lao động của giáo viên',
                'primary_key' => 'contractid',
                'columns' => [
                    'contractid' => 'INTEGER - ID hợp đồng (Primary Key)',
                    'teacherid' => 'INTEGER - ID giáo viên (Foreign Key -> teacher.teacherid)',
                    'contracttype' => 'STRING - Loại hợp đồng',
                    'startdate' => 'DATE - Ngày bắt đầu',
                    'enddate' => 'DATE - Ngày kết thúc',
                    'note' => 'TEXT - Ghi chú'
                ]
            ],
            'systemuser' => [
                'description' => 'Bảng lưu người dùng hệ thống',
                'primary_key' => 'userid',
                'columns' => [
                    'userid' => 'INTEGER - ID người dùng (Primary Key)',
                    'username' => 'STRING - Tên đăng nhập',
                    'passwordhash' => 'STRING - Mật khẩu đã hash',
                    'email' => 'STRING - Email',
                    'fullname' => 'STRING - Họ và tên',
                    'avatar' => 'STRING - Đường dẫn avatar',
                    'status' => 'STRING - Trạng thái',
                    'teacherid' => 'INTEGER - ID giáo viên (Foreign Key -> teacher.teacherid, nullable)',
                    'roleid' => 'INTEGER - ID vai trò (Foreign Key -> role.roleid)',
                    'createdat' => 'DATETIME - Ngày tạo',
                    'updatedat' => 'DATETIME - Ngày cập nhật'
                ]
            ],
            'role' => [
                'description' => 'Bảng lưu vai trò người dùng (Admin, Kế toán, Giáo viên)',
                'primary_key' => 'roleid',
                'columns' => [
                    'roleid' => 'INTEGER - ID vai trò (Primary Key)',
                    'rolename' => 'STRING - Tên vai trò',
                    'roledescription' => 'TEXT - Mô tả vai trò'
                ]
            ],
        ];

        return $tables;
    }

    /**
     * Lấy thông tin về relationships giữa các bảng
     *
     * @return array
     */
    public function getDatabaseRelationships(): array
    {
        return [
            'teacher' => [
                'belongs_to' => [
                    'jobtitle' => 'teacher.jobtitleid -> jobtitle.jobtitleid',
                    'budgetspendingunit' => 'teacher.unitid -> budgetspendingunit.unitid'
                ],
                'has_many' => [
                    'salaryincreasedecision' => 'salaryincreasedecision.teacherid -> teacher.teacherid',
                    'teacherpayrollcomponent' => 'teacherpayrollcomponent.teacherid -> teacher.teacherid',
                    'teacherjobtitlehistory' => 'teacherjobtitlehistory.teacherid -> teacher.teacherid',
                    'employmentcontract' => 'employmentcontract.teacherid -> teacher.teacherid',
                    'payrollrundetail' => 'payrollrundetail.teacherid -> teacher.teacherid',
                    'systemuser' => 'systemuser.teacherid -> teacher.teacherid (nullable)'
                ]
            ],
            'payrollcomponent' => [
                'belongs_to' => [],
                'has_many' => [
                    'payrollcomponentconfig' => 'payrollcomponentconfig.componentid -> payrollcomponent.componentid',
                    'payrollcomponentunitconfig' => 'payrollcomponentunitconfig.componentid -> payrollcomponent.componentid',
                    'teacherpayrollcomponent' => 'teacherpayrollcomponent.componentid -> payrollcomponent.componentid',
                    'payrollrundetailcomponent' => 'payrollrundetailcomponent.componentid -> payrollcomponent.componentid'
                ]
            ],
            'payrollrun' => [
                'belongs_to' => [
                    'budgetspendingunit' => 'payrollrun.unitid -> budgetspendingunit.unitid',
                    'basesalary' => 'payrollrun.basesalaryid -> basesalary.basesalaryid'
                ],
                'has_many' => [
                    'payrollrundetail' => 'payrollrundetail.payrollrunid -> payrollrun.payrollrunid'
                ]
            ],
            'payrollrundetail' => [
                'belongs_to' => [
                    'payrollrun' => 'payrollrundetail.payrollrunid -> payrollrun.payrollrunid',
                    'teacher' => 'payrollrundetail.teacherid -> teacher.teacherid'
                ],
                'has_many' => [
                    'payrollrundetailcomponent' => 'payrollrundetailcomponent.detailid -> payrollrundetail.detailid'
                ]
            ],
            'basesalary' => [
                'belongs_to' => [
                    'budgetspendingunit' => 'basesalary.unitid -> budgetspendingunit.unitid'
                ],
                'has_many' => [
                    'payrollrun' => 'payrollrun.basesalaryid -> basesalary.basesalaryid'
                ]
            ],
            'budgetspendingunit' => [
                'belongs_to' => [],
                'has_many' => [
                    'teacher' => 'teacher.unitid -> budgetspendingunit.unitid',
                    'payrollrun' => 'payrollrun.unitid -> budgetspendingunit.unitid',
                    'basesalary' => 'basesalary.unitid -> budgetspendingunit.unitid',
                    'payrollcomponentunitconfig' => 'payrollcomponentunitconfig.unitid -> budgetspendingunit.unitid'
                ]
            ],
            'jobtitle' => [
                'belongs_to' => [],
                'has_many' => [
                    'teacher' => 'teacher.jobtitleid -> jobtitle.jobtitleid',
                    'teacherjobtitlehistory' => 'teacherjobtitlehistory.jobtitleid -> jobtitle.jobtitleid'
                ]
            ],
            'systemuser' => [
                'belongs_to' => [
                    'teacher' => 'systemuser.teacherid -> teacher.teacherid (nullable)',
                    'role' => 'systemuser.roleid -> role.roleid'
                ],
                'has_many' => []
            ],
            'role' => [
                'belongs_to' => [],
                'has_many' => [
                    'systemuser' => 'systemuser.roleid -> role.roleid'
                ]
            ]
        ];
    }
}

