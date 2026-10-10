<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('dental_services')->nullOnDelete();
            $table->date('appointment_date');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->enum('status', [
                'pending',
                'confirmed',
                'checked_in',
                'in_progress',
                'completed',
                'cancelled',
                'no_show'
            ])->default('pending');
            $table->text('patient_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'appointment_date']);
            $table->index(['doctor_id', 'appointment_date', 'starts_at']);
            $table->index(['patient_id', 'appointment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
