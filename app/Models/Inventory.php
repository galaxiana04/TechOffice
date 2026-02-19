<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'assetcode', 'machinecode', 'inventory_kind_id', 'quantity_total', 'quantity_available'];

    public function kind()
    {
        return $this->belongsTo(InventoryKind::class, 'inventory_kind_id');
    }

    public function loans()
    {
        return $this->hasMany(InventoryLoan::class);
    }
    public function files()
    {
        return $this->morphMany(CollectFile::class, 'collectable');
    }
}
