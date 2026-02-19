<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newbomkomat extends Model
{
    use HasFactory;

    protected $fillable = ['newbom_id', 'kodematerial', 'material', 'status', 'rev'];

    public function newbom()
    {
        return $this->belongsTo(Newbom::class);
    }

    public function newprogressreports()
    {
        return $this->belongsToMany(Newprogressreport::class, 'newbomkomat_newprogressreport', 'newbomkomat_id', 'newprogressreport_id')
            ->withTimestamps();
    }
    public function histories()
    {
        return $this->hasMany(Newbomkomathistory::class, 'newbomkomat_id');
    }
    public function requirements()
    {
        return $this->belongsToMany(KomatRequirement::class, 'komat_requirement_newbomkomat');
    }
}
