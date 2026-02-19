<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'laboratory_id',
        'item_id',
        'user_id',
        'type', // 'in' or 'out'
        'quantity',
        'notes',
        'cost_at_time' // <--- IMPORTANT: Snapshots price for financial reports
    ];

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
