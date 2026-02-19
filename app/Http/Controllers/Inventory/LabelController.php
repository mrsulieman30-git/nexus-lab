<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabelController extends Controller
{
    /**
     * Display label or batch selection list.
     */
    public function show(Request $request, Item $item)
    {
        if ($item->laboratory_id !== Auth::user()->laboratory_id) abort(403);

        // 1. If a specific batch is selected, print THAT specific label
        if ($request->has('batch_id')) {
            $batch = ItemBatch::where('id', $request->batch_id)
                ->where('item_id', $item->id)
                ->firstOrFail();

            return view('labels.show', compact('item', 'batch'));
        }

        // 2. Fetch available batches (Stock > 0)
        $batches = $item->batches()
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();

        // 3. Logic Tree
        if ($batches->count() > 1) {
            // SCENARIO A: Multiple batches exist -> Show Selection List
            return view('labels.select', compact('item', 'batches'));
        }
        elseif ($batches->count() === 1) {
            // SCENARIO B: Only one batch exists -> Auto-select it
            $batch = $batches->first();
            return view('labels.show', compact('item', 'batch'));
        }

        // SCENARIO C: No batches (Legacy items) -> Print Generic
        return view('labels.show', compact('item'));
    }
}
