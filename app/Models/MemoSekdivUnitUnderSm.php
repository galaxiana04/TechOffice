<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoSekdivUnitUnderSm extends Model
{
    use HasFactory;

    protected $fillable = [
        'memo_sekdiv_sm_decision_id',
        'unitname',
    ];

    public function smDecision()
    {
        return $this->belongsTo(MemoSekdivSmDecision::class, 'memo_sekdiv_sm_decision_id');
    }
}
