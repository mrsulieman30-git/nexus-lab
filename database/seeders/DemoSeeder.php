<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Laboratory;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemBatch;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Create a demo laboratory with presets
        $lab = Laboratory::create([
            'name' => 'Demo Laboratory',
            'license_number' => 'DEM-001',
            'email' => 'demo@lab.test',
            'website' => 'https://demo.lab',
            'phone' => '+1000000000',
            'address' => '123 Demo Street',
            'logo_path' => null,
            'storage_locations' => json_encode(['Fridge 1','Fridge 2','Shelf 1','Shelf 2']),
            'prebuilt_units' => json_encode(['box','kit','bottle','reagent','single']),
            'sample_types' => json_encode(['serum','plasma','whole blood','urine','stool','csf','swab','biopsy','other']),
        ]);

        // Users
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@lab.test',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'laboratory_id' => null
        ]);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@demo.lab',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'laboratory_id' => $lab->id
        ]);

        $manager = User::create([
            'name' => 'Lab Manager',
            'email' => 'manager@demo.lab',
            'password' => Hash::make('password'),
            'role' => 'lab_manager',
            'laboratory_id' => $lab->id
        ]);

        $tech = User::create([
            'name' => 'Lab Tech',
            'email' => 'tech@demo.lab',
            'password' => Hash::make('password'),
            'role' => 'lab_tech',
            'laboratory_id' => $lab->id
        ]);

        // Suppliers
        $sup1 = Supplier::create([
            'laboratory_id' => $lab->id,
            'name' => 'Acme Reagents',
            'contact_person' => 'Jane Doe',
            'email' => 'sales@acme.test',
            'phone' => '+10001111',
            'address' => '1 Supplier Road'
        ]);

        $sup2 = Supplier::create([
            'laboratory_id' => $lab->id,
            'name' => 'Global Kits Ltd',
            'contact_person' => 'John Smith',
            'email' => 'contact@globalkits.test',
            'phone' => '+10002222',
            'address' => '2 Supplier Avenue'
        ]);

        // Categories
        $this->call(CategorySeeder::class);

        // Get categories for items
        $hematology = Category::where('laboratory_id', $lab->id)->where('name', 'Hematology Reagents')->first();
        $chemistry = Category::where('laboratory_id', $lab->id)->where('name', 'Clinical Chemistry Reagents')->first();

        // Items
        // Item 1: Box -> contains kits
        $item1 = Item::create([
            'laboratory_id' => $lab->id,
            'category_id' => $hematology->id,
            'supplier_id' => $sup2->id,
            'name' => 'Complete CBC Kit',
            'brand' => 'Acme Kits',
            'sku' => 'CBC-BOX-001',
            'unit' => 'kit',
            'quantity' => 50, // stored as kits
            'min_threshold' => 15,
            'min_threshold_unit' => 'kit',
            'packaging_type' => 'box',
            'kits_per_box' => 25,
            'tests_per_kit' => 10,
            'storage_location' => 'Fridge 1',
            'cost_per_unit' => 12.50,
            'selling_price' => 20.00,
            'sample_type' => 'whole blood'
        ]);

        ItemBatch::create([
            'laboratory_id' => $lab->id,
            'item_id' => $item1->id,
            'lot_number' => 'LOT-CBC-001',
            'expiry_date' => now()->addMonths(12),
            'quantity' => 50,
            'storage_location' => 'Fridge 1'
        ]);

        // Item 2: Kit standalone
        $item2 = Item::create([
            'laboratory_id' => $lab->id,
            'category_id' => $chemistry->id,
            'supplier_id' => $sup1->id,
            'name' => 'Glucose Reagent Kit',
            'brand' => 'Glux',
            'sku' => 'GLX-KIT-01',
            'unit' => 'kit',
            'quantity' => 30,
            'min_threshold' => 5,
            'min_threshold_unit' => 'kit',
            'packaging_type' => 'kit',
            'tests_per_kit' => 25,
            'storage_location' => 'Shelf 1',
            'cost_per_unit' => 8.00,
            'selling_price' => 15.00,
            'sample_type' => 'serum'
        ]);

        ItemBatch::create([
            'laboratory_id' => $lab->id,
            'item_id' => $item2->id,
            'lot_number' => 'LOT-GLX-01',
            'expiry_date' => now()->addMonths(18),
            'quantity' => 30,
            'storage_location' => 'Shelf 1'
        ]);

        // Item 3: Bottle reagent
        $item3 = Item::create([
            'laboratory_id' => $lab->id,
            'category_id' => $chemistry->id,
            'supplier_id' => $sup1->id,
            'name' => 'Reagent A Bottle',
            'brand' => 'ReagCorp',
            'sku' => 'RAG-BT-01',
            'unit' => 'bottle',
            'quantity' => 10,
            'min_threshold' => 2,
            'min_threshold_unit' => 'unit',
            'packaging_type' => 'bottle',
            'tests_per_bottle' => 100,
            'storage_location' => 'Fridge 2',
            'cost_per_unit' => 50.00,
            'selling_price' => 80.00,
            'sample_type' => 'serum'
        ]);

        ItemBatch::create([
            'laboratory_id' => $lab->id,
            'item_id' => $item3->id,
            'lot_number' => 'LOT-RAG-01',
            'expiry_date' => now()->addMonths(24),
            'quantity' => 10,
            'storage_location' => 'Fridge 2'
        ]);
    }
}
