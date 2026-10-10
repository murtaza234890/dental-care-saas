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
        Schema::create('doctor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('specialization')->default('General Dentist');
            $table->decimal('consultation_fee', 10, 2)->default(0.00);
            $table->unsignedSmallInteger('slot_duration_minutes')->default(30);
            $table->text('bio')->nullable();
            $table->json('working_days')->nullable();
            $table->time('start_time')->default('10:00:00');
            $table->time('end_time')->default('20:00:00');
            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'specialization']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_profiles');
    }
};
