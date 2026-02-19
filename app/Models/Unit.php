<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_technology_division'];

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'idunit');
    }
    public function prokers()
    {
        return $this->hasMany(Proker::class);
    }
}
