<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollingstockDesignation extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function rollingstockSpecs()
    {
        return $this->hasMany(RollingstockSpec::class);
    }
}
