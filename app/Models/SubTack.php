<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTack extends Model
{
    use HasFactory;

    protected $table = 'subtacks'; // Sesuaikan dengan nama tabel di database
    protected $fillable = ['number', 'tack_id', 'documentnumber'];

    public function tack()
    {
        return $this->belongsTo(Tack::class, 'tack_id');
    }
    public function subtackMembers()
    {
        return $this->hasMany(SubTackMember::class, 'subtack_id');
    }
}
