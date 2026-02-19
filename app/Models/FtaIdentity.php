<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FtaIdentity extends Model
{
    protected $fillable = [
        'componentname',
        'proyek_type_id',
        'cfi',
        'diagram_url'
    ];

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'proyek_type_id');
    }

    public function ftaEvents()
    {
        return $this->hasMany(FtaEvent::class);
    }

    public function ftaNodes()
    {
        return $this->hasMany(FtaNode::class);
    }
}
