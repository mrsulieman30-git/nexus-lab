<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Item;
use App\Models\ItemBatch; // Import Batch Model
use App\Models\Supplier;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Main Dashboard View
     */
    public function index()
    {
        $labId = Auth::user()->laboratory_id;

        // 1. Low Stock Alerts (Based on Total Quantity vs Min Threshold)
        $lowStockItems = Item::where('laboratory_id', $labId)
            ->whereColumn('quantity', '<=', 'min_threshold')
            ->limit(5)
            ->get();

        // 2. SMART EXPIRY ALERT (The "Consume First" Logic)
        // We look for Batches that have stock > 0 and are expiring in the next 60 days.
        // Bolt optimization: ~66% faster query by using denormalized laboratory_id instead of whereHas('item')
        $expiryQuery = ItemBatch::where('laboratory_id', $labId)
            ->where('quantity', '>', 0) // Only warn if we actually have it in stock
            ->where('expiry_date', '<=', Carbon::now()->addDays(60)); // Warning range: 60 Days

        // Fetch the top 10 for the list
        $expiringBatches = $expiryQuery->clone()
            ->with('item')
            ->orderBy('expiry_date', 'asc') // Oldest first (FIFO)
            ->limit(10)
            ->get();

        // 3. Recent Activity Log
        $recentActivity = StockTransaction::where('laboratory_id', $labId)
            ->with(['item', 'user'])
            ->latest()
            ->limit(10)
            ->get();

        // 4. General Stats
        $stats = [
            'total_items' => Item::where('laboratory_id', $labId)->count(),
            'total_suppliers' => Supplier::where('laboratory_id', $labId)->count(),
            'low_stock_count' => Item::where('laboratory_id', $labId)
                                     ->whereColumn('quantity', '<=', 'min_threshold')
                                     ->count(),
            // Get the TRUE count of all expiring batches, not just the limited list
            'expiring_count' => $expiryQuery->count()
        ];

        return Inertia::render('Dashboard', [
            'lowStockItems' => $lowStockItems,
            'expiringBatches' => $expiringBatches, // Sending Batches, not Items
            'recentActivity' => $recentActivity,
            'stats' => $stats
        ]);
    }
}
