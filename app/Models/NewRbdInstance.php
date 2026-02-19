<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewRbdInstance extends Model
{
    use HasFactory;

    protected $table = 'new_rbd_instances';

    protected $fillable = [
        'componentname',
        'new_rbd_model_id', // tambahkan kolom relasi baru
        'time_interval',
        'temporary_reliability_value',
        'temporary_failure_rate_value',
        'diagram_url',
        'user_id', // tambahkan
        'r_t_symbolic',
        'hazard_rate_expression',
        'frequency_expression',
        't_value',
        't_expression'
    ];





    public function newRbdModel()
    {
        return $this->belongsTo(NewRbdModel::class, 'new_rbd_model_id')->nullable();
    }

    public function nodes()
    {
        return $this->hasMany(NewRbdNode::class, 'rbd_instance_id');
    }

    public function links()
    {
        return $this->hasMany(NewRbdLink::class, 'rbd_instance_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
