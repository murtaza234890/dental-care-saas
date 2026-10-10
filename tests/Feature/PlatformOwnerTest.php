<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DentalService;
use App\Models\DoctorProfile;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformOwnerTest extends TestCase
{
    use RefreshDatabase;

    private User $platformOwner;
    private Clinic $clinicA;
    private Clinic $clinicB;
    private User $adminA;
    private User $dentistA;
    private User $receptionistA;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Platform Super Owner
        $this->platformOwner = User::create([
            'name' => 'SaaS Platform Owner',
            'email' => 'admin@dentalcare.test',
            'password' => bcrypt('password'),
            'role' => 'platform_owner',
            'is_active' => true,
        ]);

        // 2. Clinic A
        $this->clinicA = Clinic::create([
            'name' => 'City Dental & Implant Centre',
            'slug' => 'city-dental',
            'email' => 'info@citydental.pk',
            'phone' => '0300-1112233',
            'currency' => 'PKR',
            'status' => 'active',
        ]);

        $this->adminA = User::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Dr. Ahmed Khan',
            'email' => 'ahmed@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);

        $this->dentistA = User::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Dr. Sara Ali',
            'email' => 'sara@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'clinic_id' => $this->clinicA->id,
            'user_id' => $this->dentistA->id,
            'specialization' => 'Orthodontist',
            'consultation_fee' => 1500.00,
            'working_days' => ['monday', 'wednesday', 'friday'],
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
        ]);

        $this->receptionistA = User::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Ayesha Frontdesk',
            'email' => 'ayesha@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        // 3. Clinic B
        $this->clinicB = Clinic::create([
            'name' => 'Apex Aesthetic Dental',
            'slug' => 'apex-aesthetic',
            'email' => 'info@apexdental.pk',
            'phone' => '042-35556677',
            'currency' => 'PKR',
            'status' => 'suspended',
        ]);

        User::create([
            'clinic_id' => $this->clinicB->id,
            'name' => 'Dr. Bilal Qureshi',
            'email' => 'bilal@apexdental.test',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);
    }

    public function test_platform_owner_can_view_all_clinics_directory(): void
    {
        $response = $this->actingAs($this->platformOwner)->get('/platform/clinics');

        $response->assertStatus(200);
        $response->assertSeeText('All Clinics Registry & SaaS Tenants');
        $response->assertSee('City Dental & Implant Centre');
        $response->assertSee('Apex Aesthetic Dental');
        $response->assertSee('/clinic/city-dental');
        $response->assertSee('/clinic/apex-aesthetic');
    }

    public function test_platform_owner_can_inspect_specific_clinic_tenant_dossier(): void
    {
        $patient = Patient::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Zainab Bibi',
            'phone' => '0300-9998888',
        ]);

        $service = DentalService::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Scaling & Polishing',
            'category' => 'Preventive',
            'standard_fee' => 3500,
            'estimated_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $inv = Invoice::create([
            'clinic_id' => $this->clinicA->id,
            'patient_id' => $patient->id,
            'invoice_number' => 'INV-TENANT-01',
            'total_amount' => 7000.00,
            'paid_amount' => 5000.00,
            'balance_amount' => 2000.00,
            'status' => 'partially_paid',
        ]);

        Payment::create([
            'clinic_id' => $this->clinicA->id,
            'invoice_id' => $inv->id,
            'patient_id' => $patient->id,
            'received_by_user_id' => $this->adminA->id,
            'amount' => 5000.00,
            'payment_method' => 'cash',
            'payment_date' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->platformOwner)->get("/platform/clinics/{$this->clinicA->id}");

        $response->assertStatus(200);
        $response->assertSee('City Dental & Implant Centre');
        $response->assertSee('Dr. Ahmed Khan');
        $response->assertSee('Dr. Sara Ali');
        $response->assertSee('Ayesha Frontdesk');
        $response->assertSee('7,000');
        $response->assertSee('5,000');
    }

    public function test_platform_owner_can_suspend_and_reactivate_clinic(): void
    {
        // 1. Suspend Clinic A
        $resSuspend = $this->actingAs($this->platformOwner)->patch("/platform/clinics/{$this->clinicA->id}/status", [
            'status' => 'suspended',
        ]);
        $resSuspend->assertRedirect();
        $resSuspend->assertSessionHas('status');

        $this->clinicA->refresh();
        $this->assertEquals('suspended', $this->clinicA->status);

        // Public site should be unavailable (404) when suspended
        $this->get('/clinic/city-dental')->assertStatus(404);

        // 2. Reactivate Clinic A
        $resActivate = $this->actingAs($this->platformOwner)->patch("/platform/clinics/{$this->clinicA->id}/status", [
            'status' => 'active',
        ]);
        $resActivate->assertRedirect();

        $this->clinicA->refresh();
        $this->assertEquals('active', $this->clinicA->status);

        // Public site is accessible again
        $this->get('/clinic/city-dental')->assertStatus(200)->assertSee('City Dental & Implant Centre');
    }

    public function test_platform_owner_can_view_telemetry_and_gtv(): void
    {
        $response = $this->actingAs($this->platformOwner)->get('/platform/telemetry');

        $response->assertStatus(200);
        $response->assertSeeText('Platform Telemetry & SaaS Metrics');
        $response->assertSee('Platform GTV');
        $response->assertSee('Multi-Tenant Scoping Isolation');
        $response->assertSee('Top Tenant Clinics Leaderboard');
    }

    public function test_non_platform_owner_cannot_access_platform_area(): void
    {
        // Clinic Admin receives 403
        $this->actingAs($this->adminA)->get('/platform/clinics')->assertStatus(403);
        $this->actingAs($this->adminA)->get("/platform/clinics/{$this->clinicA->id}")->assertStatus(403);
        $this->actingAs($this->adminA)->get('/platform/telemetry')->assertStatus(403);

        // Associate Dentist receives 403
        $this->actingAs($this->dentistA)->get('/platform/clinics')->assertStatus(403);
        $this->actingAs($this->dentistA)->get('/platform/telemetry')->assertStatus(403);

        // Receptionist receives 403
        $this->actingAs($this->receptionistA)->get('/platform/clinics')->assertStatus(403);
        $this->actingAs($this->receptionistA)->get('/platform/telemetry')->assertStatus(403);
    }

    public function test_search_and_status_filtering_in_clinics_registry(): void
    {
        // Filter by active
        $resActive = $this->actingAs($this->platformOwner)->get('/platform/clinics?status=active');
        $resActive->assertStatus(200);
        $resActive->assertSee('City Dental & Implant Centre');
        $resActive->assertDontSee('Apex Aesthetic Dental');

        // Filter by suspended
        $resSuspended = $this->actingAs($this->platformOwner)->get('/platform/clinics?status=suspended');
        $resSuspended->assertStatus(200);
        $resSuspended->assertSee('Apex Aesthetic Dental');
        $resSuspended->assertDontSee('City Dental & Implant Centre');

        // Search by keyword
        $resSearch = $this->actingAs($this->platformOwner)->get('/platform/clinics?search=Apex');
        $resSearch->assertStatus(200);
        $resSearch->assertSee('Apex Aesthetic Dental');
        $resSearch->assertDontSee('City Dental & Implant Centre');
    }
}
