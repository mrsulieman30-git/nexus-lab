<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Models\ItemBatch;
use App\Models\Laboratory;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailyStockReport;
use Carbon\Carbon;

class CheckInventory extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'inventory:check';

    /**
     * The console command description.
     */
    protected $description = 'Checks all laboratories for low stock and expiring items, sending email reports.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $laboratories = Laboratory::all();

        foreach ($laboratories as $lab) {
            $this->info("Checking Lab: {$lab->name}...");

            // 1. Check Low Stock
            $lowStock = Item::where('laboratory_id', $lab->id)
                ->whereColumn('quantity', '<=', 'min_threshold')
                ->get();

            // 2. Check Expiring Batches (Next 30 Days)
            // OPTIMIZATION: Replaced expensive `whereHas` subquery with a direct query
            // on the denormalized `laboratory_id` column present on `item_batches`.
            $expiring = ItemBatch::where('laboratory_id', $lab->id)
                ->where('quantity', '>', 0) // Only if we have them
                ->whereDate('expiry_date', '>=', Carbon::now())
                ->whereDate('expiry_date', '<=', Carbon::now()->addDays(30))
                ->with('item')
                ->get();

            // 3. Send Email if there are issues
            if ($lowStock->count() > 0 || $expiring->count() > 0) {

                // Get Lab Managers for this lab
                $managers = User::where('laboratory_id', $lab->id)
                    ->whereIn('role', ['lab_manager', 'super_admin'])
                    ->get();

                foreach ($managers as $manager) {
                    Mail::to($manager->email)->send(new DailyStockReport($lowStock, $expiring, $lab->name));
                    $this->info("  -> Email sent to {$manager->email}");
                }

            } else {
                $this->info("  -> No alerts found.");
            }
        }

        $this->info('Inventory check complete.');
    }
}
