<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DentalService;
use App\Models\DoctorProfile;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicAdministrationTest extends TestCase
{
    use RefreshDatabase;

    private Clinic $clinicA;
    private Clinic $clinicB;
    private User $adminA;
    private User $dentistA;
    private User $receptionistA;
    private User $adminB;

    protected function setUp(): void
    {
        parent::setUp();

        // Clinic A
        $this->clinicA = Clinic::create([
            'name' => 'Islamabad Dental Complex',
            'slug' => 'isb-dental',
            'email' => 'info@isbdental.test',
            'phone' => '051-2223344',
            'currency' => 'PKR',
            'status' => 'active',
        ]);

        $this->adminA = User::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Dr. Admin A',
            'email' => 'admin@isbdental.test',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);

        $this->dentistA = User::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Dr. Associate A',
            'email' => 'associate@isbdental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'clinic_id' => $this->clinicA->id,
            'user_id' => $this->dentistA->id,
            'specialization' => 'Orthodontist',
            'consultation_fee' => 2000.00,
            'working_days' => ['monday', 'tuesday', 'wednesday'],
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
        ]);

        $this->receptionistA = User::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Frontdesk Staff',
            'email' => 'reception@isbdental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        // Clinic B
        $this->clinicB = Clinic::create([
            'name' => 'Lahore Laser Dental',
            'slug' => 'lhr-laser',
            'email' => 'info@lhrlaser.test',
            'phone' => '042-3334455',
            'currency' => 'PKR',
            'status' => 'active',
        ]);

        $this->adminB = User::create([
            'clinic_id' => $this->clinicB->id,
            'name' => 'Dr. Admin B',
            'email' => 'admin@lhrlaser.test',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);
    }

    public function test_clinic_admin_can_view_staff_roster(): void
    {
        $response = $this->actingAs($this->adminA)->get('/admin/staff');

        $response->assertStatus(200);
        $response->assertSeeText('Clinic Staff & Team Access');
        $response->assertSee('Dr. Admin A');
        $response->assertSee('Dr. Associate A');
        $response->assertSee('Frontdesk Staff');
        $response->assertSee('Onboard Staff Member');
    }

    public function test_clinic_admin_can_onboard_new_dentist_and_receptionist(): void
    {
        // 1. Onboard Dentist
        $dentistPayload = [
            'name' => 'Dr. Farhan Qureshi',
            'email' => 'farhan@isbdental.test',
            'phone' => '0300-4445566',
            'role' => 'dentist',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'specialization' => 'Pediatric Dentist',
            'consultation_fee' => 2500.00,
            'slot_duration_minutes' => 30,
        ];

        $resDentist = $this->actingAs($this->adminA)->post('/admin/staff', $dentistPayload);
        $resDentist->assertRedirect('/admin/staff');

        $this->assertDatabaseHas('users', [
            'clinic_id' => $this->clinicA->id,
            'email' => 'farhan@isbdental.test',
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $newDentist = User::where('email', 'farhan@isbdental.test')->first();
        $this->assertNotNull($newDentist->doctorProfile);
        $this->assertEquals('Pediatric Dentist', $newDentist->doctorProfile->specialization);
        $this->assertEquals(2500.00, $newDentist->doctorProfile->consultation_fee);

        // 2. Onboard Receptionist
        $receptionistPayload = [
            'name' => 'Sadia Malik',
            'email' => 'sadia@isbdental.test',
            'phone' => '0300-7778899',
            'role' => 'receptionist',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ];

        $resRec = $this->actingAs($this->adminA)->post('/admin/staff', $receptionistPayload);
        $resRec->assertRedirect('/admin/staff');

        $this->assertDatabaseHas('users', [
            'clinic_id' => $this->clinicA->id,
            'email' => 'sadia@isbdental.test',
            'role' => 'receptionist',
            'is_active' => true,
        ]);
    }

    public function test_admin_cannot_deactivate_or_demote_themselves(): void
    {
        // Attempt to demote self to receptionist
        $resDemote = $this->actingAs($this->adminA)->put("/admin/staff/{$this->adminA->id}", [
            'name' => 'Dr. Admin A',
            'email' => 'admin@isbdental.test',
            'role' => 'receptionist',
            'is_active' => '1',
        ]);

        $resDemote->assertSessionHasErrors('role');

        // Attempt to deactivate self
        $resDeactivate = $this->actingAs($this->adminA)->put("/admin/staff/{$this->adminA->id}", [
            'name' => 'Dr. Admin A',
            'email' => 'admin@isbdental.test',
            'role' => 'clinic_admin',
            'is_active' => '0',
        ]);

        $resDeactivate->assertSessionHasErrors('role');

        // Attempt to toggle status of self
        $resToggle = $this->actingAs($this->adminA)->patch("/admin/staff/{$this->adminA->id}/toggle");
        $resToggle->assertSessionHas('error');

        // Attempt to delete self
        $resDelete = $this->actingAs($this->adminA)->delete("/admin/staff/{$this->adminA->id}");
        $resDelete->assertSessionHas('error');

        $this->adminA->refresh();
        $this->assertTrue($this->adminA->is_active);
        $this->assertEquals('clinic_admin', $this->adminA->role);
    }

    public function test_clinic_admin_can_toggle_staff_status(): void
    {
        $response = $this->actingAs($this->adminA)->patch("/admin/staff/{$this->receptionistA->id}/toggle");
        $response->assertRedirect();

        $this->receptionistA->refresh();
        $this->assertFalse($this->receptionistA->is_active);

        // Toggle back
        $response2 = $this->actingAs($this->adminA)->patch("/admin/staff/{$this->receptionistA->id}/toggle");
        $response2->assertRedirect();

        $this->receptionistA->refresh();
        $this->assertTrue($this->receptionistA->is_active);
    }

    public function test_safe_deactivation_when_staff_has_historical_records(): void
    {
        $patient = Patient::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Test Patient',
            'phone' => '0300-1234567',
        ]);

        $service = DentalService::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Dental Consultation',
            'category' => 'Preventive',
            'standard_fee' => 1500,
            'estimated_duration_minutes' => 30,
            'is_active' => true,
        ]);

        // Link doctor to appointment
        Appointment::create([
            'clinic_id' => $this->clinicA->id,
            'patient_id' => $patient->id,
            'doctor_id' => $this->dentistA->id,
            'service_id' => $service->id,
            'appointment_date' => Carbon::today(),
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addMinutes(30),
            'status' => 'confirmed',
        ]);

        $this->assertTrue($this->dentistA->isReferenced());

        // Attempt deletion
        $response = $this->actingAs($this->adminA)->delete("/admin/staff/{$this->dentistA->id}");
        $response->assertRedirect('/admin/staff');

        // User should still exist, but marked inactive
        $this->assertDatabaseHas('users', [
            'id' => $this->dentistA->id,
            'is_active' => false,
        ]);
    }

    public function test_permanent_deletion_when_staff_is_unreferenced(): void
    {
        $unusedStaff = User::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Temporary Trainee',
            'email' => 'trainee@isbdental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $this->assertFalse($unusedStaff->isReferenced());

        $response = $this->actingAs($this->adminA)->delete("/admin/staff/{$unusedStaff->id}");
        $response->assertRedirect('/admin/staff');

        $this->assertDatabaseMissing('users', [
            'id' => $unusedStaff->id,
        ]);
    }

    public function test_non_admin_cannot_access_staff_management_or_settings(): void
    {
        // Receptionist 403
        $this->actingAs($this->receptionistA)->get('/admin/staff')->assertStatus(403);
        $this->actingAs($this->receptionistA)->get('/admin/staff/create')->assertStatus(403);
        $this->actingAs($this->receptionistA)->get('/admin/settings')->assertStatus(403);

        // Dentist 403
        $this->actingAs($this->dentistA)->get('/admin/staff')->assertStatus(403);
        $this->actingAs($this->dentistA)->get('/admin/staff/create')->assertStatus(403);
        $this->actingAs($this->dentistA)->get('/admin/settings')->assertStatus(403);
    }

    public function test_clinic_admin_can_update_workspace_settings(): void
    {
        $response = $this->actingAs($this->adminA)->get('/admin/settings');
        $response->assertStatus(200);
        $response->assertSeeText('Clinic Workspace Settings');
        $response->assertSee('Islamabad Dental Complex');

        $updatePayload = [
            'name' => 'Islamabad Premier Dental & Orthodontics',
            'slug' => 'isb-premier-dental',
            'phone' => '051-9998877',
            'whatsapp_number' => '923001234567',
            'email' => 'contact@premierdental.pk',
            'address' => 'Plaza 20, Sector F-8 Markaz, Islamabad',
            'emergency_notice' => 'Walk-in emergency consultations available 24/7.',
            'currency' => 'PKR',
            'timezone' => 'Asia/Karachi',
            'status' => 'active',
        ];

        $updateRes = $this->actingAs($this->adminA)->put('/admin/settings', $updatePayload);
        $updateRes->assertRedirect('/admin/settings');
        $updateRes->assertSessionHas('status');

        $this->clinicA->refresh();
        $this->assertEquals('Islamabad Premier Dental & Orthodontics', $this->clinicA->name);
        $this->assertEquals('isb-premier-dental', $this->clinicA->slug);
        $this->assertEquals('923001234567', $this->clinicA->whatsapp_number);
        $this->assertEquals('Walk-in emergency consultations available 24/7.', $this->clinicA->emergency_notice);

        // Verify public portal displays emergency notice
        $publicRes = $this->get('/clinic/isb-premier-dental');
        $publicRes->assertStatus(200);
        $publicRes->assertSee('Islamabad Premier Dental & Orthodontics');
        $publicRes->assertSee('Walk-in emergency consultations available 24/7.');
    }

    public function test_multi_tenant_staff_isolation(): void
    {
        $staffB = User::create([
            'clinic_id' => $this->clinicB->id,
            'name' => 'Secret Clinic B Staff',
            'email' => 'secret@lhrlaser.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        // Admin A viewing staff should not see Clinic B staff
        $response = $this->actingAs($this->adminA)->get('/admin/staff');
        $response->assertDontSee('Secret Clinic B Staff');

        // Admin A cannot edit, update, toggle or delete Clinic B staff
        $this->actingAs($this->adminA)->get("/admin/staff/{$staffB->id}/edit")->assertStatus(404);
        $this->actingAs($this->adminA)->put("/admin/staff/{$staffB->id}", [
            'name' => 'Hacked Name',
            'email' => 'secret@lhrlaser.test',
            'role' => 'receptionist',
        ])->assertStatus(404);
        $this->actingAs($this->adminA)->patch("/admin/staff/{$staffB->id}/toggle")->assertStatus(404);
        $this->actingAs($this->adminA)->delete("/admin/staff/{$staffB->id}")->assertStatus(404);
    }
}
