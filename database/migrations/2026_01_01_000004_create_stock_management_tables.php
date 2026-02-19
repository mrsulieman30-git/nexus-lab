<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Batches (For FIFO/Expiry Tracking)
        Schema::create('item_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('laboratory_id')->constrained()->onDelete('cascade'); // Added for easier filtering
            $table->string('lot_number')->nullable();
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('quantity')->default(0);
            $table->string('storage_location')->nullable();
            $table->timestamps();
        });

        // 2. Transactions (Audit Log)
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laboratory_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->enum('type', ['in', 'out']);
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->decimal('cost_at_time', 10, 2)->nullable(); // Useful for reporting history

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
        Schema::dropIfExists('item_batches');
    }
};
