<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseSalary extends Model
{
    protected $table = 'basesalary';

    protected $primaryKey = 'basesalaryid';

    public $timestamps = false;

    protected $fillable = [
        'unitid',
        'effectivedate',
        'expirationdate',
        'basesalaryamount',
        'note',
    ];

    protected $casts = [
        'basesalaryid' => 'integer',
        'unitid' => 'integer',
        'effectivedate' => 'date',
        'expirationdate' => 'date',
        'basesalaryamount' => 'decimal:2',
    ];

    /**
     * Quan hệ với BudgetSpendingUnit
     */
    public function unit()
    {
        return $this->belongsTo(BudgetSpendingUnit::class, 'unitid', 'unitid');
    }
}


