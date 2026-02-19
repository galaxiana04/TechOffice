<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewRbdNode extends Model
{
    use HasFactory;

    protected $table = 'new_rbd_nodes';

    protected $fillable = [
        'rbd_instance_id',
        'failure_rate_id',
        'foreign_instance_id',
        'key_value',
        'category',
        'code',
        'name',
        'x',
        'y',
        'reliability',
        'k',
        'n',
        'group_id',
        'configuration',
        'quantity',
        't_initial',
    ];

    public function rbdInstance()
    {
        return $this->belongsTo(NewRbdInstance::class, 'rbd_instance_id');
    }

    public function failureRate()
    {
        return $this->belongsTo(NewRbdFailureRate::class, 'failure_rate_id');
    }

    public function foreignInstance()  // Baru
    {
        return $this->belongsTo(NewRbdInstance::class, 'foreign_instance_id');
    }
}
