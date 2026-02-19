<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomatProcess extends Model
{
    use HasFactory;

    protected $table = 'komat_process';

    protected $fillable = [
        'komat_name',
        'komat_id',
    ];

    public function newbomkomat()
    {
        return $this->belongsTo(Newbomkomat::class, 'komat_id');
    }

    public function komatProcessHistories()
    {
        return $this->hasMany(KomatProcessHistory::class, 'komat_process_id');
    }
}
