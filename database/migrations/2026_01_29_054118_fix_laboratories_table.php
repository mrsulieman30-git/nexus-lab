<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('laboratories', function (Blueprint $table) {
            // Add 'email' if it doesn't exist
            if (!Schema::hasColumn('laboratories', 'email')) {
                $table->string('email')->nullable()->after('name');
            }
            // Add 'website' if it doesn't exist
            if (!Schema::hasColumn('laboratories', 'website')) {
                $table->string('website')->nullable()->after('email');
            }
            // Add 'status' if it doesn't exist
            if (!Schema::hasColumn('laboratories', 'status')) {
                $table->string('status')->default('active')->after('website');
            }
            // Add 'logo_path' if it doesn't exist
            if (!Schema::hasColumn('laboratories', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('address');
            }
        });
    }

    public function down()
    {
        Schema::table('laboratories', function (Blueprint $table) {
            $table->dropColumn(['email', 'website', 'status', 'logo_path']);
        });
    }
};
