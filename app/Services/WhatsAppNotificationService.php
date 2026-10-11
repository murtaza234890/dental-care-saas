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

        $dispatchResult = $this->dispatchToGateway($phone, $body);

        // Clinical audit log
        Log::info("Dispatched WhatsApp Appointment Reminder for Appt #{$appointment->id} to {$phone}", [
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'provider_id' => $dispatchResult['provider_id'],
            'status' => $dispatchResult['status'],
        ]);

        return NotificationLog::create([
            'clinic_id' => $clinic->id,
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'channel' => 'whatsapp',
            'notification_type' => 'appointment_reminder_24h',
            'recipient_phone' => $phone,
            'message_body' => $body,
            'status' => $dispatchResult['status'],
            'provider_message_id' => $dispatchResult['provider_id'],
            'error_message' => $dispatchResult['error'],
            'sent_at' => Carbon::now(),
        ]);
    }

    /**
     * Send a custom WhatsApp message to a patient.
     */
    public function sendCustomMessage(Clinic $clinic, Patient $patient, string $message, ?Appointment $appointment = null): NotificationLog
    {
        $phone = $this->sanitizePhoneNumber($patient->phone);
        $dispatchResult = $this->dispatchToGateway($phone, $message);

        Log::info("Dispatched Custom WhatsApp Message to {$phone}", [
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'status' => $dispatchResult['status'],
        ]);

        return NotificationLog::create([
            'clinic_id' => $clinic->id,
            'appointment_id' => $appointment?->id,
            'patient_id' => $patient->id,
            'channel' => 'whatsapp',
            'notification_type' => 'recall_followup',
            'recipient_phone' => $phone,
            'message_body' => $message,
            'status' => $dispatchResult['status'],
            'provider_message_id' => $dispatchResult['provider_id'],
            'error_message' => $dispatchResult['error'],
            'sent_at' => Carbon::now(),
        ]);
    }

    /**
     * Generate direct WhatsApp Web / App Click-to-Chat URL for manual 1-click dispatch.
     */
    public function getWhatsAppWebUrl(Appointment $appointment): string
    {
        $appointment->loadMissing(['clinic', 'patient', 'doctor.doctorProfile', 'service']);
        $phone = $this->sanitizePhoneNumber($appointment->patient->phone);
        $body = $this->buildReminderMessage($appointment);

        return "https://wa.me/{$phone}?text=" . rawurlencode($body);
    }

    /**
     * Generate custom direct WhatsApp Web URL for any patient.
     */
    public function getPatientChatUrl(Patient $patient, string $message = ''): string
    {
        $phone = $this->sanitizePhoneNumber($patient->phone);
        $query = $message ? '?text=' . rawurlencode($message) : '';

        return "https://wa.me/{$phone}{$query}";
    }

    /**
     * Dispatch message to external Cloud Gateway if configured, or fallback to simulated log driver.
     */
    protected function dispatchToGateway(string $phone, string $body): array
    {
        // 1. Meta WhatsApp Cloud API (Graph API)
        $metaToken = config('services.whatsapp.cloud_token');
        $metaPhoneId = config('services.whatsapp.phone_number_id');

        if (! empty($metaToken) && ! empty($metaPhoneId)) {
            try {
                $response = \Illuminate\Support\Facades\Http::withToken($metaToken)
                    ->timeout(10)
                    ->post("https://graph.facebook.com/v20.0/{$metaPhoneId}/messages", [
                        'messaging_product' => 'whatsapp',
                        'recipient_type' => 'individual',
                        'to' => $phone,
                        'type' => 'text',
                        'text' => ['preview_url' => false, 'body' => $body],
                    ]);

                if ($response->successful()) {
                    $msgId = $response->json('messages.0.id') ?? ('wamid_' . bin2hex(random_bytes(8)));
                    return ['status' => 'delivered', 'provider_id' => $msgId, 'error' => null];
                }

                return [
                    'status' => 'failed',
                    'provider_id' => null,
                    'error' => 'Meta API Error: ' . $response->body(),
                ];
            } catch (\Throwable $e) {
                return ['status' => 'failed', 'provider_id' => null, 'error' => $e->getMessage()];
            }
        }

        // 2. Twilio WhatsApp API
        $twilioSid = config('services.twilio.sid');
        $twilioToken = config('services.twilio.token');
        $twilioFrom = config('services.twilio.whatsapp_from');

        if (! empty($twilioSid) && ! empty($twilioToken) && ! empty($twilioFrom)) {
            try {
                $response = \Illuminate\Support\Facades\Http::withBasicAuth($twilioSid, $twilioToken)
                    ->timeout(10)
                    ->asForm()
                    ->post("https://api.twilio.com/2010-04-01/Accounts/{$twilioSid}/Messages.json", [
                        'From' => 'whatsapp:' . $twilioFrom,
                        'To' => 'whatsapp:' . (str_starts_with($phone, '+') ? $phone : '+' . $phone),
                        'Body' => $body,
                    ]);

                if ($response->successful()) {
                    return ['status' => 'delivered', 'provider_id' => $response->json('sid'), 'error' => null];
                }

                return [
                    'status' => 'failed',
                    'provider_id' => null,
                    'error' => 'Twilio Error: ' . $response->body(),
                ];
            } catch (\Throwable $e) {
                return ['status' => 'failed', 'provider_id' => null, 'error' => $e->getMessage()];
            }
        }

        // 3. Simulated / Log Mode (Instant local success when no API credentials configured)
        return [
            'status' => 'sent',
            'provider_id' => 'sim_' . bin2hex(random_bytes(8)),
            'error' => null,
        ];
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
