<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollComponent extends Model
{
    protected $table = 'payrollcomponent';

    protected $primaryKey = 'componentid';

    public $timestamps = false;

    protected $fillable = [
        'componentname',
        'componentgroup',
        'calculationmethod',
        'componentdescription',
    ];
}


