<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Laboratory;
use App\Models\StockTransaction;
use App\Models\Item;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $labId = Auth::user()->laboratory_id;
        $lab = Laboratory::find($labId);

        $period = $request->get('period', 'month'); // day, week, month, year
        $startDate = $this->getStartDate($period, $lab->financial_year_start ?? '2026-01-01');

        // Total Valuation
        // Valuation: quantity is stored in base-units (kits or bottles). Item.cost_per_unit stores
        // the package-level price (e.g., per box). Convert package price to per-base-unit when computing valuation.
        $valuation = Item::where('laboratory_id', $labId)
            ->selectRaw("SUM(quantity * (CASE WHEN packaging_type = 'box' AND kits_per_box > 0 THEN cost_per_unit / kits_per_box ELSE cost_per_unit END)) as total")
            ->value('total');
        $valuation = $valuation !== null ? (float) $valuation : 0.0;

        // Incomes (Stock In Costs)
        $incomes = StockTransaction::where('stock_transactions.laboratory_id', $labId)
            ->where('type', 'in')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('SUM(quantity * cost_at_time) as total')
            ->value('total');
        $incomes = $incomes !== null ? (float) $incomes : 0.0;

        // Outcomes (Stock Out Costs)
        $outcomes = StockTransaction::where('stock_transactions.laboratory_id', $labId)
            ->where('type', 'out')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('SUM(quantity * cost_at_time) as total')
            ->value('total');
        $outcomes = $outcomes !== null ? (float) $outcomes : 0.0;

        // Trends
        $rawTrends = StockTransaction::where('stock_transactions.laboratory_id', $labId)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, type, SUM(quantity * cost_at_time) as amount')
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get();

        // Normalize trends into an array keyed by date with numeric amounts for 'in' and 'out'
        $trends = $rawTrends->groupBy('date')->mapWithKeys(function ($group, $date) {
            $in = $group->where('type', 'in')->sum(fn($r) => (float) $r->amount);
            $out = $group->where('type', 'out')->sum(fn($r) => (float) $r->amount);
            return [$date => ['in' => $in, 'out' => $out]];
        })->toArray();

        // Top Expenses
        $topExpensesRaw = StockTransaction::where('stock_transactions.laboratory_id', $labId)
            ->where('type', 'out')
            ->where('stock_transactions.created_at', '>=', $startDate)
            ->join('items', 'stock_transactions.item_id', '=', 'items.id')
            ->select('items.name', DB::raw('SUM(stock_transactions.quantity * stock_transactions.cost_at_time) as cost'))
            ->groupBy('items.name')
            ->orderByDesc('cost')
            ->limit(10)
            ->get();

        $topExpenses = $topExpensesRaw->map(fn($r) => ['name' => $r->name, 'cost' => (float) $r->cost])->toArray();

        return Inertia::render('Finance/Index', [
            'lab' => $lab,
            'valuation' => $valuation,
            'incomes' => $incomes,
            'outcomes' => $outcomes,
            'trends' => $trends,
            'topExpenses' => $topExpenses,
            'period' => $period
        ]);
    }

    public function report(Request $request)
    {
        $labId = Auth::user()->laboratory_id;
        $lab = Laboratory::find($labId);
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period, $lab->financial_year_start ?? '2026-01-01');

        $data = [
            'lab' => $lab,
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => now(),
            'valuation' => Item::where('laboratory_id', $labId)->selectRaw("SUM(quantity * (CASE WHEN packaging_type = 'box' AND kits_per_box > 0 THEN cost_per_unit / kits_per_box ELSE cost_per_unit END)) as total")->value('total') ?? 0,
            'incomes' => StockTransaction::where('stock_transactions.laboratory_id', $labId)->where('type', 'in')->where('created_at', '>=', $startDate)->selectRaw('SUM(quantity * cost_at_time) as total')->value('total') ?? 0,
            'outcomes' => StockTransaction::where('stock_transactions.laboratory_id', $labId)->where('type', 'out')->where('created_at', '>=', $startDate)->selectRaw('SUM(quantity * cost_at_time) as total')->value('total') ?? 0,
            'transactions' => StockTransaction::with('item')->where('stock_transactions.laboratory_id', $labId)->where('created_at', '>=', $startDate)->orderBy('created_at')->get()
        ];

        $pdf = PDF::loadView('pdf.financial-report', $data);
        return $pdf->download('financial-report-' . $period . '.pdf');
    }

    private function getStartDate($period, $fyStart)
    {
        $fyStart = Carbon::parse($fyStart);
        $now = Carbon::now();

        switch ($period) {
            case 'day':
                return $now->startOfDay();
            case 'week':
                return $now->startOfWeek();
            case 'month':
                return $now->startOfMonth();
            case 'year':
                return $fyStart->year($now->year);
            default:
                return $now->startOfMonth();
        }
    }
}
