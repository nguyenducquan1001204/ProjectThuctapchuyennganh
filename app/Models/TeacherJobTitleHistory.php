<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherJobTitleHistory extends Model
{
    protected $table = 'teacherjobtitlehistory';
    
    protected $primaryKey = 'historyid';
    
    public $timestamps = false;
    
    protected $fillable = [
        'teacherid',
        'jobtitleid',
        'effectivedate',
        'expiredate',
        'note',
    ];

    protected $casts = [
        'historyid' => 'integer',
        'teacherid' => 'integer',
        'jobtitleid' => 'integer',
        'effectivedate' => 'date',
        'expiredate' => 'date',
    ];

    /**
     * Relationship với Teacher
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacherid', 'teacherid');
    }

    /**
     * Relationship với JobTitle
     */
    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class, 'jobtitleid', 'jobtitleid');
    }
}

