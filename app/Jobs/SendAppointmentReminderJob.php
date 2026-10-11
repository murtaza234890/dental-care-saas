<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\WhatsAppNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAppointmentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times job may be attempted.
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Appointment $appointment,
        public bool $force = false
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WhatsAppNotificationService $notificationService): void
    {
        try {
            $notificationService->sendAppointmentReminder($this->appointment, $this->force);
        } catch (\Throwable $e) {
            Log::error("Failed to dispatch appointment reminder for Appt #{$this->appointment->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            throw $e;
        }
    }
}
