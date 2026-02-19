<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\ItemBatch;
use App\Models\StockTransaction;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'laboratory_id',
        'category_id',
        'supplier_id',

        // Identity
        'name',
        'brand',
        'sku',
        // 'lot_number', // REMOVED: lot_number belongs to Batches now

        // Stock Data
        'unit',
        'quantity',       // Master quantity (sum of batches)
        'min_threshold',
        'min_threshold_unit',

        // Logistics
        // 'expiry_date',    // REMOVED: expiry belongs to Batches now
        'storage_location',

        // Financials
        'cost_per_unit',
        'selling_price',

        // Packaging
        'packaging_type',
        'kits_per_box',
        'tests_per_kit',
        'tests_per_bottle',

        // Scientific / Files
        'leaflet_path',
        'normal_range',
        'standard_concentration',
        'sample_type'
    ];

    protected $casts = [
        'cost_per_unit' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'kits_per_box' => 'integer',
        'tests_per_kit' => 'integer',
        'tests_per_bottle' => 'integer',
    ];

    protected $appends = ['boxes_remaining','kits_remaining','total_kits','total_tests','threshold_kits'];

    /**
     * Number of full boxes remaining based on quantity and kits_per_box.
     */
    public function getBoxesRemainingAttribute()
    {
        if ($this->packaging_type === 'box' && $this->kits_per_box) {
            return intdiv((int)$this->quantity, (int)$this->kits_per_box);
        }
        return 0;
    }

    /**
     * Remaining kits not forming a full box.
     */
    public function getKitsRemainingAttribute()
    {
        if ($this->packaging_type === 'box' && $this->kits_per_box) {
            return (int)$this->quantity % (int)$this->kits_per_box;
        }
        // If item is a kit, all quantity are kits
        if ($this->packaging_type === 'kit' || str_contains($this->unit ?? '', 'kit')) {
            return (int)$this->quantity;
        }
        // For bottles, return total tests if tests_per_bottle defined
        if (($this->packaging_type === 'bottle' || $this->packaging_type === 'reagent') && $this->tests_per_bottle) {
            return (int)$this->quantity * (int)$this->tests_per_bottle;
        }
        return (int)$this->quantity;
    }

    /**
     * Total kits/tests available (normalized to kits/tests where applicable).
     */
    public function getTotalKitsAttribute()
    {
        if ($this->packaging_type === 'box' && $this->kits_per_box) {
            return (int)$this->quantity; // quantity stored in kits in our model
        }
        if ($this->packaging_type === 'kit') {
            return (int)$this->quantity;
        }
        // For reagent/bottle items, total_kits is not applicable. Return quantity (bottles)
        if (($this->packaging_type === 'bottle' || $this->packaging_type === 'reagent')) {
            return (int)$this->quantity;
        }
        return (int)$this->quantity;
    }

    /**
     * Total tests available for reagent/bottle packaging (quantity * tests_per_bottle)
     */
    public function getTotalTestsAttribute()
    {
        if (($this->packaging_type === 'bottle' || $this->packaging_type === 'reagent') && $this->tests_per_bottle) {
            return (int)$this->quantity * (int)$this->tests_per_bottle;
        }
        return null;
    }

    /**
     * Convert the configured min_threshold into kits/tests for comparison.
     */
    public function getThresholdKitsAttribute()
    {
        $unit = $this->min_threshold_unit ?? 'unit';
        $value = (int)$this->min_threshold;
        if ($unit === 'box' && $this->kits_per_box) {
            return $value * (int)$this->kits_per_box;
        }
        return $value;
    }

    /**
     * Global Scope: Ensure users only see items from their own Laboratory.
     */
    protected static function booted()
    {
        static::addGlobalScope('lab_access', function (Builder $builder) {
            if (Auth::check() && Auth::user()->role !== 'super_admin') {
                $builder->where('laboratory_id', Auth::user()->laboratory_id);
            }
        });
    }

    // --- RELATIONSHIPS ---

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function batches()
    {
        return $this->hasMany(ItemBatch::class);
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    // --- ACCESSORS ---

    public function getIsLowStockAttribute()
    {
        return $this->quantity <= $this->min_threshold;
    }
}
