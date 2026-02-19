<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('laboratories')) {
            return;
        }

        Schema::table('laboratories', function (Blueprint $table) {
            if (! Schema::hasColumn('laboratories', 'storage_locations')) {
                $table->json('storage_locations')->nullable()->after('logo_path');
            }
            if (! Schema::hasColumn('laboratories', 'prebuilt_units')) {
                $table->json('prebuilt_units')->nullable()->after('storage_locations');
            }
            if (! Schema::hasColumn('laboratories', 'sample_types')) {
                $table->json('sample_types')->nullable()->after('prebuilt_units');
            }
        });
    }

    public function down(): void
    {
        Schema::table('laboratories', function (Blueprint $table) {
            if (Schema::hasColumn('laboratories', 'sample_types')) {
                $table->dropColumn('sample_types');
            }
            if (Schema::hasColumn('laboratories', 'prebuilt_units')) {
                $table->dropColumn('prebuilt_units');
            }
            if (Schema::hasColumn('laboratories', 'storage_locations')) {
                $table->dropColumn('storage_locations');
            }
        });
    }
};
