<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollRunDetail extends Model
{
    protected $table = 'payrollrundetail';

    protected $primaryKey = 'detailid';

    public $timestamps = false;

    protected $fillable = [
        'payrollrunid',
        'teacherid',
        'totalincome',
        'totalemployeedeductions',
        'totalemployercontributions',
        'netpay',
        'totalcost',
        'note',
    ];

    protected $casts = [
        'detailid' => 'integer',
        'payrollrunid' => 'integer',
        'teacherid' => 'integer',
        'totalincome' => 'decimal:2',
        'totalemployeedeductions' => 'decimal:2',
        'totalemployercontributions' => 'decimal:2',
        'netpay' => 'decimal:2',
        'totalcost' => 'decimal:2',
    ];

    /**
     * Relationship with PayrollRun
     */
    public function payrollRun()
    {
        return $this->belongsTo(PayrollRun::class, 'payrollrunid', 'payrollrunid');
    }

    /**
     * Relationship with Teacher
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacherid', 'teacherid');
    }

    /**
     * Relationship with PayrollRunDetailComponent
     */
    public function components()
    {
        return $this->hasMany(PayrollRunDetailComponent::class, 'detailid', 'detailid');
    }
}

