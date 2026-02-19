<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller; // Import Base Controller
use App\Models\Item;
use App\Models\ItemBatch;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Laboratory;
use App\Models\StockTransaction;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display Inventory Catalog
     */
    public function index(Request $request)
    {
        $query = Item::query()
            ->where('laboratory_id', Auth::user()->laboratory_id)
            ->with(['category', 'supplier']);

        // Search Logic
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('brand', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }

        // Category Filter
        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        $lab = Laboratory::find(Auth::user()->laboratory_id);

        return Inertia::render('Items/Index', [
            'items' => $query->latest()->paginate(10)->withQueryString(),
            'categories' => Category::where('laboratory_id', Auth::user()->laboratory_id)->get(),
            'filters' => $request->only(['search', 'category']),
            'lab' => $lab
        ]);
    }

    /**
     * Show Item Details (Profile + History + Batches)
     */
    public function show(Item $item)
    {
        if ($item->laboratory_id !== Auth::user()->laboratory_id) abort(403);

        $item->load(['category', 'supplier']);

        // Fetch Batches (FIFO Order)
        $batches = ItemBatch::where('item_id', $item->id)
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();

        // Fetch History (Recent Transactions)
        $history = StockTransaction::with('user')
            ->where('item_id', $item->id)
            ->latest()
            ->limit(20)
            ->get();

        $lab = Laboratory::find(Auth::user()->laboratory_id);

        return Inertia::render('Items/Show', [
            'item' => $item,
            'batches' => $batches,
            'history' => $history,
            'lab' => $lab
        ]);
    }

    /**
     * THE SMART TRANSACTION HANDLER
     * Handles: Add/Deduct Stock, Create Batches, Link POs, Log History
     */
    public function transaction(Request $request, Item $item)
    {
        if ($item->laboratory_id !== Auth::user()->laboratory_id) abort(403);

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:in,out',
            // Only allow the three packaging types requested: box, kit, reagent
            'package_level' => 'nullable|in:box,kit,reagent',
            'min_threshold_unit' => 'nullable|in:unit,kit,box',
            'po_id' => 'nullable|integer',
            'lot_number' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255'
        ]);

        try {
            DB::transaction(function () use ($request, $item) {

                // Convert requested quantity based on packaging level into the model's base unit
                // Base unit mapping:
                // - packaging_type 'box' or 'kit' -> base unit is 'kit' (quantity stored in kits)
                // - packaging_type 'reagent' -> base unit is 'bottle' (quantity stored in bottles)
                $effectiveQty = $request->quantity;
                if ($request->package_level) {
                    if ($request->package_level === 'box' && $item->kits_per_box) {
                        // User provided number of boxes -> convert to kits
                        $effectiveQty = $request->quantity * (int) $item->kits_per_box;
                    } elseif ($request->package_level === 'kit') {
                        // Kit is already the base unit for kit/box items
                        $effectiveQty = $request->quantity;
                    } elseif ($request->package_level === 'reagent') {
                        // Reagent/bottle is the base unit for reagent items: keep as-is (count of bottles)
                        $effectiveQty = $request->quantity;
                    }
                }

                // 1. UPDATE STOCK LEVEL
                if ($request->type === 'in') {
                    $item->increment('quantity', $effectiveQty);

                    // Create New Batch (Only for Incoming)
                    if ($request->lot_number || $request->expiry_date) {
                        ItemBatch::create([
                            'item_id' => $item->id,
                            'laboratory_id' => $item->laboratory_id, // Ensure lab ID is set on batch
                            'lot_number' => $request->lot_number ?? 'Unknown',
                            'expiry_date' => $request->expiry_date,
                            'quantity' => $effectiveQty,
                            'storage_location' => $item->storage_location
                        ]);
                    }

                    // Update Item Cost: store cost as price PER PACKAGING UNIT (box, kit, reagent/bottle)
                    // But record per-base-unit cost in transactions for accounting.
                    $perBaseCost = null;
                    if ($request->cost_per_unit !== null && $request->cost_per_unit >= 0) {
                        $pkg = $request->package_level;

                        if ($pkg === 'box') {
                            // price provided is per-box; save that as the item's package price
                            $item->update(['cost_per_unit' => (float)$request->cost_per_unit]);
                            // compute per-kit cost for accounting (price per box divided by kits per box)
                            if ($item->kits_per_box) {
                                $perBaseCost = ((float)$request->cost_per_unit) / (float)$item->kits_per_box;
                            } else {
                                // if kits_per_box missing, fall back to dividing by quantity
                                $perBaseCost = ((float)$request->cost_per_unit) / max(1, (float)$request->quantity);
                            }
                        } elseif ($pkg === 'kit') {
                            // price per kit: both package price and per-base cost are the same
                            $item->update(['cost_per_unit' => (float)$request->cost_per_unit]);
                            $perBaseCost = (float)$request->cost_per_unit;
                        } elseif ($pkg === 'reagent') {
                            // price per bottle
                            $item->update(['cost_per_unit' => (float)$request->cost_per_unit]);
                            $perBaseCost = (float)$request->cost_per_unit;
                        } else {
                            // Fallback: treat as per-base-unit/package price
                            $item->update(['cost_per_unit' => (float)$request->cost_per_unit]);
                            $perBaseCost = (float)$request->cost_per_unit;
                        }
                    }

                } else {
                    // Outgoing (Deduction)
                    if ($item->quantity < $effectiveQty) {
                        throw new \Exception("Insufficient stock.");
                    }
                    $item->decrement('quantity', $effectiveQty);

                    // Note: Advanced FIFO deduction from specific batches can be added here
                }

                // 2. CREATE TRANSACTION LOG
                StockTransaction::create([
                    'laboratory_id' => Auth::user()->laboratory_id,
                    'item_id' => $item->id,
                    'user_id' => Auth::id(),
                    'type' => $request->type,
                    'quantity' => $effectiveQty,
                    'notes' => trim(($request->notes ?? '') . ' ' . ($request->package_level ? "(as {$request->package_level} x {$request->quantity})" : '')),
                    // Store per-base-unit cost for accurate accounting (e.g., per-kit or per-bottle)
                    'cost_at_time' => $perBaseCost !== null ? $perBaseCost : $item->cost_per_unit
                ]);

                // 3. SMART PO LINKING (Mark specific item as received)
                if ($request->po_id && $request->type === 'in') {
                    $po = PurchaseOrder::find($request->po_id);

                    if ($po && $po->laboratory_id == Auth::user()->laboratory_id) {

                        // Mark the specific line item as received
                        DB::table('purchase_order_items')
                            ->where('purchase_order_id', $po->id)
                            ->where('item_id', $item->id)
                            ->update([
                                    'is_received' => true,
                                    'received_at' => now(),
                                    // Save the per-base-unit cost for the PO line (consistent with StockTransaction.cost_at_time)
                                    'unit_cost' => $perBaseCost ?? ($item->cost_per_unit ?? 0)
                                ]);

                        // Check if the Purchase Order is now fully complete
                        $pendingItems = DB::table('purchase_order_items')
                            ->where('purchase_order_id', $po->id)
                            ->where('is_received', false)
                            ->count();

                        $po->update(['status' => $pendingItems === 0 ? 'received' : 'ordered']);
                    }
                }
            });

            return back()->with('message', 'Stock updated successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Transaction failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Create Item View
     */
    public function create()
    {
        $lab = Laboratory::find(Auth::user()->laboratory_id);

        return Inertia::render('Items/Create', [
            'categories' => Category::where('laboratory_id', Auth::user()->laboratory_id)->get(),
            'suppliers' => Supplier::where('laboratory_id', Auth::user()->laboratory_id)->get(),
            'lab' => $lab
        ]);
    }

    /**
     * Store New Item
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'sku' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            // Only the three allowed packaging types
            'packaging_type' => 'nullable|in:box,kit,reagent',
            'kits_per_box' => 'nullable|integer|min:1',
            'tests_per_kit' => 'nullable|integer|min:1',
            'tests_per_bottle' => 'nullable|integer|min:1',
            'min_threshold' => 'required|integer|min:0',
            'min_threshold_unit' => 'nullable|in:unit,kit,box',
            'quantity' => 'required|integer|min:0', // Initial stock
            'lot_number' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date',
            'storage_location' => 'nullable|string|max:255',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'leaflet' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
        ]);

        $leafletPath = null;
        if ($request->hasFile('leaflet')) {
            $leafletPath = $request->file('leaflet')->store('leaflets', 'public');
        }

        DB::transaction(function () use ($validated, $leafletPath, $request) {
            // Create Item
            $item = new Item($validated);
            $item->laboratory_id = Auth::user()->laboratory_id;
            $item->leaflet_path = $leafletPath;
            // Ensure unit is consistent with packaging_type
            if (isset($validated['packaging_type'])) {
                if ($validated['packaging_type'] === 'box' || $validated['packaging_type'] === 'kit') {
                    $item->unit = 'kit';
                } elseif ($validated['packaging_type'] === 'reagent') {
                    $item->unit = 'bottle';
                }
            }
            $item->quantity = 0; // Updated below
            // ensure min_threshold_unit saved if provided
            if (isset($validated['min_threshold_unit'])) {
                $item->min_threshold_unit = $validated['min_threshold_unit'];
            }
            $item->save();

            // Create First Batch
            if ($validated['quantity'] > 0) {
                // Convert initial quantity depending on packaging_type
                $initialQty = $validated['quantity'];
                if (isset($validated['packaging_type']) && $validated['packaging_type'] === 'box' && !empty($validated['kits_per_box'])) {
                    // User entered number of boxes -> store quantity in kits
                    $initialQty = $validated['quantity'] * (int)$validated['kits_per_box'];
                }

                $batch = ItemBatch::create([
                    'item_id' => $item->id,
                    'laboratory_id' => $item->laboratory_id, // Ensure Lab ID
                    'lot_number' => $request->lot_number,
                    'expiry_date' => $request->expiry_date,
                    'quantity' => $initialQty,
                    'storage_location' => $request->storage_location
                ]);

                $item->quantity = $batch->quantity;
                $item->save();

                // Determine per-base-unit cost for initial stock if a package price was provided
                $initialPerBaseCost = null;
                if (isset($validated['cost_per_unit']) && $validated['cost_per_unit'] !== null) {
                    if (($validated['packaging_type'] ?? null) === 'box' && !empty($validated['kits_per_box'])) {
                        $item->cost_per_unit = (float)$validated['cost_per_unit'];
                        $initialPerBaseCost = ((float)$validated['cost_per_unit']) / (float)$validated['kits_per_box'];
                        $item->save();
                    } else {
                        // kit or reagent: treated as per-base/package price
                        $item->cost_per_unit = (float)$validated['cost_per_unit'];
                        $initialPerBaseCost = (float)$validated['cost_per_unit'];
                        $item->save();
                    }
                }

                // Log Initial Stock (store quantity in base-units); cost_at_time should be per-base-unit
                StockTransaction::create([
                    'laboratory_id' => $item->laboratory_id,
                    'item_id' => $item->id,
                    'user_id' => Auth::id(),
                    'type' => 'in',
                    'quantity' => $batch->quantity,
                    'notes' => 'Initial Stock',
                    'cost_at_time' => $initialPerBaseCost ?? $item->cost_per_unit
                ]);
            }
        });

        return redirect()->route('items.index')->with('message', 'Item created successfully.');
    }

    /**
     * Edit Item View
     */
    public function edit(Item $item)
    {
        if ($item->laboratory_id !== Auth::user()->laboratory_id) abort(403);
        $lab = Laboratory::find(Auth::user()->laboratory_id);
        return Inertia::render('Items/Edit', [
            'item' => $item,
            'categories' => Category::where('laboratory_id', Auth::user()->laboratory_id)->get(),
            'suppliers' => Supplier::where('laboratory_id', Auth::user()->laboratory_id)->get(),
            'lab' => $lab
        ]);
    }

    /**
     * Update Item Details
     */
    public function update(Request $request, Item $item)
    {
        if ($item->laboratory_id !== Auth::user()->laboratory_id) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'sku' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'packaging_type' => 'nullable|in:box,kit,bottle,reagent,single',
            'kits_per_box' => 'nullable|integer|min:1',
            'tests_per_kit' => 'nullable|integer|min:1',
            'tests_per_bottle' => 'nullable|integer|min:1',
            'min_threshold' => 'required|integer|min:0',
            'storage_location' => 'nullable|string|max:255',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'leaflet' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
        ]);

        if ($request->hasFile('leaflet')) {
            if ($item->leaflet_path) {
                Storage::disk('public')->delete($item->leaflet_path);
            }
            $item->leaflet_path = $request->file('leaflet')->store('leaflets', 'public');
            $item->save();
        }

        // Ensure min_threshold_unit allowed on update
        if (isset($validated['min_threshold_unit'])) {
            $item->min_threshold_unit = $validated['min_threshold_unit'];
        }

        // Ensure unit is consistent with packaging_type when updating
        if (isset($validated['packaging_type'])) {
            if ($validated['packaging_type'] === 'box' || $validated['packaging_type'] === 'kit') {
                $validated['unit'] = 'kit';
            } elseif ($validated['packaging_type'] === 'reagent') {
                $validated['unit'] = 'bottle';
            }
        }

        // Prevent updating 'quantity' directly here (must use transaction)
        $item->update($validated);

        return redirect()->route('items.index')->with('message', 'Item updated successfully.');
    }

    /**
     * Delete Item
     */
    public function destroy(Item $item)
    {
        if ($item->laboratory_id !== Auth::user()->laboratory_id) abort(403);
        $item->delete();
        return redirect()->route('items.index')->with('message', 'Item deleted.');
    }

    /**
     * Helper for History Route
     */
    public function history(Item $item)
    {
        return redirect()->route('items.show', $item->id);
    }
}
