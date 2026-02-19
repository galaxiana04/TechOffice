<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FtaNode extends Model
{
    protected $fillable = [
        'fta_identity_id',
        'type',
        'event_name',
        'parent_id',
        'fta_event_id'
    ];

    public function ftaEvent()
    {
        return $this->belongsTo(FtaEvent::class, 'fta_event_id');
    }

    public function ftaIdentity()
    {
        return $this->belongsTo(FtaIdentity::class, 'fta_identity_id');
    }

    public function parent()
    {
        return $this->belongsTo(FtaNode::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(FtaNode::class, 'parent_id');
    }
}
