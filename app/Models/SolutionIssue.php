<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolutionIssue extends Model
{
    use HasFactory;

    protected $fillable = ['issue_notulen_id', 'followup', 'status', 'deadlinedate', 'pic', 'update'];

    public function issueNotulen()
    {
        return $this->belongsTo(IssueNotulen::class);
    }
}
