<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('items')) {
            return;
        }

        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'min_threshold_unit')) {
                $table->string('min_threshold_unit')->nullable()->default('unit')->after('min_threshold');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('items')) {
            return;
        }

        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'min_threshold_unit')) {
                $table->dropColumn('min_threshold_unit');
            }
        });
    }
};
