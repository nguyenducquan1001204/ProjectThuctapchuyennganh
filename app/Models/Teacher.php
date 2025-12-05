<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Teacher extends Model
{
    protected $table = 'teacher';
    
    protected $primaryKey = 'teacherid';
    
    public $timestamps = false;
    
    protected $fillable = [
        'fullname',
        'birthdate',
        'gender',
        'jobtitleid',
        'unitid',
        'startdate',
        'currentcoefficient',
        'coefficient_history',
        'status',
    ];

    protected $casts = [
        'teacherid' => 'integer',
        'jobtitleid' => 'integer',
        'unitid' => 'integer',
        'birthdate' => 'date',
        'startdate' => 'date',
        'currentcoefficient' => 'decimal:2',
        'coefficient_history' => 'array',
    ];

    /**
     * Relationship với JobTitle
     */
    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class, 'jobtitleid', 'jobtitleid');
    }

    /**
     * Relationship với BudgetSpendingUnit
     */
    public function unit()
    {
        return $this->belongsTo(BudgetSpendingUnit::class, 'unitid', 'unitid');
    }

    /**
     * Thêm bản ghi lịch sử hệ số lương
     */
    public function addCoefficientHistory($coefficient, $effectivedate = null, $expiredate = null, $note = null)
    {
        $history = $this->coefficient_history ?? [];
        
        // Đóng bản ghi cũ (nếu có) bằng cách set expiredate
        if (!empty($history)) {
            foreach ($history as &$record) {
                if (!isset($record['expiredate']) || $record['expiredate'] === null) {
                    $record['expiredate'] = $expiredate ?: (date('Y-m-d', strtotime(($effectivedate ?: Carbon::today()->format('Y-m-d')) . ' -1 day')));
                }
            }
        }

        // Thêm bản ghi mới
        $history[] = [
            'coefficient' => (float)$coefficient,
            'effectivedate' => $effectivedate ?: Carbon::today()->format('Y-m-d'),
            'expiredate' => $expiredate,
            'note' => $note,
        ];

        $this->coefficient_history = $history;
        $this->save();
    }

    /**
     * Lấy lịch sử hệ số lương
     */
    public function getCoefficientHistory()
    {
        return $this->coefficient_history ?? [];
    }
}
