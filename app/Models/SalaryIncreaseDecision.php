<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryIncreaseDecision extends Model
{
    protected $table = 'salaryincreasedecision';

    protected $primaryKey = 'decisionid';

    public $timestamps = false;

    protected $fillable = [
        'teacherid',
        'decisiondate',
        'oldcoefficient',
        'newcoefficient',
        'applydate',
        'note',
    ];

    protected $casts = [
        'decisionid' => 'integer',
        'teacherid' => 'integer',
        'decisiondate' => 'date',
        'oldcoefficient' => 'decimal:4',
        'newcoefficient' => 'decimal:4',
        'applydate' => 'date',
    ];

    /**
     * Quan hệ với Teacher
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacherid', 'teacherid');
    }
}

