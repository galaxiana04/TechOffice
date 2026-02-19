<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessageKatalogKomat extends Model
{
    protected $table = 'chat_messages_katalog_komat';
    protected $fillable = ['chat_session_id', 'sender', 'message'];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSessionKatalogKomat::class, 'chat_session_id', 'id');
    }
}
