<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model
{
    protected $table = 'jobtitle';
    
    protected $primaryKey = 'jobtitleid';
    
    public $timestamps = false;
    
    protected $fillable = [
        'jobtitlename',
        'jobtitledescription',
    ];

    protected $casts = [
        'jobtitleid' => 'integer',
    ];
}

