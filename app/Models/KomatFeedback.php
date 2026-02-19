<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomatFeedback extends Model
{
    use HasFactory;

    protected $table = 'komat_feedback';

    protected $fillable = [
        'komat_position_id',
        'komat_process_history_id',
        'komat_requirement_id',
        'comment',
        'status',
        'feedback_status',
        'user_rule',
        'user_name',
        'user_id',
    ];

    /**
     * Relasi ke KomatPosition
     */
    public function komatPosition()
    {
        return $this->belongsTo(KomatPosition::class, 'komat_position_id');
    }
    // Relasi ke KomatProcessHistory
    public function komatProcessHistory()
    {
        return $this->belongsTo(KomatProcessHistory::class, 'komat_process_history_id');
    }

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    public function files()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }
}
