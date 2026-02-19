<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSessionKatalogKomat extends Model
{
    protected $table = 'chat_session_katalog_komat';
    protected $fillable = ['session_id', 'name'];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessageKatalogKomat::class, 'chat_session_id', 'id');
    }
}
