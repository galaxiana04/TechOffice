<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoSekdivSmDecision extends Model
{
    use HasFactory;

    protected $fillable = [
        'memo_sekdiv_id',
        'smpositionname',
    ];

    public function memoSekdiv()
    {
        return $this->belongsTo(MemoSekdiv::class);
    }
    public function unitUnderSms()
    {
        return $this->hasMany(MemoSekdivUnitUnderSm::class, 'memo_sekdiv_sm_decision_id');
    }
}
