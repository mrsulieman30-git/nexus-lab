<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laboratory_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('color_hex')->default('#3B82F6');
            $table->timestamps();
        });

        // 2. Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laboratory_id')->constrained()->onDelete('cascade');
            $table->string('name');
            // Merged Fields
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_info')->nullable(); // Legacy field, kept for safety
            $table->timestamps();
        });

        // 3. Items
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laboratory_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');

            // Core Identity
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('sku')->nullable();

            // Stock Data
            $table->string('unit');
            $table->integer('quantity')->default(0);
            $table->integer('min_threshold')->default(10);

            // Logistics Defaults
            $table->string('lot_number')->nullable(); // Default for quick add
            $table->date('expiry_date')->nullable(); // Default for quick add
            $table->string('storage_location')->nullable();

            // Scientific & Docs
            $table->string('leaflet_path')->nullable();
            $table->string('normal_range')->nullable();
            $table->string('standard_concentration')->nullable();
            $table->string('sample_type')->nullable();

            // Financials (Merged)
            $table->decimal('cost_per_unit', 10, 2)->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('categories');
    }
};
