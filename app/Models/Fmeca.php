<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fmeca extends Model
{


    protected $fillable = [
        'project_type_id',
        'fmeca_identity_id',
        'subsystemname',
        'issafetyorisreliability',
        'notifvalue',
    ];
    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }
    public function fmecaIdentity()
    {
        return $this->belongsTo(FmecaIdentity::class);
    }
}
