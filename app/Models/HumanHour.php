<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HumanHour extends Model
{
    use HasFactory;

    protected $table = 'human_hours'; // Menyesuaikan dengan nama tabel di migration

    protected $fillable = [
        'month',
        'year',
        'proyek_type_id',
        'humanhours',
    ];

    /**
     * Relasi ke model ProjectType.
     */
    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'proyek_type_id');
    }
}
