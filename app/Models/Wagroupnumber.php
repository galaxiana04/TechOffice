<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Wagroupnumber extends Model
{
    use HasFactory;

    protected $fillable = ['groupname', 'number', 'isverified'];
}
