<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBreakdownStructure extends Model
{
    protected $fillable = [
        'project_type_id',
        'product', // Sudah benar ditambahkan di sini
        'level1',
        'level2',
        'level3',
        'level4', 
        'qty_per_ts',
        'qty_per_system',
        'qty_per_subsystem',
        'total_qty',
        'failure_rate',
        'failure_rate_total',
        'source_note',
        'average_speed_kph',
    ];

    /**
     * Relasi ke ProjectType
     */
    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

    /**
     * Relasi many-to-many ke Newprogressreport
     */
    public function newProgressReports()
    {
        return $this->belongsToMany(NewProgressReport::class, 'newprogressreport_product_breakdown_structure', 'product_breakdown_structure_id', 'newprogressreport_id')
            ->withTimestamps();
    }

    /**
     * Accessor untuk kode lengkap (full_code)
     * DISARANKAN: Tambahkan $this->level4 agar kode yang muncul lengkap 1.2.3.4
     */
    public function getFullCodeAttribute()
    {
        $parts = array_filter([$this->level1, $this->level2, $this->level3, $this->level4]);
        return implode('.', $parts);
    }

    /**
     * Accessor untuk daftar nama dokumen sumber
     */
    public function getSourceDrawingNamesAttribute()
    {
        return $this->newProgressReports->pluck('nodokumen')->implode(', ');
    }
}