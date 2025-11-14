<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetSpendingUnit extends Model
{
    protected $table = 'budgetspendingunit';
    
    protected $primaryKey = 'unitid';
    
    public $timestamps = false;
    
    protected $fillable = [
        'unitname',
        'address',
        'taxnumber',
        'note',
    ];

    protected $casts = [
        'unitid' => 'integer',
    ];
}

