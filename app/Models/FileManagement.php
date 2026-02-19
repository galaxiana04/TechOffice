<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileManagement extends Model
{
    use HasFactory;

    protected $table = 'file_management';

    protected $fillable = ['user_id', 'project_id', 'file_name', 'file_code', 'path_file', 'file_link'];


    // Ganti jadi:
    public function libraryProject()
    {
        return $this->belongsTo(LibraryProjectType::class, 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function files()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }
}
