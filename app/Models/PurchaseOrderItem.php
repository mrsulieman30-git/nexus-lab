<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'quantity',
        'unit_cost',
        'subtotal',
        'is_received', // <--- IMPORTANT
        'received_at'  // <--- IMPORTANT
    ];

    protected $casts = [
        'is_received' => 'boolean',
        'received_at' => 'datetime'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
}
