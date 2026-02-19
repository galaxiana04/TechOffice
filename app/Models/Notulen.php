<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notulen extends Model
{
    use HasFactory;

    protected $fillable = ['number', 'notulen_time_start', 'notulen_time_end', 'place', 'user_id', 'agenda_notulen_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Relasi One-to-Many ke Dokumen terkait Notulen
    // Relasi One-to-Many ke TopicNotulen
    // Relasi One-to-Many ke TopicNotulen
    public function topicnotulens()
    {
        return $this->hasMany(TopicNotulen::class, 'notulen_id');
    }
    public function agendaNotulen()
    {
        return $this->belongsTo(AgendaNotulen::class, 'agenda_notulen_id');
    }
    public function files()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }


}
