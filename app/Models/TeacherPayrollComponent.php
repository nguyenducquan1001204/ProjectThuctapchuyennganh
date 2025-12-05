<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherPayrollComponent extends Model
{
    protected $table = 'teacherpayrollcomponent';

    protected $primaryKey = 'teachercomponentid';

    public $timestamps = false;

    protected $fillable = [
        'teacherid',
        'componentid',
        'effectivedate',
        'expirationdate',
        'adjustcustomcoefficient',
        'adjustcustompercentage',
        'note',
    ];

    protected $casts = [
        'teachercomponentid' => 'integer',
        'teacherid' => 'integer',
        'componentid' => 'integer',
        'effectivedate' => 'date',
        'expirationdate' => 'date',
        'adjustcustomcoefficient' => 'decimal:4',
        'adjustcustompercentage' => 'decimal:4',
    ];

    /**
     * Quan hệ với Teacher
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacherid', 'teacherid');
    }

    /**
     * Quan hệ với PayrollComponent
     */
    public function component()
    {
        return $this->belongsTo(PayrollComponent::class, 'componentid', 'componentid');
    }
}

