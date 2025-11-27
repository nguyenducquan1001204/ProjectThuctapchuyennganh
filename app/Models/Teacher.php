<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'status',
    ];

    protected $casts = [
        'teacherid' => 'integer',
        'jobtitleid' => 'integer',
        'unitid' => 'integer',
        'birthdate' => 'date',
        'startdate' => 'date',
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
}

