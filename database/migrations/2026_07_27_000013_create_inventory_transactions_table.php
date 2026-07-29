<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('inventory_batch_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            // The referenced table is created by the next migration, so its
            // foreign key is added in the final constraints migration.
            $table->foreignId('patient_immunization_id')->nullable()->index();
            $table->foreignId('performed_by_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('transaction_type');
            $table->integer('quantity');
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->string('reference_number')->nullable();
            $table->string('reason')->nullable();
            $table->date('transaction_date');
            $table->timestamps();
            $table->index(['inventory_item_id', 'transaction_date']);
            $table->index(['transaction_type', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
