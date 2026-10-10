<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DentalService;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\TreatmentRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_staff_can_view_patient_directory(): void
    {
        $clinic = Clinic::create(['name' => 'Metro Dental', 'slug' => 'metro-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Haris',
            'email' => 'dr.haris@metrodental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $patient1 = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Zainab Bibi',
            'phone' => '0300-1112223',
            'blood_group' => 'B+',
        ]);

        $patient2 = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Kashif Mehmood',
            'phone' => '0301-4445556',
            'blood_group' => 'O+',
        ]);

        $response = $this->actingAs($doctor)->get('/patients');

        $response->assertStatus(200);
        $response->assertSee('Zainab Bibi');
        $response->assertSee('Kashif Mehmood');
        $response->assertSee('Patients Directory');
    }

    public function test_patient_search_filters_directory_correctly(): void
    {
        $clinic = Clinic::create(['name' => 'Metro Dental', 'slug' => 'metro-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Haris',
            'email' => 'dr.haris@metrodental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Tariq Jameel',
            'phone' => '0321-9998887',
        ]);

        Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Ali Raza',
            'phone' => '0333-1112233',
        ]);

        $response = $this->actingAs($doctor)->get('/patients?search=Tariq');

        $response->assertStatus(200);
        $response->assertSee('Tariq Jameel');
        $response->assertDontSee('Ali Raza');
    }

    public function test_staff_can_register_new_patient(): void
    {
        $clinic = Clinic::create(['name' => 'Metro Dental', 'slug' => 'metro-dental']);
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Sadia Frontdesk',
            'email' => 'sadia@metrodental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $response = $this->actingAs($receptionist)->post('/patients', [
            'name' => 'Farhan Akhtar',
            'phone' => '0345-6677889',
            'email' => 'farhan@example.com',
            'date_of_birth' => '1992-05-14',
            'gender' => 'male',
            'blood_group' => 'AB+',
            'address' => 'House 12, Street 4, Islamabad',
            'medical_history' => 'Asthmatic, allergic to penicillin',
        ]);

        $this->assertDatabaseHas('patients', [
            'clinic_id' => $clinic->id,
            'name' => 'Farhan Akhtar',
            'phone' => '0345-6677889',
            'blood_group' => 'AB+',
        ]);

        $patient = Patient::where('name', 'Farhan Akhtar')->first();
        $response->assertRedirect(route('patients.show', $patient->id));
    }

    public function test_staff_can_view_patient_dossier(): void
    {
        $clinic = Clinic::create(['name' => 'Metro Dental', 'slug' => 'metro-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Haris',
            'email' => 'dr.haris@metrodental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Bilal Aslam',
            'phone' => '0300-8889990',
            'blood_group' => 'A+',
            'medical_history' => 'Severe Penicillin Allergy',
        ]);

        $response = $this->actingAs($doctor)->get("/patients/{$patient->id}");

        $response->assertStatus(200);
        $response->assertSee('Bilal Aslam');
        $response->assertSee('Severe Penicillin Allergy');
        $response->assertSee('Critical Clinical Medical Alert and Drug Allergies');
    }

    public function test_receptionist_cannot_view_confidential_clinical_notes_in_dossier(): void
    {
        $clinic = Clinic::create(['name' => 'Metro Dental', 'slug' => 'metro-dental']);
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Sadia Frontdesk',
            'email' => 'sadia@metrodental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Haris',
            'email' => 'dr.haris@metrodental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Bilal Aslam',
            'phone' => '0300-8889990',
        ]);

        $service = DentalService::create([
            'clinic_id' => $clinic->id,
            'name' => 'Root Canal Treatment',
            'estimated_duration_minutes' => 60,
            'standard_fee' => 12000,
            'is_active' => true,
        ]);

        TreatmentRecord::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'service_id' => $service->id,
            'tooth_numbers' => '16',
            'diagnosis' => 'Irreversible pulpitis',
            'treatment_rendered' => 'Canals prepared and dressed with Ca(OH)2',
            'clinical_notes' => 'Patient expressed severe dental phobia. Observed deep subgingival caries.',
            'prescription' => 'Augmentin 625mg BD x 5 days',
        ]);

        // Receptionist view: Clinical notes must be masked
        $response = $this->actingAs($receptionist)->get("/patients/{$patient->id}");
        $response->assertStatus(200);
        $response->assertSee('Confidential Clinical Record');
        $response->assertDontSee('Patient expressed severe dental phobia. Observed deep subgingival caries.');

        // Dentist view: Clinical notes must be visible
        $doctorResponse = $this->actingAs($doctor)->get("/patients/{$patient->id}");
        $doctorResponse->assertStatus(200);
        $doctorResponse->assertSee('Patient expressed severe dental phobia. Observed deep subgingival caries.');
    }

    public function test_patient_tenant_isolation_is_enforced(): void
    {
        $clinicA = Clinic::create(['name' => 'Clinic A', 'slug' => 'clinic-a']);
        $clinicB = Clinic::create(['name' => 'Clinic B', 'slug' => 'clinic-b']);

        $userA = User::create([
            'clinic_id' => $clinicA->id,
            'name' => 'User A',
            'email' => 'user.a@clinica.test',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);

        $patientB = Patient::create([
            'clinic_id' => $clinicB->id,
            'name' => 'Private Patient B',
            'phone' => '0321-0000000',
        ]);

        // Staff from Clinic A attempts to view patient from Clinic B
        $response = $this->actingAs($userA)->get("/patients/{$patientB->id}");

        // Due to BelongsToClinic global scope, model binding fails or record is not found (404)
        $response->assertStatus(404);
    }

    public function test_staff_can_update_patient_details(): void
    {
        $clinic = Clinic::create(['name' => 'Metro Dental', 'slug' => 'metro-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Haris',
            'email' => 'dr.haris@metrodental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Naveed Qureshi',
            'phone' => '0300-1234567',
        ]);

        $response = $this->actingAs($doctor)->put("/patients/{$patient->id}", [
            'name' => 'Naveed Qureshi Updated',
            'phone' => '0300-7654321',
            'email' => 'naveed@test.com',
            'blood_group' => 'O-',
            'medical_history' => 'Hypertension reported',
        ]);

        $response->assertRedirect(route('patients.show', $patient->id));

        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'name' => 'Naveed Qureshi Updated',
            'phone' => '0300-7654321',
            'blood_group' => 'O-',
            'medical_history' => 'Hypertension reported',
        ]);
    }

    public function test_staff_can_archive_patient(): void
    {
        $clinic = Clinic::create(['name' => 'Metro Dental', 'slug' => 'metro-dental']);
        $admin = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Admin',
            'email' => 'admin@metrodental.test',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Archive Me',
            'phone' => '0300-9999999',
        ]);

        $response = $this->actingAs($admin)->delete("/patients/{$patient->id}");

        $response->assertRedirect(route('patients.index'));
        $this->assertDatabaseMissing('patients', ['id' => $patient->id]);
    }
}
