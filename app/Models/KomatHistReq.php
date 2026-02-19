<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomatHistReq extends Model
{
    use HasFactory;

    protected $table = 'komat_hist_req';

    protected $fillable = [
        'komat_process_history_id',
        'komat_requirement_id',
    ];

    public function komatProcessHistory()
    {
        return $this->belongsTo(KomatProcessHistory::class, 'komat_process_history_id');
    }

    public function komatRequirement()
    {
        return $this->belongsTo(KomatRequirement::class, 'komat_requirement_id');
    }

    // Relasi ke banyak KomatPosition
    public function komatPositions()
    {
        return $this->hasMany(KomatPosition::class, 'komat_hist_req_id');
    }
}
