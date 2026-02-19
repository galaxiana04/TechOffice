<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicNotulen extends Model
{
    use HasFactory;

    protected $fillable = ['notulen_id', 'title', 'status'];

    public function notulen()
    {
        return $this->belongsTo(Notulen::class);
    }
    public function issueNotulens()
    {
        return $this->hasMany(IssueNotulen::class);
    }


    public function files()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }
}
