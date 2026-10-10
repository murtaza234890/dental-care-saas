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
        Schema::create('treatment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('dental_services')->nullOnDelete();
            $table->string('tooth_numbers', 100)->nullable(); // e.g. 11, 21, 46
            $table->text('diagnosis');
            $table->text('treatment_rendered');
            $table->text('clinical_notes')->nullable(); // Confidential dentist notes
            $table->text('prescription')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'patient_id']);
            $table->index(['doctor_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_records');
    }
};
