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
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('treatment_record_id')->nullable()->constrained('treatment_records')->nullOnDelete();
            $table->date('due_date');
            $table->string('reason');
            $table->enum('status', ['pending', 'contacted', 'booked', 'cancelled'])->default('pending');
            $table->dateTime('last_contacted_at')->nullable();
            $table->text('staff_notes')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'due_date', 'status']);
            $table->index(['patient_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
