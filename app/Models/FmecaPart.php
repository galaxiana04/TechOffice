<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FmecaPart extends Model
{
    use HasFactory;

    protected $table = 'fmeca_parts';

    protected $fillable = [
        'fmeca_identity_id',
        'name',
    ];

    public function fmecaIdentity()
    {
        return $this->belongsTo(FmecaIdentity::class, 'fmeca_identity_id');
    }

    public function fmecaItems()
    {
        return $this->hasMany(FmecaItem::class, 'fmeca_part_id');
    }
}
