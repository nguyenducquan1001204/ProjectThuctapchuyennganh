<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollComponentConfig extends Model
{
    protected $table = 'payrollcomponentconfig';

    protected $primaryKey = 'configid';

    public $timestamps = false;

    protected $fillable = [
        'componentid',
        'effectivedate',
        'expirationdate',
        'defaultcoefficient',
        'percentagevalue',
        'fixedamount',
        'note',
    ];

    protected $casts = [
        'effectivedate' => 'date',
        'expirationdate' => 'date',
        'defaultcoefficient' => 'decimal:4',
        'percentagevalue' => 'decimal:4',
        'fixedamount' => 'decimal:2',
    ];

    /**
     * Quan hệ với PayrollComponent
     */
    public function component()
    {
        return $this->belongsTo(PayrollComponent::class, 'componentid', 'componentid');
    }
}

