<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmploymentContract extends Model
{
    protected $table = 'employmentcontract';
    
    protected $primaryKey = 'contractid';
    
    public $incrementing = true;
    
    public $timestamps = false;
    
    protected $fillable = [
        'teacherid',
        'contracttype',
        'signdate',
        'startdate',
        'enddate',
        'note',
    ];
    
    protected $casts = [
        'contractid' => 'integer',
        'teacherid' => 'integer',
        'signdate' => 'date',
        'startdate' => 'date',
        'enddate' => 'date',
    ];
    
    /**
     * Relationship với Teacher
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacherid', 'teacherid');
    }
}

