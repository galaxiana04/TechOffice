<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewRbdModel extends Model
{
    use HasFactory;

    protected $table = 'new_rbd_models'; // nama tabel

    protected $fillable = [
        'name',
        'description',
        'proyek_type_id',    // tambahkan
        'user_id', // tambahkan
    ];

    // Relasi ke ProjectType
    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'proyek_type_id');
    }
    // === RELASI ===
    public function failureRates()
    {
        return $this->hasMany(NewRbdFailureRate::class, 'new_rbd_model_id');
    }

    public function instances()
    {
        return $this->hasMany(NewRbdInstance::class, 'new_rbd_model_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
