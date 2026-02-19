<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Packaging info
            $table->string('packaging_type')->nullable()->after('unit'); // e.g. box, kit, bottle, reagent, single
            $table->integer('kits_per_box')->nullable()->after('packaging_type');
            $table->integer('tests_per_kit')->nullable()->after('kits_per_box');
            $table->integer('tests_per_bottle')->nullable()->after('tests_per_kit');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['packaging_type', 'kits_per_box', 'tests_per_kit', 'tests_per_bottle']);
        });
    }
};
