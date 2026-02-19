<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemosekdivFeedback extends Model
{
    use HasFactory;

    protected $table = 'memosekdiv_feedbacks';

    protected $fillable = [
        'memo_sekdiv_id',
        'pic',
        'author',
        'level',
        'email',
        'reviewresult',
        'condition1',
        'condition2',
        'isread',
        'comment',
    ];

    public function memoSekdiv()
    {
        return $this->belongsTo(MemoSekdiv::class, 'memo_sekdiv_id');
    }
    public function files()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }
}
