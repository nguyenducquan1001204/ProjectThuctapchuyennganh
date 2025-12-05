<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollComponentUnitConfig extends Model
{
    protected $table = 'payrollcomponentunitconfig';

    protected $primaryKey = 'unitconfigid';

    public $timestamps = false;

    protected $fillable = [
        'unitid',
        'componentid',
        'effectivedate',
        'expirationdate',
        'adjustcoefficient',
        'adjustpercentage',
        'adjustfixedamount',
        'note',
    ];

    protected $casts = [
        'unitconfigid' => 'integer',
        'unitid' => 'integer',
        'componentid' => 'integer',
        'effectivedate' => 'date',
        'expirationdate' => 'date',
        'adjustcoefficient' => 'decimal:4',
        'adjustpercentage' => 'decimal:4',
        'adjustfixedamount' => 'decimal:2',
    ];

    /**
     * Quan hệ với BudgetSpendingUnit
     */
    public function unit()
    {
        return $this->belongsTo(BudgetSpendingUnit::class, 'unitid', 'unitid');
    }

    /**
     * Quan hệ với PayrollComponent
     */
    public function component()
    {
        return $this->belongsTo(PayrollComponent::class, 'componentid', 'componentid');
    }
}

