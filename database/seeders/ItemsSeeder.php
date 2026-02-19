<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Laboratory;
use App\Models\Category;

class ItemsSeeder extends Seeder
{
    public function run()
    {
        $lab = Laboratory::first() ?: Laboratory::create(['name' => 'Demo Lab']);
        $category = Category::where('laboratory_id', $lab->id)->first() ?: Category::create(['laboratory_id' => $lab->id, 'name' => 'General', 'color_hex' => '#888888']);

        $items = [
            [
                'name' => 'Glucose Reagent',
                'packaging_type' => 'box',
                'kits_per_box' => 6,
                'tests_per_kit' => 50,
                'unit' => 'kit',
                'quantity' => 12, // in kits
                'cost_per_unit' => 10.00 // package price per box stored here (developer note: for box packaging this is per-box)
            ],
            [
                'name' => 'Hemoglobin Kit',
                'packaging_type' => 'kit',
                'tests_per_kit' => 25,
                'unit' => 'kit',
                'quantity' => 30,
                'cost_per_unit' => 2.50
            ],
            [
                'name' => 'Control Reagent',
                'packaging_type' => 'reagent',
                'tests_per_bottle' => 100,
                'unit' => 'bottle',
                'quantity' => 5,
                'cost_per_unit' => 25.00
            ],
        ];

        foreach ($items as $data) {
            Item::updateOrCreate([
                'laboratory_id' => $lab->id,
                'name' => $data['name']
            ], array_merge($data, ['laboratory_id' => $lab->id, 'category_id' => $category->id]));
        }
    }
}
