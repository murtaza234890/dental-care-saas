<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DentalService;
use App\Models\DoctorProfile;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\TreatmentRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinalAcceptanceCriteriaTest extends TestCase
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
            'name' => 'Islamabad Premier Dental',
            'slug' => 'premier-dental',
            'email' => 'info@premierdental.test',
            'phone' => '051-1112233',
            'currency' => 'PKR',
            'status' => 'active',
        ]);

        $this->adminA = User::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Dr. Ahmed Khan',
            'email' => 'ahmed@premierdental.test',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);

        $this->dentistA = User::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Dr. Sara Ali',
            'email' => 'sara@premierdental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'clinic_id' => $this->clinicA->id,
            'user_id' => $this->dentistA->id,
            'specialization' => 'Endodontist',
            'consultation_fee' => 2000.00,
            'slot_duration_minutes' => 30,
            'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
        ]);

        $this->receptionistA = User::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Ayesha Noor',
            'email' => 'ayesha@premierdental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        // Clinic B
        $this->clinicB = Clinic::create([
            'name' => 'Lahore Dental Specialists',
            'slug' => 'lhr-specialists',
            'email' => 'info@lhrdental.test',
            'phone' => '042-3334455',
            'currency' => 'PKR',
            'status' => 'active',
        ]);

        $this->adminB = User::create([
            'clinic_id' => $this->clinicB->id,
            'name' => 'Dr. Bilal Qureshi',
            'email' => 'bilal@lhrdental.test',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);
    }

    /**
     * Acceptance Criterion 1: Isolation Test
     * Clinic Admin A cannot view, edit, or delete any records belonging to Clinic B under any circumstance.
     */
    public function test_acceptance_criterion_1_isolation_test(): void
    {
        $patientB = Patient::create([
            'clinic_id' => $this->clinicB->id,
            'name' => 'Confidential Patient B',
            'phone' => '0300-9999999',
        ]);

        $serviceB = DentalService::create([
            'clinic_id' => $this->clinicB->id,
            'name' => 'Clinic B Exclusive Veneer',
            'category' => 'Cosmetic',
            'standard_fee' => 45000,
            'estimated_duration_minutes' => 60,
            'is_active' => true,
        ]);

        $appointmentB = Appointment::create([
            'clinic_id' => $this->clinicB->id,
            'patient_id' => $patientB->id,
            'doctor_id' => $this->adminB->id,
            'service_id' => $serviceB->id,
            'appointment_date' => Carbon::tomorrow()->toDateString(),
            'starts_at' => Carbon::tomorrow()->setTime(11, 0),
            'ends_at' => Carbon::tomorrow()->setTime(12, 0),
            'status' => 'confirmed',
        ]);

        $invoiceB = Invoice::create([
            'clinic_id' => $this->clinicB->id,
            'patient_id' => $patientB->id,
            'appointment_id' => $appointmentB->id,
            'invoice_number' => 'INV-SEC-B-001',
            'total_amount' => 45000,
            'paid_amount' => 0,
            'balance_amount' => 45000,
            'status' => 'unpaid',
        ]);

        // 1. Patient Isolation
        $this->actingAs($this->adminA)->get('/patients')->assertDontSee('Confidential Patient B');
        $this->actingAs($this->adminA)->get("/patients/{$patientB->id}")->assertStatus(404);
        $this->actingAs($this->adminA)->put("/patients/{$patientB->id}", ['name' => 'Hacked'])->assertStatus(404);
        $this->actingAs($this->adminA)->delete("/patients/{$patientB->id}")->assertStatus(404);

        // 2. Service Isolation
        $this->actingAs($this->adminA)->get('/services')->assertDontSee('Clinic B Exclusive Veneer');
        $this->actingAs($this->adminA)->get("/services/{$serviceB->id}/edit")->assertStatus(404);

        // 3. Invoice Isolation
        $this->actingAs($this->adminA)->get('/invoices')->assertDontSee('INV-SEC-B-001');
        $this->actingAs($this->adminA)->get("/invoices/{$invoiceB->id}")->assertStatus(404);

        // 4. Staff Isolation
        $this->actingAs($this->adminA)->get('/admin/staff')->assertDontSee('Dr. Bilal Qureshi');
        $this->actingAs($this->adminA)->get("/admin/staff/{$this->adminB->id}/edit")->assertStatus(404);
    }

    /**
     * Acceptance Criterion 2: Double Booking Test
     * Two simultaneous booking requests for Doctor D at 10:00 AM on Date X must result in
     * exactly 1 successful booking and 1 clear "Slot already booked" error.
     */
    public function test_acceptance_criterion_2_double_booking_test(): void
    {
        $tomorrowDate = Carbon::tomorrow()->toDateString();
        $slotTime = '10:00';

        $bookingPayload1 = [
            'patient_name' => 'Patient First',
            'patient_phone' => '0300-1111111',
            'patient_email' => 'first@test.com',
            'doctor_id' => $this->dentistA->id,
            'appointment_date' => $tomorrowDate,
            'starts_at' => $slotTime,
        ];

        // First booking request succeeds
        $res1 = $this->post("/clinic/{$this->clinicA->slug}/book", $bookingPayload1);
        $res1->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'clinic_id' => $this->clinicA->id,
            'doctor_id' => $this->dentistA->id,
            'status' => 'confirmed',
        ]);

        $createdAppt = Appointment::where('clinic_id', $this->clinicA->id)
            ->where('doctor_id', $this->dentistA->id)
            ->first();
        $this->assertNotNull($createdAppt);
        $this->assertEquals($tomorrowDate, $createdAppt->appointment_date->toDateString());

        // Second booking request for the exact same slot and doctor must be rejected
        $bookingPayload2 = [
            'patient_name' => 'Patient Second (Conflict)',
            'patient_phone' => '0300-2222222',
            'patient_email' => 'second@test.com',
            'doctor_id' => $this->dentistA->id,
            'appointment_date' => $tomorrowDate,
            'starts_at' => $slotTime,
        ];

        $res2 = $this->post("/clinic/{$this->clinicA->slug}/book", $bookingPayload2);
        $res2->assertSessionHasErrors('starts_at');

        // Confirm exactly 1 appointment exists in database for this doctor
        $appointmentCount = Appointment::where('clinic_id', $this->clinicA->id)
            ->where('doctor_id', $this->dentistA->id)
            ->count();

        $this->assertEquals(1, $appointmentCount);
    }

    /**
     * Acceptance Criterion 3: Financial Integrity Test
     * Partial payments accurately reflect remaining balance; zero balance marks invoice Paid.
     */
    public function test_acceptance_criterion_3_financial_integrity_test(): void
    {
        $patient = Patient::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Tariq Jamil',
            'phone' => '0300-5556677',
        ]);

        $service = DentalService::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Root Canal Treatment',
            'category' => 'Endodontics',
            'standard_fee' => 10000,
            'estimated_duration_minutes' => 45,
            'is_active' => true,
        ]);

        $invoice = Invoice::create([
            'clinic_id' => $this->clinicA->id,
            'patient_id' => $patient->id,
            'invoice_number' => 'INV-INTEGRITY-01',
            'total_amount' => 10000,
            'discount_amount' => 0,
            'paid_amount' => 0,
            'balance_amount' => 10000,
            'status' => 'unpaid',
            'due_date' => Carbon::today(),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'service_id' => $service->id,
            'description' => 'Root Canal Treatment',
            'unit_price' => 10000,
            'quantity' => 1,
            'total_price' => 10000,
        ]);

        // Step 1: Record partial payment of 4,000 PKR
        $payment1Res = $this->actingAs($this->adminA)->post("/invoices/{$invoice->id}/payments", [
            'amount' => 4000.00,
            'payment_method' => 'cash',
            'payment_date' => Carbon::now()->format('Y-m-d\TH:i'),
        ]);
        $payment1Res->assertRedirect();

        $invoice->refresh();
        $this->assertEquals(4000.00, (float) $invoice->paid_amount);
        $this->assertEquals(6000.00, (float) $invoice->balance_amount);
        $this->assertEquals('partially_paid', $invoice->status);

        // Step 2: Record remaining payment of 6,000 PKR
        $payment2Res = $this->actingAs($this->adminA)->post("/invoices/{$invoice->id}/payments", [
            'amount' => 6000.00,
            'payment_method' => 'card',
            'payment_date' => Carbon::now()->format('Y-m-d\TH:i'),
        ]);
        $payment2Res->assertRedirect();

        $invoice->refresh();
        $this->assertEquals(10000.00, (float) $invoice->paid_amount);
        $this->assertEquals(0.00, (float) $invoice->balance_amount);
        $this->assertEquals('paid', $invoice->status);
    }

    /**
     * Acceptance Criterion 4: Mobile Responsive Test
     * Public booking page and receptionist calendar are completely usable on standard smartphone viewports (375px+).
     */
    public function test_acceptance_criterion_4_public_booking_and_mobile_views(): void
    {
        // Public booking page returns 200 with viewport meta tag
        $resPublic = $this->get("/clinic/{$this->clinicA->slug}");
        $resPublic->assertStatus(200);
        $resPublic->assertSee('viewport');
        $resPublic->assertSee('width=device-width');
        $resPublic->assertSee($this->clinicA->name);
        $resPublic->assertSee('Book Appointment Now');

        // Appointments calendar returns 200 with responsive layouts
        $resCalendar = $this->actingAs($this->receptionistA)->get('/appointments');
        $resCalendar->assertStatus(200);
        $resCalendar->assertSee('Daily Schedule');
    }

    /**
     * Acceptance Criterion 5: Role Boundary Test
     * Receptionist role attempting to access doctor clinical notes or clinic settings receives an authorization error.
     */
    public function test_acceptance_criterion_5_role_boundary_test(): void
    {
        // Receptionist cannot access confidential clinical notes
        $resNotes = $this->actingAs($this->receptionistA)->get('/clinical/confidential-notes');
        $resNotes->assertStatus(403);

        // Receptionist cannot access clinic settings
        $resSettings = $this->actingAs($this->receptionistA)->get('/admin/settings');
        $resSettings->assertStatus(403);

        // Receptionist cannot access financial analytics / reports
        $resReports = $this->actingAs($this->receptionistA)->get('/reports');
        $resReports->assertStatus(403);

        // Receptionist cannot access platform super admin
        $resPlatform = $this->actingAs($this->receptionistA)->get('/platform/clinics');
        $resPlatform->assertStatus(403);

        // Doctor / Dentist cannot access clinic settings or platform area
        $this->actingAs($this->dentistA)->get('/admin/settings')->assertStatus(403);
        $this->actingAs($this->dentistA)->get('/platform/clinics')->assertStatus(403);

        // Clinic Admin can access settings
        $this->actingAs($this->adminA)->get('/admin/settings')->assertStatus(200);
    }
}
