<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Laboratory;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create the Main Laboratory
        // We use firstOrCreate so it doesn't duplicate if you run it twice
        $lab = Laboratory::firstOrCreate(
            ['name' => 'Nexus Main Lab'],
            [
                'email' => 'info@nexuslab.com',
                'status' => 'active',
                'license_number' => 'NEX-001',
                'address' => 'Bosaso, Somalia',
                'phone' => '+252 90 000 000'
            ]
        );

        // 2. Create the Super Admin User linked to this Lab
        User::updateOrCreate(
            ['email' => 'admin@admin.com'], // The login email
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'), // The login password
                'role' => 'super_admin',
                'laboratory_id' => $lab->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 3. Create a Lab Technician User (Optional, for testing)
        User::updateOrCreate(
            ['email' => 'tech@nexus.com'],
            [
                'name' => 'Lab Technician',
                'password' => Hash::make('password'),
                'role' => 'lab_tech',
                'laboratory_id' => $lab->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
