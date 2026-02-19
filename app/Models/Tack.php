<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tack extends Model
{
    use HasFactory;

    protected $fillable = ['number', 'proyek_type_id', 'tack_phase_id'];

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'proyek_type_id');
    }

    public function tackPhase()
    {
        return $this->belongsTo(TackPhase::class);
    }


    public function subtacks()
    {
        return $this->hasMany(SubTack::class, 'tack_id');
    }
}

