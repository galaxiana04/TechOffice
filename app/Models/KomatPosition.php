<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomatPosition extends Model
{
    use HasFactory;

    protected $table = 'komat_position';

    protected $fillable = [
        'komat_hist_req_id',
        'unit_id',
        'level',
        'status',
        'status_process',
    ];

    public function komatHistReq()
    {
        return $this->belongsTo(KomatHistReq::class, 'komat_hist_req_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    // Add this relationship to access KomatFeedback
    public function feedbacks()
    {
        return $this->hasMany(KomatFeedback::class, 'komat_position_id');
    }
}
