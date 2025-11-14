<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminMenuItem extends Model
{
    protected $table = 'adminmenuitems';
    
    public $timestamps = true;
    
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    
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

    protected $casts = [
        'isActive' => 'boolean',
        'orderIndex' => 'integer',
        'parentId' => 'integer',
        'createdAt' => 'datetime',
        'updatedAt' => 'datetime',
    ];

    /**
     * Quan hệ với chính nó để lấy menu con
     */
    public function children()
    {
        return $this->hasMany(AdminMenuItem::class, 'parentId', 'id')
            ->where('isActive', 1)
            ->orderBy('orderIndex', 'asc');
    }

    /**
     * Quan hệ với parent
     */
    public function parent()
    {
        return $this->belongsTo(AdminMenuItem::class, 'parentId', 'id');
    }

    /**
     * Lấy tất cả menu items đã được sắp xếp theo cấp
     */
    public static function getMenuTree()
    {
        return self::where('isActive', 1)
            ->whereNull('parentId')
            ->orderBy('orderIndex', 'asc')
            ->with('children')
            ->get();
    }
}
