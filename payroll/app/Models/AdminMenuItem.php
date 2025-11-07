<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminMenuItem extends Model
{
    protected $table = 'adminmenuitems';

    protected $fillable = [
        'title',
        'slug',
        'parentId',
        'orderIndex',
        'isActive',
        'routeName',
        'externalUrl',
        'target',
        'icon',
    ];

    public function children()
    {
        return $this->hasMany(self::class, 'parentId')
            ->where('isActive', true)
            ->orderBy('orderIndex');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parentId');
    }
}

