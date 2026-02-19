<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailureRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'component_identity_id',
        'start_date',
        'failure_date',
        'failure_time',
        'ttf_hours',
        'workdays',
        'source_file',
        'service_type',
        'trainset',
        'is_new',
        'train_no',
        'car_type',
        'relation',
        'problemdescription',
        'solution',
        'cause_classification',
    ];

    protected $casts = [
        'start_date' => 'date',
        'failure_date' => 'date',
        'failure_time' => 'datetime:H:i:s',
    ];

    public function component()
    {
        return $this->belongsTo(ComponentIdentity::class);
    }
}
