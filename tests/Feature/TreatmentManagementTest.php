<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DentalService;
use App\Models\FollowUp;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\TreatmentRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TreatmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_staff_can_view_treatments_directory(): void
    {
        $clinic = Clinic::create(['name' => 'City Dental Care', 'slug' => 'city-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Ahmed Khan',
            'email' => 'dr.ahmed@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Usman Tariq',
            'phone' => '0300-1234567',
        ]);

        $service = DentalService::create([
            'clinic_id' => $clinic->id,
            'name' => 'Composite Restoration',
            'estimated_duration_minutes' => 45,
            'standard_fee' => 5000,
            'is_active' => true,
        ]);

        TreatmentRecord::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'service_id' => $service->id,
            'tooth_numbers' => '16',
            'diagnosis' => 'Occlusal dental caries',
            'treatment_rendered' => 'Excavation of caries and placement of composite resin',
        ]);

        $response = $this->actingAs($doctor)->get('/treatments');

        $response->assertStatus(200);
        $response->assertSee('Clinical Treatments and Odontogram');
        $response->assertSee('Usman Tariq');
        $response->assertSee('#16');
        $response->assertSee('Composite Restoration');
    }

    public function test_search_and_filters_work_on_treatments_directory(): void
    {
        $clinic = Clinic::create(['name' => 'City Dental Care', 'slug' => 'city-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Ahmed Khan',
            'email' => 'dr.ahmed@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $patient1 = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Patient One',
            'phone' => '0300-1111111',
        ]);

        $patient2 = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Patient Two',
            'phone' => '0300-2222222',
        ]);

        TreatmentRecord::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient1->id,
            'doctor_id' => $doctor->id,
            'tooth_numbers' => '46',
            'diagnosis' => 'Periapical abscess on lower right molar',
            'treatment_rendered' => 'Incision and drainage',
        ]);

        TreatmentRecord::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient2->id,
            'doctor_id' => $doctor->id,
            'tooth_numbers' => '11',
            'diagnosis' => 'Crown fracture upper central incisor',
            'treatment_rendered' => 'Direct composite buildup',
        ]);

        // Filter by tooth
        $response = $this->actingAs($doctor)->get('/treatments?tooth=46');
        $response->assertStatus(200);
        $response->assertSee('Patient One');
        $response->assertDontSee('Patient Two');
    }

    public function test_dentist_can_record_treatment_with_odontogram_and_automated_workflows(): void
    {
        $clinic = Clinic::create(['name' => 'City Dental Care', 'slug' => 'city-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Ahmed Khan',
            'email' => 'dr.ahmed@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Zubair Shah',
            'phone' => '0333-8889990',
        ]);

        $service = DentalService::create([
            'clinic_id' => $clinic->id,
            'name' => 'Root Canal Treatment',
            'estimated_duration_minutes' => 60,
            'standard_fee' => 15000,
            'is_active' => true,
        ]);

        $appointment = Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'service_id' => $service->id,
            'appointment_date' => Carbon::today(),
            'starts_at' => '11:00:00',
            'ends_at' => '12:00:00',
            'status' => 'in_progress',
        ]);

        $recallDate = Carbon::tomorrow()->addDays(7)->format('Y-m-d');

        $response = $this->actingAs($doctor)->post('/treatments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'service_id' => $service->id,
            'appointment_id' => $appointment->id,
            'tooth_numbers' => '16, 21',
            'diagnosis' => 'Irreversible pulpitis secondary to deep recurrent decay',
            'treatment_rendered' => 'Canals located (MB, DB, P), instrumented to size 25/.06, Ca(OH)2 dressing',
            'clinical_notes' => 'Curved mesiobuccal root canal. Patient was given local anesthesia 2% lidocaine.',
            'prescription' => 'Augmentin 625mg 1 tab BID x 5 days',
            // Workflows
            'complete_appointment' => 1,
            'schedule_followup' => 1,
            'followup_due_date' => $recallDate,
            'followup_reason' => 'RCT Step 2: Gutta-percha Obturation',
            'create_invoice' => 1,
        ]);

        $treatment = TreatmentRecord::where('patient_id', $patient->id)->first();
        $this->assertNotNull($treatment);
        $this->assertEquals('16, 21', $treatment->tooth_numbers);

        $response->assertRedirect(route('treatments.show', $treatment->id));

        // 1. Verify Appointment completed
        $this->assertEquals('completed', $appointment->fresh()->status);

        // 2. Verify Follow-Up created
        $followUp = FollowUp::where('treatment_record_id', $treatment->id)->first();
        $this->assertNotNull($followUp);
        $this->assertEquals($recallDate, $followUp->due_date->format('Y-m-d'));
        $this->assertEquals('RCT Step 2: Gutta-percha Obturation', $followUp->reason);
        $this->assertEquals('pending', $followUp->status);

        // 3. Verify Draft Invoice created
        $this->assertDatabaseHas('invoices', [
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'total_amount' => 15000,
            'balance_amount' => 15000,
            'status' => 'unpaid',
        ]);
    }

    public function test_receptionist_cannot_create_or_edit_treatment_records(): void
    {
        $clinic = Clinic::create(['name' => 'City Dental Care', 'slug' => 'city-dental']);
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Ayesha Reception',
            'email' => 'ayesha@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Zubair Shah',
            'phone' => '0333-8889990',
        ]);

        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Ahmed Khan',
            'email' => 'dr.ahmed@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $treatment = TreatmentRecord::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'diagnosis' => 'Gingivitis',
            'treatment_rendered' => 'Scaling',
        ]);

        // Receptionist attempts to access create form
        $createResponse = $this->actingAs($receptionist)->get('/treatments/create');
        $createResponse->assertStatus(403);

        // Receptionist attempts to store treatment
        $storeResponse = $this->actingAs($receptionist)->post('/treatments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'diagnosis' => 'Fake diagnosis',
            'treatment_rendered' => 'Fake procedure',
        ]);
        $storeResponse->assertStatus(403);

        // Receptionist attempts to edit treatment
        $editResponse = $this->actingAs($receptionist)->get("/treatments/{$treatment->id}/edit");
        $editResponse->assertStatus(403);
    }

    public function test_dentist_can_view_create_and_edit_treatment_forms(): void
    {
        $clinic = Clinic::create(['name' => 'City Dental Care', 'slug' => 'city-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Ahmed Khan',
            'email' => 'dr.ahmed@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Usman Tariq',
            'phone' => '0300-1234567',
        ]);

        $treatment = TreatmentRecord::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'tooth_numbers' => '16, 17',
            'diagnosis' => 'Dental caries',
            'treatment_rendered' => 'Composite restoration',
        ]);

        $createResponse = $this->actingAs($doctor)->get('/treatments/create');
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Record Clinical Procedure');

        $editResponse = $this->actingAs($doctor)->get("/treatments/{$treatment->id}/edit");
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Edit Treatment');
    }

    public function test_receptionist_sees_confidential_clinical_notes_masked_in_treatment_show(): void
    {
        $clinic = Clinic::create(['name' => 'City Dental Care', 'slug' => 'city-dental']);
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
            'name' => 'Dr. Ahmed Khan',
            'email' => 'dr.ahmed@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Zubair Shah',
            'phone' => '0333-8889990',
        ]);

        $treatment = TreatmentRecord::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'tooth_numbers' => '36',
            'diagnosis' => 'Apical Periodontitis',
            'treatment_rendered' => 'Canal instrumentation',
            'clinical_notes' => 'Observed bone resorption on periapical radiograph. Low patient tolerance.',
        ]);

        // Receptionist view
        $recResponse = $this->actingAs($receptionist)->get("/treatments/{$treatment->id}");
        $recResponse->assertStatus(200);
        $recResponse->assertSee('Confidential Clinical Record');
        $recResponse->assertDontSee('Observed bone resorption on periapical radiograph. Low patient tolerance.');

        // Dentist view
        $docResponse = $this->actingAs($doctor)->get("/treatments/{$treatment->id}");
        $docResponse->assertStatus(200);
        $docResponse->assertSee('Observed bone resorption on periapical radiograph. Low patient tolerance.');
    }

    public function test_cross_clinic_tenant_isolation_is_enforced_for_treatments(): void
    {
        $clinicA = Clinic::create(['name' => 'Clinic A', 'slug' => 'clinic-a']);
        $clinicB = Clinic::create(['name' => 'Clinic B', 'slug' => 'clinic-b']);

        $doctorA = User::create([
            'clinic_id' => $clinicA->id,
            'name' => 'Doctor A',
            'email' => 'dr.a@clinica.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $patientB = Patient::create([
            'clinic_id' => $clinicB->id,
            'name' => 'Private Patient B',
            'phone' => '0321-0000000',
        ]);

        $treatmentB = TreatmentRecord::create([
            'clinic_id' => $clinicB->id,
            'patient_id' => $patientB->id,
            'doctor_id' => $doctorA->id, // Attempt across clinic
            'diagnosis' => 'Secret Procedure',
            'treatment_rendered' => 'Confidential Surgery',
        ]);

        // Staff from Clinic A attempts to view treatment of Clinic B
        $response = $this->actingAs($doctorA)->get("/treatments/{$treatmentB->id}");
        $response->assertStatus(404);
    }

    public function test_dentist_can_update_treatment_record(): void
    {
        $clinic = Clinic::create(['name' => 'City Dental Care', 'slug' => 'city-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Ahmed Khan',
            'email' => 'dr.ahmed@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Rashid Minhas',
            'phone' => '0300-4445556',
        ]);

        $treatment = TreatmentRecord::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'tooth_numbers' => '11',
            'diagnosis' => 'Incisor chip',
            'treatment_rendered' => 'Smoothed sharp edge',
        ]);

        $response = $this->actingAs($doctor)->put("/treatments/{$treatment->id}", [
            'tooth_numbers' => '11, 21',
            'diagnosis' => 'Incisor chip and minor enamel fracture',
            'treatment_rendered' => 'Composite bonding shades A1 and A2',
            'prescription' => 'None needed',
        ]);

        $response->assertRedirect(route('treatments.show', $treatment->id));

        $this->assertDatabaseHas('treatment_records', [
            'id' => $treatment->id,
            'tooth_numbers' => '11, 21',
            'diagnosis' => 'Incisor chip and minor enamel fracture',
        ]);
    }

    public function test_dentist_can_archive_treatment_record(): void
    {
        $clinic = Clinic::create(['name' => 'City Dental Care', 'slug' => 'city-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Ahmed Khan',
            'email' => 'dr.ahmed@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Rashid Minhas',
            'phone' => '0300-4445556',
        ]);

        $treatment = TreatmentRecord::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'diagnosis' => 'Archival target',
            'treatment_rendered' => 'Procedure to remove',
        ]);

        $response = $this->actingAs($doctor)->delete("/treatments/{$treatment->id}");
        $response->assertRedirect(route('treatments.index'));

        $this->assertDatabaseMissing('treatment_records', ['id' => $treatment->id]);
    }
}
