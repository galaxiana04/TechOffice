<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaNotulen extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'project_type_id'];

    public function notulens()
    {
        return $this->hasMany(Notulen::class);
    }
    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }
}
