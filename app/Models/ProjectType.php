<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    use HasFactory;

    protected $table = 'project_types'; // Sesuaikan dengan nama tabel di database
    protected $fillable = ['title', 'project_code', 'vault_link', 'is_active'];

    public function newreports()
    {
        return $this->hasMany(Newreport::class, 'proyek_type_id');
    }
    public function tacks()
    {
        return $this->hasMany(Tack::class, 'project_type_id');
    }
    public function fmecaIdentities()
    {
        return $this->hasMany(FmecaIdentity::class, 'project_type_id');
    }
    public function operationProfile()
    {
        return $this->hasOne(ProjectOperationProfile::class, 'project_type_id');
    }
}
