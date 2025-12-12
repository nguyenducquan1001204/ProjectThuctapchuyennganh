<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    protected $table = 'chat_conversation';

    protected $primaryKey = 'conversationid';

    public $timestamps = false;

    protected $fillable = [
        'conversationid',
        'userid',
        'title',
        'createdat',
        'updatedat',
    ];

    protected $casts = [
        'conversationid' => 'string',
        'userid' => 'integer',
        'title' => 'string',
        'createdat' => 'datetime',
        'updatedat' => 'datetime',
    ];

    /**
     * Quan hệ với SystemUser
     */
    public function user()
    {
        return $this->belongsTo(SystemUser::class, 'userid', 'userid');
    }

    /**
     * Quan hệ với ChatHistory
     */
    public function messages()
    {
        return $this->hasMany(ChatHistory::class, 'conversationid', 'conversationid')
            ->orderBy('createdat', 'asc');
    }

    /**
     * Lấy tin nhắn cuối cùng
     */
    public function getLastMessage()
    {
        return $this->messages()->orderBy('createdat', 'desc')->first();
    }
}

