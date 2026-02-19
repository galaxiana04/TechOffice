<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proker extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'name',
        'proker_created_at',
        'ishide',
        'ispercentageflexible'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // In app/Models/Proker.php
    public function prokerMonthly()
    {
        return $this->hasMany(ProkerMonthly::class);
    }
}
