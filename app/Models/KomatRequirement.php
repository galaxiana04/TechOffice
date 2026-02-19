<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomatRequirement extends Model
{
    use HasFactory;

    protected $table = 'komat_requirement';

    protected $fillable = [
        'name',
        'description',
    ];
    public function komatProcessHistories()
    {
        return $this->belongsToMany(
            KomatProcessHistory::class,
            'komat_hist_req',
            'komat_requirement_id',
            'komat_process_history_id'
        )->withTimestamps();
    }
    public function newbomkomats()
    {
        return $this->belongsToMany(Newbomkomat::class, 'komat_requirement_newbomkomat');
    }
    // Relasi ke KomatProcessHistory
    public function processHistories()
    {
        return $this->hasMany(KomatProcessHistory::class, 'komat_requirement_id');
    }
}
