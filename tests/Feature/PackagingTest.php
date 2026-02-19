<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Laboratory;
use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\Category;

class PackagingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Run the seeder or ensure migrations have run via RefreshDatabase
    }

    public function test_box_to_kit_conversion_on_create()
    {
        $lab = Laboratory::factory()->create();
        $user = User::factory()->create(['laboratory_id' => $lab->id]);

        $category = Category::factory()->create(['laboratory_id' => $lab->id]);

        $this->actingAs($user)
            ->post(route('items.store'), [
                'name' => 'Glucose Reagent',
                'category_id' => $category->id,
                'unit' => 'kit',
                'packaging_type' => 'box',
                'kits_per_box' => 6,
                'quantity' => 2,
                'min_threshold' => 1
            ]);

        $item = Item::first();
        $this->assertNotNull($item);
        $this->assertEquals('kit', $item->unit);
        // Quantity should be converted to kits (2 boxes * 6 kits)
        $this->assertEquals(12, $item->quantity);
    }

    public function test_box_cost_divides_per_kit_on_transaction()
    {
        $lab = Laboratory::factory()->create();
        $user = User::factory()->create(['laboratory_id' => $lab->id]);

        $item = Item::create([
            'laboratory_id' => $lab->id,
            'name' => 'Glucose Reagent',
            'unit' => 'kit',
            'packaging_type' => 'box',
            'kits_per_box' => 6,
            'quantity' => 0,
            'min_threshold' => 0
        ]);

        // Add 2 boxes at $10 per box -> total cost $20 -> per-kit cost ~1.6667
        $this->actingAs($user)
            ->post(route('items.transaction', $item->id), [
                'quantity' => 2,
                'type' => 'in',
                'package_level' => 'box',
                'cost_per_unit' => 10
            ]);

        $item->refresh();
        $this->assertEquals(12, $item->quantity);
        // Item.cost_per_unit should store the package price (per-box = 10)
        $this->assertEquals(10, (float)$item->cost_per_unit);

        // The transaction should record per-base-unit cost (per-kit)
        $tx = StockTransaction::where('item_id', $item->id)->where('type', 'in')->latest()->first();
        $this->assertNotNull($tx);
        $this->assertEqualsWithDelta(10 * 2 / 12, (float)$tx->cost_at_time, 0.0001);
    }

    public function test_reagent_total_tests_calculation()
    {
        $lab = Laboratory::factory()->create();
        $user = User::factory()->create(['laboratory_id' => $lab->id]);

        $item = Item::create([
            'laboratory_id' => $lab->id,
            'name' => 'Some Reagent',
            'unit' => 'bottle',
            'packaging_type' => 'reagent',
            'tests_per_bottle' => 10,
            'quantity' => 3,
            'min_threshold' => 0
        ]);

        $this->assertEquals(30, $item->total_tests);
    }
}
