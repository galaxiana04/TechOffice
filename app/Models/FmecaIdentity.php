<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FmecaIdentity extends Model
{
    use HasFactory;

    protected $table = 'fmeca_identities';

    protected $fillable = [
        'project_type_id',
        'name',
        'train_yearly_hours',
    ];

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

    public function fmecaParts()
    {
        return $this->hasMany(FmecaPart::class, 'fmeca_identity_id');
    }
}
