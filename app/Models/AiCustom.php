<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiCustom extends Model
{
    use HasFactory;

    protected $table = 'ai_customs';

    protected $fillable = ['keyword', 'description', 'output', 'aicustomspeciality_id']; // Tambah foreign key

    // Relasi ke AiCustomSpeciality
    public function speciality()
    {
        return $this->belongsTo(AiCustomSpeciality::class, 'aicustomspeciality_id');
    }
}
