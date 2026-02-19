<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'laboratory_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'address'
    ];

    // Ensure users only see their own suppliers
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

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
