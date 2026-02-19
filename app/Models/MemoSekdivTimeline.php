<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoSekdivTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'memo_sekdiv_id',
        'infostatus',
        'entertime',
    ];

    public function memoSekdiv()
    {
        return $this->belongsTo(MemoSekdiv::class);
    }
}
