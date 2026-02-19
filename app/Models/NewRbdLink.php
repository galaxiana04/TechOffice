<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewRbdLink extends Model
{
    use HasFactory;

    protected $table = 'new_rbd_links';

    protected $fillable = [
        'rbd_instance_id',
        'from_node_id',
        'to_node_id',
    ];

    public function rbdInstance()
    {
        return $this->belongsTo(NewRbdInstance::class, 'rbd_instance_id');
    }


    // App/Models/NewRbdLink.php
    public function fromNode()
    {
        return $this->belongsTo(NewRbdNode::class, 'from_node_id');
    }

    public function toNode()
    {
        return $this->belongsTo(NewRbdNode::class, 'to_node_id');
    }
}
