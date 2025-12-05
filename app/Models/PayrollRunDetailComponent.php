<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollRunDetailComponent extends Model
{
    protected $table = 'payrollrundetailcomponent';

    protected $primaryKey = 'detailcomponentid';

    public $timestamps = false;

    protected $fillable = [
        'detailid',
        'componentid',
        'appliedcoefficient',
        'appliedpercentage',
        'calculatedamount',
        'note',
    ];

    protected $casts = [
        'detailcomponentid' => 'integer',
        'detailid' => 'integer',
        'componentid' => 'integer',
        'appliedcoefficient' => 'decimal:4',
        'appliedpercentage' => 'decimal:4',
        'calculatedamount' => 'decimal:2',
    ];

    /**
     * Relationship with PayrollRunDetail
     */
    public function detail()
    {
        return $this->belongsTo(PayrollRunDetail::class, 'detailid', 'detailid');
    }

    /**
     * Relationship with PayrollComponent
     */
    public function component()
    {
        return $this->belongsTo(PayrollComponent::class, 'componentid', 'componentid');
    }
}

