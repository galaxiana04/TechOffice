<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RbdIdentity extends Model
{
    use HasFactory;

    // Nama tabel (opsional, kalau tidak pakai plural bawaan Laravel)
    protected $table = 'rbdidentity';

    // Kolom yang bisa diisi mass-assignment
    protected $fillable = [
        'componentname',
        'proyek_type_id',
        'time_interval',
        'temporary_reliability_value',

    ];

    /**
     * Relasi ke ProjectType
     * RbdIdentity belongsTo ProjectType
     */
    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'proyek_type_id');
    }
    public function rbdBlocks()
    {
        return $this->hasMany(RbdBlock::class, 'rbdidentity_id');
    }
}
