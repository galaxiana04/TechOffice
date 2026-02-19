<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewRbdFailureRate extends Model
{
    use HasFactory;

    protected $table = 'new_rbd_failure_rates';

    protected $fillable = [
        'name',
        'failure_rate',
        'new_rbd_model_id',
        'source',
    ];

    public function nodes()
    {
        return $this->hasMany(NewRbdNode::class, 'failure_rate_id');
    }
    // Relasi ke model
    public function newRbdModel()
    {
        return $this->belongsTo(NewRbdModel::class, 'new_rbd_model_id');
    }
}
