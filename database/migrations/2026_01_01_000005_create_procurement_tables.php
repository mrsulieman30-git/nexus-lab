<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Purchase Order Header
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laboratory_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();

            $table->string('po_number')->unique();
            $table->enum('status', ['pending', 'ordered', 'received', 'cancelled'])->default('pending');
            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Purchase Order Items
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained();

            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);

            // Merged Fields (Smart Receiving)
            $table->boolean('is_received')->default(false);
            $table->date('received_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
