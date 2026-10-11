<?php

namespace App\Console\Commands;

use App\Jobs\SendAppointmentReminderJob;
use App\Models\Appointment;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders
                            {--hours=24 : Scan window in hours (default 24h = tomorrow)}
                            {--clinic= : Limit to specific clinic ID}
                            {--sync : Execute immediately in foreground instead of queueing}
                            {--dry-run : Simulate run and preview eligible recipients without sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically scan upcoming appointments and dispatch advance reminders to patients via WhatsApp.';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppNotificationService $notificationService): int
    {
        $hours = (int) $this->option('hours');
        $clinicId = $this->option('clinic');
        $isSync = (bool) $this->option('sync');
        $isDryRun = (bool) $this->option('dry-run');

        // By default for 24 hours, look at tomorrow's schedule
        $daysAhead = max(1, (int) round($hours / 24));
        $targetDate = Carbon::today()->addDays($daysAhead)->format('Y-m-d');

        $this->info("Scanning appointments for target date: [{$targetDate}] (Window: ~{$hours}h)");

        // Query appointments across clinics (or specific clinic)
        $query = Appointment::withoutGlobalScopes()
            ->whereDate('appointment_date', $targetDate)
            ->whereIn('status', ['confirmed', 'pending'])
            ->whereDoesntHave('notificationLogs', function ($q) {
                $q->withoutGlobalScopes()
                    ->where('notification_type', 'appointment_reminder_24h')
                    ->whereIn('status', ['sent', 'delivered', 'queued']);
            })
            ->with(['clinic', 'patient', 'doctor.doctorProfile']);

        if ($clinicId) {
            $query->where('clinic_id', $clinicId);
        }

        $appointments = $query->get();

        if ($appointments->isEmpty()) {
            $this->comment("No pending appointment reminders to dispatch for {$targetDate}.");
            return Command::SUCCESS;
        }

        $this->info("Found {$appointments->count()} eligible appointments needing reminders.");

        $dispatched = 0;
        $failed = 0;

        foreach ($appointments as $appt) {
            $patientName = $appt->patient->name ?? 'Unknown';
            $phone = $appt->patient->phone ?? 'N/A';
            $clinicName = $appt->clinic->name ?? 'Clinic';
            $time = Carbon::parse($appt->starts_at)->format('h:i A');

            if ($isDryRun) {
                $this->line(" • [SIMULATION] Would send WhatsApp reminder to {$patientName} ({$phone}) at {$clinicName} for {$appt->appointment_date->format('Y-m-d')} {$time}");
                $dispatched++;
                continue;
            }

            try {
                if ($isSync) {
                    $notificationService->sendAppointmentReminder($appt);
                    $this->line(" ✓ Sent WhatsApp reminder to {$patientName} ({$phone}) at {$clinicName}");
                } else {
                    SendAppointmentReminderJob::dispatch($appt);
                    $this->line(" ➔ Queued WhatsApp reminder job for {$patientName} ({$phone}) at {$clinicName}");
                }
                $dispatched++;
            } catch (\Throwable $e) {
                $this->error(" ✗ Failed sending reminder for Appt #{$appt->id}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Target Date', $targetDate],
                ['Total Eligible', $appointments->count()],
                ['Dispatched / Queued', $dispatched],
                ['Failed', $failed],
                ['Mode', $isDryRun ? 'DRY-RUN (Simulated)' : ($isSync ? 'Synchronous' : 'Asynchronous Queue')],
            ]
        );

        return Command::SUCCESS;
    }
}
