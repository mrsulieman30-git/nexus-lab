<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller; // Import Base Controller
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $labId = Auth::user()->laboratory_id;

        // Determine reporting period
        $period = $request->input('period', 'monthly'); // daily, weekly, monthly, yearly
        $year = $request->input('year', date('Y'));

        // Date range for queries
        if ($request->has('start') && $request->has('end')) {
            $start = $request->input('start');
            $end = $request->input('end');
        } else {
            // Default to the full year for the selected year
            $start = "$year-01-01";
            $end = "$year-12-31";
        }

        // Determine SQL grouping / label
        switch ($period) {
            case 'daily':
                $inflowPeriodExpr = "DATE(purchase_order_items.received_at)";
                $outflowPeriodExpr = "DATE(stock_transactions.created_at)";
                $labelFormat = 'Y-m-d';
                break;
            case 'weekly':
                // Use YEAR-WEEK format
                $inflowPeriodExpr = "CONCAT(YEAR(purchase_order_items.received_at), '-', WEEK(purchase_order_items.received_at, 1))";
                $outflowPeriodExpr = "CONCAT(YEAR(stock_transactions.created_at), '-', WEEK(stock_transactions.created_at, 1))";
                $labelFormat = 'o-W';
                break;
            case 'yearly':
                $inflowPeriodExpr = "YEAR(purchase_order_items.received_at)";
                $outflowPeriodExpr = "YEAR(stock_transactions.created_at)";
                $labelFormat = 'Y';
                break;
            case 'monthly':
            default:
                $inflowPeriodExpr = "CONCAT(YEAR(purchase_order_items.received_at), '-', LPAD(MONTH(purchase_order_items.received_at),2,'0'))";
                $outflowPeriodExpr = "CONCAT(YEAR(stock_transactions.created_at), '-', LPAD(MONTH(stock_transactions.created_at),2,'0'))";
                $labelFormat = 'Y-m';
                break;
        }

        // 1. INFLOWS: sum of received purchase order items (actual cost)
        $inflows = DB::table('purchase_order_items')
            ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->where('purchase_orders.laboratory_id', $labId)
            ->where('purchase_order_items.is_received', true)
            ->whereBetween('purchase_order_items.received_at', [$start, $end])
            ->selectRaw("{$inflowPeriodExpr} as period, SUM(purchase_order_items.subtotal) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->pluck('total', 'period')
            ->toArray();

        // 2. OUTFLOWS (usage): based on stock transactions of type 'out' and cost snapshot
        $outflows = DB::table('stock_transactions')
            ->where('stock_transactions.laboratory_id', $labId)
            ->where('stock_transactions.type', 'out')
            ->whereBetween('stock_transactions.created_at', [$start, $end])
            ->selectRaw("{$outflowPeriodExpr} as period, SUM(stock_transactions.quantity * COALESCE(stock_transactions.cost_at_time,0)) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->pluck('total', 'period')
            ->toArray();

        // 3. Supplier Scorecard (same as before)
        $supplierStats = DB::table('purchase_orders')
            ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->where('purchase_orders.laboratory_id', $labId)
            ->where('purchase_orders.status', 'received')
            ->select(
                'suppliers.name',
                DB::raw('COUNT(purchase_orders.id) as orders_count'),
                DB::raw('SUM(purchase_orders.total_amount) as total_spent'),
                DB::raw('MAX(purchase_orders.order_date) as last_order')
            )
            ->groupBy('suppliers.name')
            ->orderByDesc('total_spent')
            ->get();

        // 4. Purchase history recent
        $purchaseHistory = DB::table('purchase_order_items')
            ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('items', 'purchase_order_items.item_id', '=', 'items.id')
            ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->where('purchase_orders.laboratory_id', $labId)
            ->where('purchase_orders.status', 'received')
            ->select(
                'items.name as item',
                'suppliers.name as supplier',
                'purchase_order_items.quantity',
                'purchase_order_items.unit_cost',
                'purchase_orders.order_date'
            )
            ->orderByDesc('purchase_orders.order_date')
            ->limit(50)
            ->get();

        // Total sums for displayed range
        $totalInflows = array_sum($inflows);
        $totalOutflows = array_sum($outflows);

        return Inertia::render('Reports/Financial', [
            'inflows' => $inflows,
            'outflows' => $outflows,
            'supplierStats' => $supplierStats,
            'purchaseHistory' => $purchaseHistory,
            'totalInflows' => $totalInflows,
            'totalOutflows' => $totalOutflows,
            'currentYear' => $year,
            'period' => $period,
            'start' => $start,
            'end' => $end
        ]);
    }
}
