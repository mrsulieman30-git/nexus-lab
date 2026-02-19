<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    public function definition(): array
    {
        $names = [
            'Glucose Reagent', 'CBC Vials (Purple)', 'Microscope Slides',
            'H. Pylori Kit', 'Cholesterol Reagent', 'Urine Strips (10P)',
            'Safety Gloves (M)', 'Syringes 5ml', 'Ethanol 70%', 'Distilled Water'
        ];

        $units = ['kits', 'boxes', 'bottles', 'pcs', 'packs'];
        $storage = ['Fridge A', 'Fridge B', 'Shelf 1', 'Cupboard C', 'Freezer'];

        return [
            'laboratory_id' => 1, // Assumes Main Lab is ID 1
            'category_id' => $this->faker->numberBetween(1, 3), // Assumes 3 categories exist
            'name' => $this->faker->randomElement($names),
            'sku' => 'MED-' . $this->faker->unique()->numberBetween(1000, 9999),
            'unit' => $this->faker->randomElement($units),
            'quantity' => $this->faker->numberBetween(2, 100),
            'min_threshold' => 15, // If quantity < 15, it will trigger Low Stock
            'expiry_date' => $this->faker->dateTimeBetween('now', '+2 years'),
            'storage_location' => $this->faker->randomElement($storage),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
