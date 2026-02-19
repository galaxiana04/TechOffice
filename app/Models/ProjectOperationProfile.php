<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectOperationProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_type_id',
        'daily_operation_hours',
        'weekly_operation_days'

    ];

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

    public function components()
    {
        return $this->hasMany(ComponentIdentity::class, 'project_operation_profile_id');
    }
}
