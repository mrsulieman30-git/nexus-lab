<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SuppliersSeeder extends Seeder
{
    public function run()
    {
        $suppliers = [
            ['name' => 'Global Labs Supply', 'contact' => 'supplier1@example.com'],
            ['name' => 'BioChem Distributors', 'contact' => 'supplier2@example.com'],
            ['name' => 'MedEquip Wholesale', 'contact' => 'supplier3@example.com'],
        ];

        foreach ($suppliers as $s) {
            Supplier::updateOrCreate(['name' => $s['name']], array_merge($s, ['laboratory_id' => 1]));
        }
    }
}
