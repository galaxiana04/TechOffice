<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoSekdivAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'memo_sekdiv_id',
        'user_id',
        'permission_user_id',
    ];

    public function memoSekdiv()
    {
        return $this->belongsTo(MemoSekdiv::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
