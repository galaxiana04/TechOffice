<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTackMember extends Model
{
    use HasFactory;

    protected $table = 'subtack_members'; // Sesuaikan dengan nama tabel di database
    protected $fillable = ['name', 'subtack_id'];

    public function newprogressreports()
    {
        return $this->belongsToMany(Newprogressreport::class, 'newprogressreport_subtack_member', 'subtack_member_id', 'newprogressreport_id')
            ->withTimestamps();
    }

    public function subtack()
    {
        return $this->belongsTo(SubTack::class, 'subtack_id');
    }
}
