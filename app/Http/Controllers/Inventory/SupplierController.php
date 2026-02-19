<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller; // Import Base Controller
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    /**
     * List all suppliers (For your Split-View Index page).
     */
    public function index()
    {
        // We use get() instead of paginate() because your Vue component
        // handles the search/filtering on the client side.
        $suppliers = Supplier::where('laboratory_id', Auth::user()->laboratory_id)
            ->latest()
            ->get();

        return Inertia::render('Suppliers/Index', [
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Store new supplier.
     */
    public function store(Request $request)
    {
        // Lab technicians cannot create suppliers
        if (Auth::user()->role === config('roles.keys.lab_tech')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        $validated['laboratory_id'] = Auth::user()->laboratory_id;

        Supplier::create($validated);

        return back()->with('message', 'Supplier added successfully.');
    }

    /**
     * Update supplier.
     */
    public function update(Request $request, Supplier $supplier)
    {
        if ($supplier->laboratory_id !== Auth::user()->laboratory_id) abort(403);

        if (Auth::user()->role === config('roles.keys.lab_tech')) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        $supplier->update($validated);

        return back()->with('message', 'Supplier updated successfully.');
    }

    /**
     * Show Supplier Profile (Connecting items to the view).
     */
    public function show(Supplier $supplier)
    {
        if ($supplier->laboratory_id !== Auth::user()->laboratory_id) abort(403);

        // Fetch items linked to this supplier
        $items = Item::where('laboratory_id', Auth::user()->laboratory_id)
            ->where('supplier_id', $supplier->id)
            ->get();

        return Inertia::render('Suppliers/Show', [
            'supplier' => $supplier,
            'items' => $items // <--- This powers your "Supplied Items" table
        ]);
    }

    /**
     * Delete supplier.
     */
    public function destroy(Supplier $supplier)
    {
        if ($supplier->laboratory_id !== Auth::user()->laboratory_id) abort(403);

        if (Auth::user()->role === config('roles.keys.lab_tech')) {
            abort(403, 'Unauthorized.');
        }

        if ($supplier->items()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete: This supplier provides items in your inventory.']);
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('message', 'Supplier deleted.');
    }
}
