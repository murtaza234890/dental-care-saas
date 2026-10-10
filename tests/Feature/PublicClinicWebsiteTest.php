<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DoctorProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicClinicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_clinic_website_can_be_rendered_by_slug(): void
    {
        $clinic = Clinic::create([
            'name' => 'Dental Care Studio',
            'slug' => 'dental-care-studio',
            'status' => 'active',
        ]);

        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Zeeshan',
            'email' => 'zeeshan@example.com',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'clinic_id' => $clinic->id,
            'user_id' => $doctor->id,
            'specialization' => 'Cosmetic Dentist',
            'consultation_fee' => 2000.00,
        ]);

        $response = $this->get('/clinic/dental-care-studio');

        $response->assertStatus(200);
        $response->assertSee('Dental Care Studio');
        $response->assertSee('Dr. Zeeshan');
        $response->assertSee('Cosmetic Dentist');
    }

    public function test_unknown_clinic_slug_returns_404(): void
    {
        $response = $this->get('/clinic/non-existent-clinic-slug');

        $response->assertStatus(404);
    }

    public function test_available_slots_endpoint_returns_json_schedule(): void
    {
        $clinic = Clinic::create([
            'name' => 'Alpha Dental',
            'slug' => 'alpha-dental',
            'status' => 'active',
        ]);

        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Alpha',
            'email' => 'alpha@example.com',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'clinic_id' => $clinic->id,
            'user_id' => $doctor->id,
            'specialization' => 'Surgeon',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'slot_duration_minutes' => 30,
            'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
        ]);

        $date = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->getJson("/clinic/alpha-dental/available-slots?doctor_id={$doctor->id}&date={$date}");

        $response->assertStatus(200);
        $response->assertJson([
            'available' => true,
            'doctor' => 'Dr. Alpha',
        ]);
        $response->assertJsonStructure(['slots']);
    }

    public function test_patient_can_book_appointment_from_public_website(): void
    {
        $clinic = Clinic::create([
            'name' => 'Crown Dental',
            'slug' => 'crown-dental',
            'status' => 'active',
        ]);

        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Crown',
            'email' => 'crown@example.com',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'clinic_id' => $clinic->id,
            'user_id' => $doctor->id,
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
            'slot_duration_minutes' => 30,
        ]);

        $date = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->post("/clinic/crown-dental/book", [
            'patient_name' => 'Kashif Mehmood',
            'patient_phone' => '0300-4445555',
            'doctor_id' => $doctor->id,
            'appointment_date' => $date,
            'starts_at' => '11:00',
            'patient_notes' => 'Teeth cleaning and sensitivity checkup',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('patients', [
            'clinic_id' => $clinic->id,
            'name' => 'Kashif Mehmood',
            'phone' => '0300-4445555',
        ]);

        $this->assertDatabaseHas('appointments', [
            'clinic_id' => $clinic->id,
            'doctor_id' => $doctor->id,
            'starts_at' => '11:00:00',
            'status' => 'confirmed',
        ]);
    }

    public function test_conflicting_appointment_slot_is_rejected(): void
    {
        $clinic = Clinic::create([
            'name' => 'Crown Dental',
            'slug' => 'crown-dental',
            'status' => 'active',
        ]);

        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Crown',
            'email' => 'crown@example.com',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'clinic_id' => $clinic->id,
            'user_id' => $doctor->id,
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
            'slot_duration_minutes' => 30,
        ]);

        $date = Carbon::tomorrow()->format('Y-m-d');

        // First patient books 11:00 AM
        $this->post("/clinic/crown-dental/book", [
            'patient_name' => 'Patient One',
            'patient_phone' => '0300-1111111',
            'doctor_id' => $doctor->id,
            'appointment_date' => $date,
            'starts_at' => '11:00',
        ])->assertRedirect();

        // Second patient attempts to book the SAME 11:00 AM slot
        $response = $this->from("/clinic/crown-dental")->post("/clinic/crown-dental/book", [
            'patient_name' => 'Patient Two',
            'patient_phone' => '0300-2222222',
            'doctor_id' => $doctor->id,
            'appointment_date' => $date,
            'starts_at' => '11:00',
        ]);

        $response->assertRedirect("/clinic/crown-dental");
        $response->assertSessionHasErrors(['starts_at']);

        // Assert exactly 1 appointment was saved for that slot
        $this->assertEquals(1, Appointment::where('doctor_id', $doctor->id)->where('starts_at', '11:00:00')->count());
    }
}
