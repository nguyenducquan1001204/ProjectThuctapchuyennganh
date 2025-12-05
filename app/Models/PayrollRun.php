<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollRun extends Model
{
    protected $table = 'payrollrun';

    protected $primaryKey = 'payrollrunid';

    public $timestamps = false;

    protected $fillable = [
        'unitid',
        'basesalaryid',
        'payrollperiod',
        'status',
        'createdat',
        'approvedat',
        'note',
    ];

    protected $casts = [
        'payrollrunid' => 'integer',
        'unitid' => 'integer',
        'basesalaryid' => 'integer',
        'payrollperiod' => 'string',
        'status' => 'string',
        'createdat' => 'datetime',
        'approvedat' => 'datetime',
    ];

    /**
     * Relationship with BudgetSpendingUnit
     */
    public function unit()
    {
        return $this->belongsTo(BudgetSpendingUnit::class, 'unitid', 'unitid');
    }

    /**
     * Relationship with BaseSalary
     */
    public function baseSalary()
    {
        return $this->belongsTo(BaseSalary::class, 'basesalaryid', 'basesalaryid');
    }

    /**
     * Get status label in Vietnamese
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Khởi tạo',
            'calculating' => 'Đang tính toán',
            'approved' => 'Đã chốt',
            'paid' => 'Đã thanh toán',
            default => $this->status,
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-secondary',
            'calculating' => 'bg-info',
            'approved' => 'bg-success',
            'paid' => 'bg-primary',
            default => 'bg-secondary',
        };
    }
}

