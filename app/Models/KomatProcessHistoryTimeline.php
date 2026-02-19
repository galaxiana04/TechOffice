<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomatProcessHistoryTimeline extends Model
{
    use HasFactory;

    protected $table = 'komat_process_history_timeline';

    protected $fillable = [
        'komat_process_history_id',
        'infostatus',
        'entertime',
    ];

    /**
     * Relasi ke KomatProcessHistory
     */
    public function komatProcessHistory()
    {
        return $this->belongsTo(KomatProcessHistory::class, 'komat_process_history_id');
    }
}
