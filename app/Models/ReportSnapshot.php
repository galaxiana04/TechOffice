<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportSnapshot extends Model
{
    protected $fillable = ['unit', 'data', 'view_name', 'date'];
    protected $casts = [
        'data' => 'array', // Cast JSON ke array secara otomatis
        'date' => 'date:Y-m-d',
    ];
}
