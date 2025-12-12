<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    protected $table = 'chat_history';

    protected $primaryKey = 'historyid';

    public $timestamps = false;

    protected $fillable = [
        'userid',
        'conversationid',
        'role',
        'message',
        'createdat',
    ];

    protected $casts = [
        'historyid' => 'integer',
        'userid' => 'integer',
        'conversationid' => 'string',
        'role' => 'string',
        'message' => 'string',
        'createdat' => 'datetime',
    ];

    /**
     * Quan hệ với SystemUser
     */
    public function user()
    {
        return $this->belongsTo(SystemUser::class, 'userid', 'userid');
    }

    /**
     * Quan hệ với ChatConversation
     */
    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversationid', 'conversationid');
    }
}

