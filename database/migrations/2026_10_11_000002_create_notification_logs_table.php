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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();

            $table->string('channel', 30)->default('whatsapp'); // whatsapp, sms, email
            $table->string('notification_type', 50)->default('appointment_reminder_24h');
            $table->string('recipient_phone', 50);
            $table->text('message_body');

            $table->enum('status', ['queued', 'sent', 'delivered', 'failed'])->default('sent');
            $table->string('provider_message_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->index(['clinic_id', 'appointment_id', 'notification_type'], 'notif_logs_clinic_appt_type_idx');
            $table->index(['clinic_id', 'status', 'created_at'], 'notif_logs_clinic_status_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
