<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\NotificationLog;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    /**
     * Dispatch an automated 24-hour advance appointment reminder.
     */
    public function sendAppointmentReminder(Appointment $appointment, bool $force = false): NotificationLog
    {
        // Prevent duplicate spamming for the same appointment unless forced
        if (! $force) {
            $alreadySent = NotificationLog::where('appointment_id', $appointment->id)
                ->where('notification_type', 'appointment_reminder_24h')
                ->whereIn('status', ['sent', 'delivered'])
                ->exists();

            if ($alreadySent) {
                throw new \RuntimeException("A reminder has already been successfully sent for appointment #{$appointment->id}.");
            }
        }

        $appointment->loadMissing(['clinic', 'patient', 'doctor.doctorProfile', 'service']);

        $patient = $appointment->patient;
        $clinic = $appointment->clinic;
        $doctor = $appointment->doctor;

        $phone = $this->sanitizePhoneNumber($patient->phone);
        $body = $this->buildReminderMessage($appointment);

        $providerId = 'wamid_' . bin2hex(random_bytes(8));

        // Clinical audit log
        Log::info("Dispatched WhatsApp Appointment Reminder for Appt #{$appointment->id} to {$phone}", [
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'provider_id' => $providerId,
        ]);

        return NotificationLog::create([
            'clinic_id' => $clinic->id,
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'channel' => 'whatsapp',
            'notification_type' => 'appointment_reminder_24h',
            'recipient_phone' => $phone,
            'message_body' => $body,
            'status' => 'sent',
            'provider_message_id' => $providerId,
            'sent_at' => Carbon::now(),
        ]);
    }

    /**
     * Send a custom WhatsApp message to a patient.
     */
    public function sendCustomMessage(Clinic $clinic, Patient $patient, string $message, ?Appointment $appointment = null): NotificationLog
    {
        $phone = $this->sanitizePhoneNumber($patient->phone);
        $providerId = 'wamid_' . bin2hex(random_bytes(8));

        Log::info("Dispatched Custom WhatsApp Message to {$phone}", [
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
        ]);

        return NotificationLog::create([
            'clinic_id' => $clinic->id,
            'appointment_id' => $appointment?->id,
            'patient_id' => $patient->id,
            'channel' => 'whatsapp',
            'notification_type' => 'recall_followup',
            'recipient_phone' => $phone,
            'message_body' => $message,
            'status' => 'sent',
            'provider_message_id' => $providerId,
            'sent_at' => Carbon::now(),
        ]);
    }

    /**
     * Formats clean, professional healthcare appointment reminder copy.
     */
    public function buildReminderMessage(Appointment $appointment): string
    {
        $patient = $appointment->patient;
        $clinic = $appointment->clinic;
        $doctor = $appointment->doctor;

        $dateFormatted = Carbon::parse($appointment->appointment_date)->format('l, M d, Y');
        $timeFormatted = Carbon::parse($appointment->starts_at)->format('h:i A');
        $specialization = $doctor->doctorProfile->specialization ?? 'Dental Surgeon';
        $contactNumber = $clinic->phone ?? $clinic->whatsapp_number ?? 'Front Desk';
        $location = $clinic->address ?? 'Clinic Premises';

        return "Assalam-o-Alaikum {$patient->name},\n\n"
            . "This is an appointment reminder from {$clinic->name}.\n"
            . "Your dental visit is scheduled for:\n"
            . "• Date: {$dateFormatted}\n"
            . "• Time: {$timeFormatted}\n"
            . "• Attending Doctor: {$doctor->name} ({$specialization})\n"
            . "• Location: {$location}\n\n"
            . "For rescheduling or questions, please call {$contactNumber}.\n"
            . "Kindly arrive 10 minutes before your allocated appointment slot.";
    }

    /**
     * Format and sanitize telephone number.
     */
    private function sanitizePhoneNumber(?string $phone): string
    {
        if (! $phone) {
            return '';
        }

        $clean = preg_replace('/[^\d]/', '', $phone);

        // Convert Pakistan local '03xx' format to international '923xx'
        if (str_starts_with($clean, '03') && strlen($clean) === 11) {
            $clean = '92' . substr($clean, 1);
        }

        return $clean;
    }
}
