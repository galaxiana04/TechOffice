<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RbdNode extends Model
{
    use HasFactory;

    protected $table = 'rbd_nodes';

    protected $fillable = [
        'type',
        'block_group_type',
        'block_count',
        'k_value',
        'rbd_block_id',
        'parent_id',
        'rbdidentity_id',
    ];

    /**
     * Relasi ke RbdBlock (jika type = block)
     */
    public function rbdBlock()
    {
        return $this->belongsTo(RbdBlock::class, 'rbd_block_id');
    }

    /**
     * Relasi ke parent node
     */
    public function parent()
    {
        return $this->belongsTo(RbdNode::class, 'parent_id');
    }

    /**
     * Relasi ke child nodes
     */
    public function children()
    {
        return $this->hasMany(RbdNode::class, 'parent_id');
    }
}
