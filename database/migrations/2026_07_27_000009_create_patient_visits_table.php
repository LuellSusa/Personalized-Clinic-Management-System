<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('doctor_user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('visit_date');
            $table->string('reason_for_visit');
            $table->text('symptoms')->nullable();
            $table->decimal('height_cm', 6, 2)->nullable();
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->decimal('temperature_c', 4, 2)->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->integer('systolic_bp')->nullable();
            $table->integer('diastolic_bp')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->text('prescription')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['child_id', 'visit_date']);
            $table->index(['doctor_user_id', 'visit_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_visits');
    }
};
