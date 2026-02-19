<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FtaEvent extends Model
{
    protected $fillable = ['fta_identity_id', 'fmeca_item_id', 'name', 'failure_rate', 'source'];

    public function ftaIdentity()
    {
        return $this->belongsTo(FtaIdentity::class);
    }

    public function fmecaItem()
    {
        return $this->belongsTo(FmecaItem::class);
    }

    public function ftaNodes()
    {
        return $this->hasMany(FtaNode::class, 'fta_event_id');
    }
}
