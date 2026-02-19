<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomatSupplier extends Model
{
    use HasFactory;

    protected $table = 'komat_supplier';

    protected $fillable = [
        'name',
        'description',
    ];

    // Relasi ke KomatProcessHistory
    public function processHistories()
    {
        return $this->hasMany(KomatProcessHistory::class, 'komat_supplier_id');
    }
}
