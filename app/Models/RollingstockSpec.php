<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollingstockSpec extends Model
{
    use HasFactory;

    protected $table = 'rollingstock_specs';
    public $timestamps = false; // Jika tabel tidak memiliki created_at dan updated_at

    protected $fillable = [
        'climate',
        'average_temperature',
        'lowest_temperature',
        'highest_temperature',
        'highest_operating_altitude',
        'minimum_horizontal_curve_radius',
        'maximum_sustained_gradient_at_main_line',
        'maximum_sustained_gradient_at_depot',
        'rollingstock_type_id',
        'rollingstock_designation_id',
        'axle_load_of_rollingstock',
        'load_capacity',
        'track_gauge',
        'max_height_of_rollingstock',
        'max_width_of_rollingstock',
        'max_length_of_rollingstock_include_coupler',
        'coupler_height',
        'coupler_type',
        'distance_between_bogie_centers',
        'distance_between_axle',
        'wheel_diameter',
        'floor_height_from_top_of_rail',
        'maximum_design_speed',
        'maximum_operation_speed',
        'acceleration_rate',
        'minimum_deceleration_rate',
        'minimum_emergency_deceleration',
        'bogie_type',
        'brake_system',
        'propulsion_system',
        'suspension_system',
        'carbody_material',
        'air_conditioning_system',
        'other_requirements',
        'proyek_type_id'
    ];

    public function rollingstockType()
    {
        return $this->belongsTo(RollingstockType::class, 'rollingstock_type_id');
    }

    public function rollingstockDesignation()
    {
        return $this->belongsTo(RollingstockDesignation::class, 'rollingstock_designation_id');
    }

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'proyek_type_id');
    }
    public function files()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }
}
