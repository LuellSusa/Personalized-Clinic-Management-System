<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('batch_number')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('supplier')->nullable();
            $table->date('manufacture_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->integer('quantity_received');
            $table->integer('quantity_available');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->date('received_date');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->index(['inventory_item_id', 'status']);
            $table->index(['expiration_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_batches');
    }
};
