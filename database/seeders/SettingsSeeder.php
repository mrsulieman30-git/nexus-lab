<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Laboratory;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        // Ensure a default laboratory exists for seeded data
        $lab = Laboratory::first() ?: Laboratory::create(['name' => 'Demo Lab']);

        $categories = [
            ['name' => 'Chemistry', 'color_hex' => '#1f77b4'],
            ['name' => 'Hematology', 'color_hex' => '#ff7f0e'],
            ['name' => 'Microbiology', 'color_hex' => '#2ca02c'],
            ['name' => 'Immunology', 'color_hex' => '#d62728'],
            ['name' => 'Serology', 'color_hex' => '#9467bd'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate([
                'laboratory_id' => $lab->id,
                'name' => $cat['name']
            ], [
                'color_hex' => $cat['color_hex']
            ]);
        }
    }
}
