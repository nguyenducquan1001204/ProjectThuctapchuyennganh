<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class SystemUser extends Authenticatable
{
    protected $table = 'systemuser';

    protected $primaryKey = 'userid';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'passwordhash',
        'email',
        'fullname',
        'avatar',
        'status',
        'teacherid',
        'roleid',
        'createdat',
        'updatedat',
    ];

    protected $casts = [
        'userid'    => 'integer',
        'teacherid' => 'integer',
        'roleid'    => 'integer',
        'createdat' => 'datetime',
        'updatedat' => 'datetime',
    ];

    /**
     * Cột mật khẩu dùng cho Auth
     */
    public function getAuthPassword()
    {
        return $this->passwordhash;
    }

    /**
     * Liên kết tới giáo viên (nếu có)
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacherid', 'teacherid');
    }

    /**
     * Liên kết tới vai trò
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'roleid', 'roleid');
    }
}


