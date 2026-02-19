<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueNotulen extends Model
{
    use HasFactory;

    protected $fillable = ['topic_notulen_id', 'issue', 'status'];

    public function topicNotulen()
    {
        return $this->belongsTo(TopicNotulen::class);
    }

    public function solutions()
    {
        return $this->hasMany(SolutionIssue::class);
    }

}
