<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // To avoid enum value truncation errors, first expand the enum to include
        // the new values (without removing old legacy values), then update rows,
        // then set the final enum definition.
        if (Schema::hasTable('users')) {
            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                // Temporarily include both new and old values so updates succeed
                DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('super_admin','lab_manager','technician','admin','lab_tech') NOT NULL DEFAULT 'lab_manager'");

                // Map legacy 'technician' to 'lab_tech'
                DB::table('users')->where('role', 'technician')->update(['role' => 'lab_tech']);

                // Now set the final enum (remove 'technician')
                DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('super_admin','admin','lab_manager','lab_tech') NOT NULL DEFAULT 'lab_tech'");
            } else {
                // Non-mysql drivers (sqlite for tests): just map values, skip enum DDL
                DB::table('users')->where('role', 'technician')->update(['role' => 'lab_tech']);
            }
        }
    }

    public function down(): void
    {
        // Reverse the changes: ensure 'lab_tech' exists in the enum before mapping
        if (Schema::hasTable('users')) {
            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                // Temporarily include 'lab_tech' so we can map it back to 'technician'
                DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('super_admin','lab_manager','technician','lab_tech') NOT NULL DEFAULT 'lab_manager'");

                DB::table('users')->where('role', 'lab_tech')->update(['role' => 'technician']);

                // Revert to previous enum values
                DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('super_admin','lab_manager','technician') NOT NULL DEFAULT 'technician'");
            } else {
                // Non-mysql drivers: just map values back
                DB::table('users')->where('role', 'lab_tech')->update(['role' => 'technician']);
            }
        }
    }
};
