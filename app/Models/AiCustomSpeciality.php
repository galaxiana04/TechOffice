<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiCustomSpeciality extends Model
{
    use HasFactory;

    protected $table = 'ai_custom_specialities';

    protected $fillable = ['speciality', 'description']; // Mass Assignment

    // Relasi ke AiCustom
    public function aiCustoms()
    {
        return $this->hasMany(AiCustom::class, 'aicustomspeciality_id');
    }
}
