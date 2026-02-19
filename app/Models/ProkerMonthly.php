<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProkerMonthly extends Model
{
    use HasFactory;

    protected $table = 'proker_monthly'; // Menyesuaikan nama tabel

    protected $fillable = [
        'proker_id',
        'date',
        'percentage'
    ];

    public function proker()
    {
        return $this->belongsTo(Proker::class);
    }

    public function files()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }
}