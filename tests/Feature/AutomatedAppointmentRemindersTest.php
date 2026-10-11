<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DoctorProfile;
use App\Models\NotificationLog;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutomatedAppointmentRemindersTest extends TestCase
{
    use RefreshDatabase;

    private function createClinicEnvironment(): array
    {
        $clinic = Clinic::create([
            'name' => 'Pearl Dental Care',
            'slug' => 'pearl-dental',
            'phone' => '0300-1112233',
            'address' => 'Main Boulevard, Gulberg, Lahore',
        ]);

        $doctorUser = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Arham Khan',
            'email' => 'arham@pearldental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'clinic_id' => $clinic->id,
            'user_id' => $doctorUser->id,
            'specialization' => 'Orthodontist & Implantologist',
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Zubair Tariq',
            'phone' => '0321-9876543',
        ]);

        return [$clinic, $doctorUser, $patient];
    }

    public function test_command_scans_and_dispatches_reminders_for_tomorrow_appointments(): void
    {
        [$clinic, $doctor, $patient] = $this->createClinicEnvironment();

        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        $appointment = Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $tomorrow,
            'starts_at' => '10:30:00',
            'ends_at' => '11:00:00',
            'status' => 'confirmed',
            'patient_notes' => 'Routine scaling and polish',
        ]);

        $this->artisan('appointments:send-reminders --sync')
            ->expectsOutputToContain("Found 1 eligible appointments needing reminders.")
            ->expectsOutputToContain("Sent WhatsApp reminder to Zubair Tariq")
            ->assertSuccessful();

        $this->assertDatabaseHas('notification_logs', [
            'clinic_id' => $clinic->id,
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'channel' => 'whatsapp',
            'notification_type' => 'appointment_reminder_24h',
            'status' => 'sent',
        ]);

        $log = NotificationLog::where('appointment_id', $appointment->id)->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('Pearl Dental Care', $log->message_body);
        $this->assertStringContainsString('Dr. Arham Khan', $log->message_body);
        $this->assertStringContainsString('10:30 AM', $log->message_body);
    }

    public function test_command_prevents_duplicate_reminders_for_same_appointment(): void
    {
        [$clinic, $doctor, $patient] = $this->createClinicEnvironment();

        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        $appointment = Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $tomorrow,
            'starts_at' => '14:00:00',
            'ends_at' => '14:30:00',
            'status' => 'confirmed',
        ]);

        // First execution dispatches reminder
        $this->artisan('appointments:send-reminders --sync')
            ->assertSuccessful();

        $this->assertEquals(1, NotificationLog::where('appointment_id', $appointment->id)->count());

        // Second execution skips duplicate
        $this->artisan('appointments:send-reminders --sync')
            ->expectsOutputToContain('No pending appointment reminders to dispatch')
            ->assertSuccessful();

        $this->assertEquals(1, NotificationLog::where('appointment_id', $appointment->id)->count());
    }

    public function test_command_ignores_cancelled_or_completed_appointments(): void
    {
        [$clinic, $doctor, $patient] = $this->createClinicEnvironment();

        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $tomorrow,
            'starts_at' => '11:00:00',
            'ends_at' => '11:30:00',
            'status' => 'cancelled',
        ]);

        Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $tomorrow,
            'starts_at' => '12:00:00',
            'ends_at' => '12:30:00',
            'status' => 'completed',
        ]);

        $this->artisan('appointments:send-reminders --sync')
            ->expectsOutputToContain('No pending appointment reminders to dispatch')
            ->assertSuccessful();

        $this->assertEquals(0, NotificationLog::count());
    }

    public function test_dry_run_mode_does_not_create_notification_logs(): void
    {
        [$clinic, $doctor, $patient] = $this->createClinicEnvironment();

        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $tomorrow,
            'starts_at' => '16:00:00',
            'ends_at' => '16:30:00',
            'status' => 'confirmed',
        ]);

        $this->artisan('appointments:send-reminders --dry-run')
            ->expectsOutputToContain('[SIMULATION] Would send WhatsApp reminder')
            ->assertSuccessful();

        $this->assertEquals(0, NotificationLog::count());
    }

    public function test_clinic_option_filters_reminders_by_clinic_id(): void
    {
        [$clinicA, $doctorA, $patientA] = $this->createClinicEnvironment();

        $clinicB = Clinic::create(['name' => 'Apex Dental', 'slug' => 'apex-dental']);
        $doctorB = User::create([
            'clinic_id' => $clinicB->id,
            'name' => 'Dr. Bilal',
            'email' => 'bilal@apex.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);
        $patientB = Patient::create([
            'clinic_id' => $clinicB->id,
            'name' => 'Sara Khan',
            'phone' => '0333-1234567',
        ]);

        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        $apptA = Appointment::create([
            'clinic_id' => $clinicA->id,
            'patient_id' => $patientA->id,
            'doctor_id' => $doctorA->id,
            'appointment_date' => $tomorrow,
            'starts_at' => '09:00:00',
            'ends_at' => '09:30:00',
            'status' => 'confirmed',
        ]);

        $apptB = Appointment::create([
            'clinic_id' => $clinicB->id,
            'patient_id' => $patientB->id,
            'doctor_id' => $doctorB->id,
            'appointment_date' => $tomorrow,
            'starts_at' => '09:00:00',
            'ends_at' => '09:30:00',
            'status' => 'confirmed',
        ]);

        // Run command strictly for Clinic A
        $this->artisan("appointments:send-reminders --clinic={$clinicA->id} --sync")
            ->expectsOutputToContain('Found 1 eligible appointments needing reminders')
            ->assertSuccessful();

        $this->assertEquals(1, NotificationLog::where('clinic_id', $clinicA->id)->count());
        $this->assertEquals(0, NotificationLog::where('clinic_id', $clinicB->id)->count());
    }

    public function test_staff_can_manually_trigger_on_demand_reminder_from_schedule(): void
    {
        [$clinic, $doctor, $patient] = $this->createClinicEnvironment();

        $appointment = Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => Carbon::today()->format('Y-m-d'),
            'starts_at' => '15:00:00',
            'ends_at' => '15:30:00',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($doctor)
            ->post("/appointments/{$appointment->id}/send-reminder");

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('notification_logs', [
            'clinic_id' => $clinic->id,
            'appointment_id' => $appointment->id,
            'channel' => 'whatsapp',
            'status' => 'sent',
        ]);
    }

    public function test_staff_cannot_trigger_reminder_for_another_clinics_appointment(): void
    {
        [$clinicA, $doctorA] = $this->createClinicEnvironment();

        $clinicB = Clinic::create(['name' => 'Dental Care Hub', 'slug' => 'dental-care-hub']);
        $doctorB = User::create([
            'clinic_id' => $clinicB->id,
            'name' => 'Dr. Stranger',
            'email' => 'stranger@hub.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);
        $patientB = Patient::create([
            'clinic_id' => $clinicB->id,
            'name' => 'Foreign Patient',
            'phone' => '0300-0000000',
        ]);

        $apptB = Appointment::create([
            'clinic_id' => $clinicB->id,
            'patient_id' => $patientB->id,
            'doctor_id' => $doctorB->id,
            'appointment_date' => Carbon::today()->format('Y-m-d'),
            'starts_at' => '10:00:00',
            'ends_at' => '10:30:00',
            'status' => 'confirmed',
        ]);

        // Doctor A attempts to trigger reminder for Clinic B's appointment
        $response = $this->actingAs($doctorA)
            ->post("/appointments/{$apptB->id}/send-reminder");

        $response->assertNotFound();
        $this->assertEquals(0, NotificationLog::count());
    }

    public function test_follow_up_dashboard_displays_recent_reminder_logs(): void
    {
        [$clinic, $doctor, $patient] = $this->createClinicEnvironment();

        $appointment = Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => Carbon::tomorrow()->format('Y-m-d'),
            'starts_at' => '11:00:00',
            'ends_at' => '11:30:00',
            'status' => 'confirmed',
        ]);

        NotificationLog::create([
            'clinic_id' => $clinic->id,
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'channel' => 'whatsapp',
            'notification_type' => 'appointment_reminder_24h',
            'recipient_phone' => '03219876543',
            'message_body' => 'Assalam-o-Alaikum Zubair Tariq, This is an appointment reminder from Pearl Dental Care.',
            'status' => 'delivered',
            'sent_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($doctor)->get('/follow-ups');

        $response->assertStatus(200);
        $response->assertSee('Automated WhatsApp Reminder Logs');
        $response->assertSee('Zubair Tariq');
        $response->assertSee('03219876543');
        $response->assertSee('delivered');
    }

    public function test_direct_whatsapp_route_redirects_to_wa_me_and_creates_log(): void
    {
        [$clinic, $doctor, $patient] = $this->createClinicEnvironment();

        $appointment = Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => Carbon::tomorrow()->format('Y-m-d'),
            'starts_at' => '11:00:00',
            'ends_at' => '11:30:00',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($doctor)->get("/appointments/{$appointment->id}/whatsapp-direct");

        $response->assertRedirect();
        $this->assertStringContainsString('https://wa.me/923219876543', $response->headers->get('Location'));
        $this->assertStringContainsString('Assalam-o-Alaikum', $response->headers->get('Location'));

        $this->assertDatabaseHas('notification_logs', [
            'clinic_id' => $clinic->id,
            'appointment_id' => $appointment->id,
            'recipient_phone' => '923219876543',
        ]);
    }
}
