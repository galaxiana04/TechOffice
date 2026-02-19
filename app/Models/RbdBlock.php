<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RbdBlock extends Model
{
    use HasFactory;

    protected $table = 'rbd_blocks';

    protected $fillable = [
        'name',
        'lambda',
        'rbdidentity_id',
        'source'
    ];

    /**
     * Relasi ke RbdIdentity
     */
    public function rbdIdentity()
    {
        return $this->belongsTo(RbdIdentity::class, 'rbdidentity_id');
    }
    public function rbdNodes()
    {
        return $this->hasMany(RbdNode::class, 'rbd_block_id');
    }
}
