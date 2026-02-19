<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FmecaItem extends Model
{
    use HasFactory;

    protected $table = 'fmeca_items';

    protected $fillable = [
        'fmeca_part_id',
        'order', // Added
        'item_ref',
        'subsystem',
        'item_name',
        'function',
        'operational_mode',
        'is_safety',
        'failure_mode',
        'failure_causes',
        'failure_base',
        'ratio',
        'failure_rate',
        'items_per_train',
        'data_source',
        'failure_effect_item', // Renamed from failure_effect
        'failure_effect_subsystem', // New field
        'failure_effect_system', // New field

        'reference',
        'safety_risk_severity_class',
        'safety_risk_frequency', // New field
        'safety_risk_level',     // New field
        'reliability_risk_severity_class',
        'reliability_risk_frequency', // New field
        'reliability_risk_level',     // New field
        'failure_detection_means',
        'available_contingency',
        'remarks',
    ];

    public function fmecaPart()
    {
        return $this->belongsTo(FmecaPart::class, 'fmeca_part_id');
    }
    public function ftaEvents()
    {
        return $this->hasMany(FtaEvent::class, 'fmeca_item_id');
    }
}
