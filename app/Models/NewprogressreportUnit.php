<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewprogressreportUnit extends Model
{
    protected $table = 'newprogressreport_units';

    protected $fillable = ['name', 'description', 'status'];

    public function progressDocumentKinds()
    {
        return $this->hasMany(NewProgressReportDocumentKind::class, 'unit_id');
    }
}
