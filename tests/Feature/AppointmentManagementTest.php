<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DoctorProfile;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_staff_can_view_appointments_calendar(): void
    {
        $clinic = Clinic::create(['name' => 'City Dental', 'slug' => 'city-dental']);
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Ayesha Reception',
            'email' => 'ayesha@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $response = $this->actingAs($receptionist)->get('/appointments');

        $response->assertStatus(200);
        $response->assertSee('Daily Schedule');
    }

    public function test_staff_can_schedule_appointment_for_existing_patient(): void
    {
        $clinic = Clinic::create(['name' => 'City Dental', 'slug' => 'city-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Ahmed',
            'email' => 'dr.ahmed@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        DoctorProfile::create(['clinic_id' => $clinic->id, 'user_id' => $doctor->id]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Babar Azam',
            'phone' => '0300-5556667',
        ]);

        $date = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->actingAs($doctor)->post('/appointments', [
            'patient_type' => 'existing',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $date,
            'starts_at' => '14:00',
            'patient_notes' => 'Teeth whitening consultation',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'starts_at' => '14:00:00',
            'status' => 'confirmed',
        ]);
    }

    public function test_staff_can_schedule_appointment_for_new_patient(): void
    {
        $clinic = Clinic::create(['name' => 'City Dental', 'slug' => 'city-dental']);
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Ayesha Reception',
            'email' => 'ayesha@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Ahmed',
            'email' => 'dr.ahmed@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        DoctorProfile::create(['clinic_id' => $clinic->id, 'user_id' => $doctor->id]);

        $date = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->actingAs($receptionist)->post('/appointments', [
            'patient_type' => 'new',
            'patient_name' => 'Shaheen Afridi',
            'patient_phone' => '0321-7778899',
            'doctor_id' => $doctor->id,
            'appointment_date' => $date,
            'starts_at' => '16:30',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('patients', [
            'clinic_id' => $clinic->id,
            'name' => 'Shaheen Afridi',
            'phone' => '0321-7778899',
        ]);

        $this->assertDatabaseHas('appointments', [
            'clinic_id' => $clinic->id,
            'doctor_id' => $doctor->id,
            'starts_at' => '16:30:00',
        ]);
    }

    public function test_staff_can_update_lifecycle_status_to_checked_in_and_completed(): void
    {
        $clinic = Clinic::create(['name' => 'City Dental', 'slug' => 'city-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Ahmed',
            'email' => 'dr.ahmed@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Ali Khan',
            'phone' => '0300-1234567',
        ]);

        $appointment = Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => Carbon::today()->format('Y-m-d'),
            'starts_at' => '10:00:00',
            'ends_at' => '10:30:00',
            'status' => 'confirmed',
        ]);

        // 1. Front Desk Checks In
        $this->actingAs($doctor)->patch("/appointments/{$appointment->id}/status", [
            'status' => 'checked_in',
        ])->assertRedirect();

        $this->assertEquals('checked_in', $appointment->fresh()->status);

        // 2. Doctor Marks Completed
        $this->actingAs($doctor)->patch("/appointments/{$appointment->id}/status", [
            'status' => 'completed',
        ])->assertRedirect();

        $this->assertEquals('completed', $appointment->fresh()->status);
    }

    public function test_reschedule_prevents_conflicts_with_existing_slots(): void
    {
        $clinic = Clinic::create(['name' => 'City Dental', 'slug' => 'city-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Ahmed',
            'email' => 'dr.ahmed@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $patient = Patient::create(['clinic_id' => $clinic->id, 'name' => 'P1', 'phone' => '0300-1']);
        $date = Carbon::tomorrow()->format('Y-m-d');

        // Existing appointment at 11:00 AM
        Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $date,
            'starts_at' => '11:00:00',
            'ends_at' => '11:30:00',
            'status' => 'confirmed',
        ]);

        // Second appointment at 12:00 PM
        $app2 = Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $date,
            'starts_at' => '12:00:00',
            'ends_at' => '12:30:00',
            'status' => 'confirmed',
        ]);

        // Attempting to reschedule App 2 to 11:00 AM (already taken)
        $response = $this->actingAs($doctor)->post("/appointments/{$app2->id}/reschedule", [
            'appointment_date' => $date,
            'starts_at' => '11:00',
        ]);

        $response->assertSessionHasErrors(['reschedule']);
        $this->assertEquals('12:00:00', $app2->fresh()->starts_at);
    }
}
