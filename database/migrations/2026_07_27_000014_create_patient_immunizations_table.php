<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_immunizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('immunization_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('inventory_batch_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('doctor_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->dateTime('administered_at');
            $table->string('dose')->nullable();
            $table->integer('dose_number')->nullable();
            $table->string('route')->nullable();
            $table->string('administration_site')->nullable();
            $table->date('next_due_date')->nullable();
            $table->text('reaction_notes')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['child_id', 'administered_at']);
            $table->index(['immunization_id', 'administered_at']);
            $table->index(['next_due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_immunizations');
    }
};
