<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * This allows us to update these fields securely.
     */
    protected $fillable = [
        'name',
        'license_number',
        'phone',
        'address',
        'logo_path',
        'email',    // <--- Added
        'website',  // <--- Added
        'status'
    ];

    /**
     * Cast JSON preset columns to arrays for easy use in frontend.
     */
    protected $casts = [
        'storage_locations' => 'array',
        'prebuilt_units' => 'array',
        'sample_types' => 'array',
    ];

    /**
     * Appends custom attributes to the JSON output.
     * This allows us to access 'logo_url' directly in Vue.
     */
    protected $appends = ['logo_url'];

    /**
     * Get the full URL for the laboratory logo.
     * Usage in Vue: lab.logo_url
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo_path
            ? asset('storage/' . $this->logo_path)
            : null;
    }

    /**
     * Get the users belonging to this laboratory.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the inventory items for this laboratory.
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
