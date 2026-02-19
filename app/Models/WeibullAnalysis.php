<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeibullAnalysis extends Model
{
    use HasFactory;

    protected $fillable = ['component_identity_id', 'beta', 'eta', 'b10', 'b25', 'mttf', 'failure_count', 'ttf_data', 'analysis_date'];

    protected $casts = ['ttf_data' => 'array', 'analysis_date' => 'date'];

    public function component()
    {
        return $this->belongsTo(ComponentIdentity::class);
    }
}
