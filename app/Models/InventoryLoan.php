<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLoan extends Model
{
    use HasFactory;

    protected $fillable = ['inventory_id', 'user_id', 'quantity', 'status', 'borrowed_at', 'returned_at'];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
