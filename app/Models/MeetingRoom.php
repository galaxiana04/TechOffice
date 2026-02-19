<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingRoom extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'capacity', 'description', 'is_available'];

    public function events()
    {
        return $this->hasMany(Event::class, 'meeting_room_id');
    }
}
