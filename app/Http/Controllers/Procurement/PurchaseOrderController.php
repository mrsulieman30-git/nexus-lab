<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller; // Import Base Controller
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Added 'items' to the with() clause so the frontend receives the list
        $orders = PurchaseOrder::with(['supplier', 'items'])
            ->where('laboratory_id', Auth::user()->laboratory_id)
            ->latest()
            ->paginate(10);

        return Inertia::render('PurchaseOrders/Index', [
            'orders' => $orders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = \App\Models\Supplier::where('laboratory_id', Auth::user()->laboratory_id)->get();
        $items = \App\Models\Item::where('laboratory_id', Auth::user()->laboratory_id)->get();

        return Inertia::render('PurchaseOrders/Create', [
            'suppliers' => $suppliers,
            'items' => $items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $poNumber = 'PO-' . date('Ymd') . '-' . rand(100, 999);

            $po = PurchaseOrder::create([
                'laboratory_id' => Auth::user()->laboratory_id,
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'po_number' => $poNumber,
                'status' => 'pending',
                'order_date' => $request->order_date,
                'notes' => $request->notes,
                'total_amount' => 0 // Calculated via items usually, or set to 0 initially
            ]);

            foreach ($request->items as $row) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'item_id' => $row['id'],
                    'quantity' => $row['quantity'],
                    'unit_cost' => 0, // Can be updated later upon receipt
                    'subtotal' => 0,
                    'is_received' => false // Initialize as not received
                ]);
            }
        });

        return redirect()->route('purchase-orders.index')->with('message', 'Order Request created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->laboratory_id !== Auth::user()->laboratory_id) abort(403);

        // Load all necessary relationships for the print view
        $purchaseOrder->load(['supplier', 'user', 'items.item']);
        $lab = Auth::user()->laboratory;

        return Inertia::render('PurchaseOrders/Show', [
            'order' => $purchaseOrder,
            'lab' => $lab
        ]);
    }

    /**
     * API: Check for Pending Items (The "Smart Link")
     */
    public function checkPending(Request $request)
    {
        $itemId = $request->query('item_id');

        $orders = PurchaseOrder::where('laboratory_id', Auth::user()->laboratory_id)
            ->whereIn('status', ['pending', 'ordered'])
            // Performance optimization: whereIn with Eloquent builder avoids expensive EXISTS subquery
            ->whereIn('id', \App\Models\PurchaseOrderItem::select('purchase_order_id')
                ->where('item_id', $itemId)
                ->where('is_received', false)
            )
            ->with('supplier')
            ->get();

        return response()->json(['orders' => $orders]);
    }

    /**
     * API: Mark a specific item as received
     */
    public function receiveItem(Request $request, $id)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id'
        ]);

        $po = PurchaseOrder::findOrFail($id);
        if ($po->laboratory_id !== Auth::user()->laboratory_id) abort(403);

        // 1. Mark the specific item row as received
        DB::table('purchase_order_items')
           ->where('purchase_order_id', $po->id)
           ->where('item_id', $request->item_id)
           ->update([
               'is_received' => true,
               'received_at' => now()
           ]);

        // 2. Check if the WHOLE order is now complete
        $pendingCount = DB::table('purchase_order_items')
            ->where('purchase_order_id', $po->id)
            ->where('is_received', false)
            ->count();

        if ($pendingCount === 0) {
            $po->update(['status' => 'received']);
        } else {
            $po->update(['status' => 'ordered']);
        }

        return response()->json(['message' => 'Item updated.']);
    }

    /**
     * Manual Action: Mark Entire Order as Received
     */
    public function markReceived(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->laboratory_id !== Auth::user()->laboratory_id) abort(403);

        // Update all items
        DB::table('purchase_order_items')
            ->where('purchase_order_id', $purchaseOrder->id)
            ->update([
                'is_received' => true,
                'received_at' => now()
            ]);

        $purchaseOrder->update(['status' => 'received']);

        return back()->with('message', 'Purchase Order closed successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->laboratory_id !== Auth::user()->laboratory_id) abort(403);

        // Prevent deleting if already received to preserve financial history
        if ($purchaseOrder->status === 'received') {
            return back()->withErrors(['error' => 'Cannot delete a completed order.']);
        }

        $purchaseOrder->items()->delete(); // Delete line items first
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')->with('message', 'Order deleted.');
    }
}
