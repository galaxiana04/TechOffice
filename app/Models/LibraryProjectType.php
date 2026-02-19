<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryProjectType extends Model
{
    protected $table = 'library_project_types';

    protected $fillable = ['title', 'code', 'description', 'is_active'];

    // Relasi ke FileManagement
    public function files()
    {
        return $this->hasMany(FileManagement::class, 'project_id');
    }
}
