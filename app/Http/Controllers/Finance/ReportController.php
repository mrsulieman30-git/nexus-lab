<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller; // Import Base Controller
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Item;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the Analytics Dashboard.
     */
    public function index(Request $request)
    {
        $labId = Auth::user()->laboratory_id;
        $unitView = $request->input('unit_view', 'unit'); // unit, kit, box, bottle, reagent

        // 1. INVENTORY VALUATION (Financials)
        // Calculates total money sitting on shelf: SUM(Quantity * Cost Per Unit)
        $valuation = Item::where('laboratory_id', $labId)
            ->selectRaw("SUM(quantity * (CASE WHEN packaging_type = 'box' AND kits_per_box > 0 THEN cost_per_unit / kits_per_box ELSE cost_per_unit END)) as total_value")
            ->value('total_value');

        $totalItems = Item::where('laboratory_id', $labId)->count();
        $totalStockCount = Item::where('laboratory_id', $labId)->sum('quantity');

        // 2. CONSUMPTION TRENDS (Last 3 Months)
        // Prefer daily granularity for recent activity; fallback to monthly if sparse
        $threeMonthsAgo = Carbon::now()->subDays(90);
        $recentCount = StockTransaction::where('laboratory_id', $labId)
            ->where('type', 'out')
            ->where('created_at', '>=', $threeMonthsAgo)
            ->count();

        if ($recentCount >= 20) {
            // Daily totals for last 90 days
            $consumption = StockTransaction::where('laboratory_id', $labId)
                ->where('type', 'out')
                ->where('created_at', '>=', $threeMonthsAgo)
                ->selectRaw('DATE(created_at) as period, SUM(quantity) as total')
                ->groupBy('period')
                ->orderBy('period', 'asc')
                ->get();
        } else {
            // Monthly totals for the last 3 months
            $consumption = StockTransaction::where('laboratory_id', $labId)
                ->where('type', 'out')
                ->where('created_at', '>=', Carbon::now()->subMonths(3))
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, SUM(quantity) as total')
                ->groupBy('period')
                ->orderBy('period', 'asc')
                ->get();
        }

        // 3. CATEGORY DISTRIBUTION
        // E.g., Hematology: 50 items, Microbiology: 20 items
        $categories = Item::where('items.laboratory_id', $labId)
            ->join('categories', 'items.category_id', '=', 'categories.id')
            ->select('categories.name', 'categories.color_hex', DB::raw('count(*) as total'))
            ->groupBy('categories.name', 'categories.color_hex')
            ->get();

        // 4. TOP 5 MOST USED ITEMS
        // Leaderboard of most consumed reagents
        // Top items with conversion to requested view unit
        $rawTop = StockTransaction::where('stock_transactions.laboratory_id', $labId)
            ->where('type', 'out')
            ->join('items', 'stock_transactions.item_id', '=', 'items.id')
            ->select('items.id','items.name','items.packaging_type','items.kits_per_box','items.tests_per_kit','items.tests_per_bottle', DB::raw('SUM(stock_transactions.quantity) as usage_count'))
            ->groupBy('items.id','items.name','items.packaging_type','items.kits_per_box','items.tests_per_kit','items.tests_per_bottle')
            ->orderByDesc('usage_count')
            ->limit(20)
            ->get();

        $topItems = $rawTop->map(function ($row) use ($unitView) {
            $usage = (float) $row->usage_count;
            $display = $usage;
            $label = 'units';

            if ($unitView === 'box' && $row->kits_per_box) {
                $display = $usage / (float)$row->kits_per_box;
                $label = 'boxes';
            } elseif ($unitView === 'kit') {
                $display = $usage; $label = 'kits';
            } elseif (($unitView === 'bottle' || $unitView === 'reagent') && $row->tests_per_bottle) {
                $display = $usage / (float)$row->tests_per_bottle;
                $label = 'bottles';
            } else {
                // fallback to item unit
                $label = $row->packaging_type ?: 'units';
            }

            return [
                'id' => $row->id,
                'name' => $row->name,
                'raw_usage' => $usage,
                'display_usage' => round($display, 2),
                'display_unit' => $label
            ];
        })->sortByDesc('display_usage')->values()->slice(0,5);

        return Inertia::render('Reports/Index', [
            'stats' => [
                'total_items' => $totalItems,
                'total_stock' => $totalStockCount,
                'valuation' => $valuation ?? 0 // Defaults to 0 if null
            ],
            'consumptionData' => $consumption,
            'categoryData' => $categories,
            'topItems' => $topItems,
            'unitView' => $unitView
        ]);
    }

    /**
     * Export Inventory List to CSV (Excel Ready).
     */
    public function export()
    {
        // Fetch all items for this lab
        $items = Item::where('laboratory_id', Auth::user()->laboratory_id)
            ->with(['category', 'supplier'])
            ->get();

        $filename = "nexus-inventory-" . date('Y-m-d') . ".csv";

        // Create a file pointer connected to the output stream
        $handle = fopen('php://output', 'w');

        // Send headers to force download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // 1. Add Column Headers
        fputcsv($handle, [
            'Item Name',
            'Category',
            'Brand',
            'Supplier',
            'Quantity',
            'Unit',
            'Cost Price',
            'Total Value',
            'Location',
            'Expiry Date'
        ]);

        // 2. Add Data Rows
        foreach ($items as $item) {
                $totalValue = $item->quantity * (
                    ($item->packaging_type === 'box' && $item->kits_per_box) ? ($item->cost_per_unit / $item->kits_per_box) : $item->cost_per_unit
                );

                fputcsv($handle, [
                $item->name,
                $item->category->name ?? 'N/A',
                $item->brand,
                $item->supplier->name ?? 'N/A',
                $item->quantity,
                $item->unit,
                    $item->cost_per_unit,
                    $totalValue, // Calculated Total Value (package price converted to base-unit)
                $item->storage_location,
                $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('Y-m-d') : 'N/A' // Use explicit Carbon parsing
            ]);
        }

        fclose($handle);
        exit;
    }
}
