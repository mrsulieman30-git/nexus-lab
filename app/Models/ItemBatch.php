<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'laboratory_id', // Added for security scope
        'item_id',
        'lot_number',
        'batch_number',
        'expiry_date',
        'quantity',
        'storage_location'
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }
}
