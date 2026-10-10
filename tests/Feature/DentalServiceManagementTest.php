<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DentalService;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\TreatmentRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DentalServiceManagementTest extends TestCase
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
            'name' => 'City Dental Hospital',
            'slug' => 'city-dental',
            'email' => 'city@test.com',
            'phone' => '0300-1111111',
            'currency' => 'PKR',
            'status' => 'active',
        ]);

        $this->adminA = User::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Dr. Admin A',
            'email' => 'admin@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);

        $this->dentistA = User::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Dr. Associate A',
            'email' => 'dentist@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $this->receptionistA = User::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Receptionist A',
            'email' => 'frontdesk@citydental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        // Clinic B
        $this->clinicB = Clinic::create([
            'name' => 'Apex Dental Care',
            'slug' => 'apex-dental',
            'email' => 'apex@test.com',
            'phone' => '0300-2222222',
            'currency' => 'PKR',
            'status' => 'active',
        ]);

        $this->adminB = User::create([
            'clinic_id' => $this->clinicB->id,
            'name' => 'Dr. Admin B',
            'email' => 'admin@apexdental.test',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);
    }

    public function test_clinic_staff_can_view_services_catalog_with_categories_and_kpis(): void
    {
        $service1 = DentalService::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Ultrasonic Scaling',
            'category' => 'Preventive',
            'standard_fee' => 3500.00,
            'estimated_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $service2 = DentalService::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Molar Root Canal Therapy',
            'category' => 'Endodontics',
            'standard_fee' => 15000.00,
            'estimated_duration_minutes' => 60,
            'is_active' => true,
        ]);

        // Clinic Admin view
        $responseAdmin = $this->actingAs($this->adminA)->get('/services');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Services Catalog & Pricing Engine');
        $responseAdmin->assertSee('Ultrasonic Scaling');
        $responseAdmin->assertSee('Molar Root Canal Therapy');
        $responseAdmin->assertSee('Add New Procedure');

        // Receptionist view
        $responseReceptionist = $this->actingAs($this->receptionistA)->get('/services');
        $responseReceptionist->assertStatus(200);
        $responseReceptionist->assertSee('Ultrasonic Scaling');
        $responseReceptionist->assertDontSee('Add New Procedure'); // Non-admin cannot see create button
    }

    public function test_clinic_admin_can_create_new_dental_service_with_category(): void
    {
        $payload = [
            'name' => 'Zirconia Monolithic Crown',
            'category' => 'Prosthodontics',
            'standard_fee' => 22000.00,
            'estimated_duration_minutes' => 45,
            'description' => 'Precision milled CAD/CAM crown restoration.',
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->adminA)->post('/services', $payload);

        $response->assertRedirect('/services');
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('dental_services', [
            'clinic_id' => $this->clinicA->id,
            'name' => 'Zirconia Monolithic Crown',
            'category' => 'Prosthodontics',
            'standard_fee' => 22000.00,
            'estimated_duration_minutes' => 45,
            'is_active' => true,
        ]);
    }

    public function test_non_admin_cannot_create_or_modify_services(): void
    {
        $service = DentalService::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Basic Extraction',
            'category' => 'Oral Surgery',
            'standard_fee' => 4000.00,
            'estimated_duration_minutes' => 20,
            'is_active' => true,
        ]);

        // Dentist cannot view create form
        $this->actingAs($this->dentistA)->get('/services/create')->assertStatus(403);

        // Receptionist cannot post new service
        $this->actingAs($this->receptionistA)->post('/services', [
            'name' => 'Unauthorized Service',
            'category' => 'General',
            'standard_fee' => 1000,
            'estimated_duration_minutes' => 20,
        ])->assertStatus(403);

        // Receptionist cannot edit
        $this->actingAs($this->receptionistA)->get("/services/{$service->id}/edit")->assertStatus(403);

        // Dentist cannot delete
        $this->actingAs($this->dentistA)->delete("/services/{$service->id}")->assertStatus(403);
    }

    public function test_clinic_admin_can_update_service_details_and_tariff(): void
    {
        $service = DentalService::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Composite Filling',
            'category' => 'Restorative',
            'standard_fee' => 4000.00,
            'estimated_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $updatePayload = [
            'name' => 'Composite Resin Restoration (Multi-Surface)',
            'category' => 'Restorative',
            'standard_fee' => 5500.00,
            'estimated_duration_minutes' => 40,
            'description' => 'Upgraded multi-surface light cure restoration.',
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->adminA)->put("/services/{$service->id}", $updatePayload);

        $response->assertRedirect('/services');

        $service->refresh();
        $this->assertEquals('Composite Resin Restoration (Multi-Surface)', $service->name);
        $this->assertEquals(5500.00, $service->standard_fee);
        $this->assertEquals(40, $service->estimated_duration_minutes);
    }

    public function test_clinic_admin_can_toggle_service_active_status(): void
    {
        $service = DentalService::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Cosmetic Veneer',
            'category' => 'Cosmetic',
            'standard_fee' => 30000.00,
            'estimated_duration_minutes' => 60,
            'is_active' => true,
        ]);

        // Toggle to inactive
        $response = $this->actingAs($this->adminA)->patch("/services/{$service->id}/toggle");
        $response->assertRedirect();

        $service->refresh();
        $this->assertFalse($service->is_active);

        // Toggle back to active
        $response2 = $this->actingAs($this->adminA)->patch("/services/{$service->id}/toggle");
        $response2->assertRedirect();

        $service->refresh();
        $this->assertTrue($service->is_active);
    }

    public function test_destroy_soft_deactivates_service_when_linked_to_historical_records(): void
    {
        $service = DentalService::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Surgical Extraction',
            'category' => 'Oral Surgery',
            'standard_fee' => 15000.00,
            'estimated_duration_minutes' => 45,
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Tariq Mehmood',
            'phone' => '0300-3333333',
        ]);

        // Link with an appointment
        Appointment::create([
            'clinic_id' => $this->clinicA->id,
            'patient_id' => $patient->id,
            'doctor_id' => $this->adminA->id,
            'service_id' => $service->id,
            'appointment_date' => Carbon::today(),
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addMinutes(45),
            'status' => 'confirmed',
        ]);

        $this->assertTrue($service->isReferenced());

        // Attempt deletion
        $response = $this->actingAs($this->adminA)->delete("/services/{$service->id}");
        $response->assertRedirect('/services');

        // Service should still exist in database, but marked inactive
        $this->assertDatabaseHas('dental_services', [
            'id' => $service->id,
            'is_active' => false,
        ]);
    }

    public function test_destroy_permanently_deletes_service_when_unreferenced(): void
    {
        $service = DentalService::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Unused Test Service',
            'category' => 'General',
            'standard_fee' => 2000.00,
            'estimated_duration_minutes' => 15,
            'is_active' => true,
        ]);

        $this->assertFalse($service->isReferenced());

        $response = $this->actingAs($this->adminA)->delete("/services/{$service->id}");
        $response->assertRedirect('/services');

        $this->assertDatabaseMissing('dental_services', [
            'id' => $service->id,
        ]);
    }

    public function test_multi_tenant_isolation_clinic_a_cannot_view_or_modify_clinic_b_services(): void
    {
        $serviceB = DentalService::create([
            'clinic_id' => $this->clinicB->id,
            'name' => 'Clinic B Exclusive Implant',
            'category' => 'Prosthodontics',
            'standard_fee' => 80000.00,
            'estimated_duration_minutes' => 90,
            'is_active' => true,
        ]);

        // Admin A viewing catalog should not see Clinic B service
        $response = $this->actingAs($this->adminA)->get('/services');
        $response->assertDontSee('Clinic B Exclusive Implant');

        // Admin A cannot edit Clinic B service
        $this->actingAs($this->adminA)->get("/services/{$serviceB->id}/edit")->assertStatus(404);

        // Admin A cannot update Clinic B service
        $this->actingAs($this->adminA)->put("/services/{$serviceB->id}", [
            'name' => 'Hacked Name',
            'category' => 'General',
            'standard_fee' => 100,
            'estimated_duration_minutes' => 15,
        ])->assertStatus(404);

        // Admin A cannot delete Clinic B service
        $this->actingAs($this->adminA)->delete("/services/{$serviceB->id}")->assertStatus(404);
    }

    public function test_category_filter_and_search_query_filter_services_properly(): void
    {
        DentalService::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Scaling & Polishing',
            'category' => 'Preventive',
            'standard_fee' => 3000,
            'estimated_duration_minutes' => 30,
            'is_active' => true,
        ]);

        DentalService::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Root Canal Molar',
            'category' => 'Endodontics',
            'standard_fee' => 15000,
            'estimated_duration_minutes' => 60,
            'is_active' => true,
        ]);

        // Filter by category
        $responseEndo = $this->actingAs($this->adminA)->get('/services?category=Endodontics');
        $responseEndo->assertStatus(200);
        $responseEndo->assertSee('Root Canal Molar');
        $responseEndo->assertDontSee('Scaling & Polishing');

        // Search query
        $responseSearch = $this->actingAs($this->adminA)->get('/services?search=Scaling');
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Scaling & Polishing');
        $responseSearch->assertDontSee('Root Canal Molar');
    }
}
