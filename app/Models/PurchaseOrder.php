<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'laboratory_id',
        'supplier_id',
        'user_id',
        'po_number',
        'status',
        'order_date',
        'expected_date',
        'total_amount',
        'notes'
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
    ];

    /**
     * Global Scope: Ensure users only see POs from their own Laboratory.
     */
    protected static function booted()
    {
        static::addGlobalScope('lab_access', function (Builder $builder) {
            if (Auth::check() && Auth::user()->role !== 'super_admin') {
                $builder->where('laboratory_id', Auth::user()->laboratory_id);
            }
        });
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
