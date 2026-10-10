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

class ReportManagementTest extends TestCase
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

    public function test_clinic_admin_can_view_reports_and_financial_analytics(): void
    {
        $patient = Patient::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Kamran Akmal',
            'phone' => '0300-9998877',
        ]);

        $service = DentalService::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Root Canal Treatment (Molar)',
            'category' => 'Endodontics',
            'standard_fee' => 15000.00,
            'estimated_duration_minutes' => 60,
            'is_active' => true,
        ]);

        $appointment = Appointment::create([
            'clinic_id' => $this->clinicA->id,
            'patient_id' => $patient->id,
            'doctor_id' => $this->dentistA->id,
            'service_id' => $service->id,
            'appointment_date' => Carbon::today(),
            'starts_at' => Carbon::today()->setTime(11, 0),
            'ends_at' => Carbon::today()->setTime(12, 0),
            'status' => 'completed',
        ]);

        TreatmentRecord::create([
            'clinic_id' => $this->clinicA->id,
            'patient_id' => $patient->id,
            'doctor_id' => $this->dentistA->id,
            'service_id' => $service->id,
            'appointment_id' => $appointment->id,
            'tooth_numbers' => '16',
            'diagnosis' => 'Irreversible pulpitis',
            'treatment_rendered' => 'Complete Root Canal Filling',
        ]);

        $invoice = Invoice::create([
            'clinic_id' => $this->clinicA->id,
            'patient_id' => $patient->id,
            'appointment_id' => $appointment->id,
            'invoice_number' => 'INV-TEST-001',
            'total_amount' => 15000.00,
            'discount_amount' => 0.00,
            'paid_amount' => 10000.00,
            'balance_amount' => 5000.00,
            'status' => 'partially_paid',
            'created_at' => Carbon::today(),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'service_id' => $service->id,
            'description' => 'Root Canal Treatment (Molar)',
            'unit_price' => 15000.00,
            'quantity' => 1,
            'total_price' => 15000.00,
        ]);

        Payment::create([
            'clinic_id' => $this->clinicA->id,
            'invoice_id' => $invoice->id,
            'patient_id' => $patient->id,
            'received_by_user_id' => $this->adminA->id,
            'amount' => 10000.00,
            'payment_method' => 'cash',
            'payment_date' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->adminA)->get('/reports');

        $response->assertStatus(200);
        $response->assertSee('Financial Analytics');
        $response->assertSee('15,000.00'); // Invoiced
        $response->assertSee('10,000.00'); // Collected
        $response->assertSee('5,000.00');  // Dues
        $response->assertSee('Endodontics');
        $response->assertSee('Dr. Associate A');
        $response->assertSee('Print Executive Statement');
    }

    public function test_non_admin_cannot_access_financial_reports(): void
    {
        // Receptionist cannot access
        $this->actingAs($this->receptionistA)->get('/reports')->assertStatus(403);
        $this->actingAs($this->receptionistA)->get('/reports/print')->assertStatus(403);

        // Dentist cannot access
        $this->actingAs($this->dentistA)->get('/reports')->assertStatus(403);
        $this->actingAs($this->dentistA)->get('/reports/print')->assertStatus(403);
    }

    public function test_multi_tenant_financial_isolation(): void
    {
        // Clinic A invoice & payment
        $patientA = Patient::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Patient Clinic A',
            'phone' => '0300-1111111',
        ]);

        $invoiceA = Invoice::create([
            'clinic_id' => $this->clinicA->id,
            'patient_id' => $patientA->id,
            'invoice_number' => 'INV-CLINIC-A-01',
            'total_amount' => 25000.00,
            'paid_amount' => 25000.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_at' => Carbon::now(),
        ]);

        Payment::create([
            'clinic_id' => $this->clinicA->id,
            'invoice_id' => $invoiceA->id,
            'patient_id' => $patientA->id,
            'received_by_user_id' => $this->adminA->id,
            'amount' => 25000.00,
            'payment_method' => 'cash',
            'payment_date' => Carbon::now(),
        ]);

        // Clinic B invoice & payment
        $patientB = Patient::create([
            'clinic_id' => $this->clinicB->id,
            'name' => 'Secret VIP Patient Clinic B',
            'phone' => '0300-2222222',
        ]);

        $invoiceB = Invoice::create([
            'clinic_id' => $this->clinicB->id,
            'patient_id' => $patientB->id,
            'invoice_number' => 'INV-CLINIC-B-99',
            'total_amount' => 99999.00,
            'paid_amount' => 99999.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_at' => Carbon::now(),
        ]);

        Payment::create([
            'clinic_id' => $this->clinicB->id,
            'invoice_id' => $invoiceB->id,
            'patient_id' => $patientB->id,
            'received_by_user_id' => $this->adminB->id,
            'amount' => 99999.00,
            'payment_method' => 'card',
            'payment_date' => Carbon::now(),
        ]);

        // Admin A should see Clinic A's 25,000, and NEVER see Clinic B's 99,999 or VIP patient
        $responseA = $this->actingAs($this->adminA)->get('/reports');
        $responseA->assertStatus(200);
        $responseA->assertSee('25,000.00');
        $responseA->assertDontSee('99,999.00');
        $responseA->assertDontSee('Secret VIP Patient Clinic B');
        $responseA->assertDontSee('INV-CLINIC-B-99');
    }

    public function test_date_range_filtering_filters_data_accurately(): void
    {
        $patient = Patient::create([
            'clinic_id' => $this->clinicA->id,
            'name' => 'Usman Tariq',
            'phone' => '0300-7776655',
        ]);

        // Payment Today
        $invToday = Invoice::create([
            'clinic_id' => $this->clinicA->id,
            'patient_id' => $patient->id,
            'invoice_number' => 'INV-TODAY-01',
            'total_amount' => 8000.00,
            'paid_amount' => 8000.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_at' => Carbon::today(),
        ]);

        Payment::create([
            'clinic_id' => $this->clinicA->id,
            'invoice_id' => $invToday->id,
            'patient_id' => $patient->id,
            'received_by_user_id' => $this->adminA->id,
            'amount' => 8000.00,
            'payment_method' => 'cash',
            'payment_date' => Carbon::today()->setTime(14, 0),
        ]);

        // Payment Last Month
        $lastMonthDate = Carbon::now()->subMonth()->startOfMonth()->addDays(5);
        $invLastMonth = Invoice::create([
            'clinic_id' => $this->clinicA->id,
            'patient_id' => $patient->id,
            'invoice_number' => 'INV-LAST-MONTH-02',
            'total_amount' => 45000.00,
            'paid_amount' => 45000.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'created_at' => $lastMonthDate,
        ]);

        Payment::create([
            'clinic_id' => $this->clinicA->id,
            'invoice_id' => $invLastMonth->id,
            'patient_id' => $patient->id,
            'received_by_user_id' => $this->adminA->id,
            'amount' => 45000.00,
            'payment_method' => 'card',
            'payment_date' => $lastMonthDate,
        ]);

        // Filter by today
        $responseToday = $this->actingAs($this->adminA)->get('/reports?range=today');
        $responseToday->assertStatus(200);
        $responseToday->assertSee('8,000.00');
        $responseToday->assertDontSee('45,000.00');

        // Filter by last month
        $responseLastMonth = $this->actingAs($this->adminA)->get('/reports?range=last_month');
        $responseLastMonth->assertStatus(200);
        $responseLastMonth->assertSee('45,000.00');
        $responseLastMonth->assertDontSee('8,000.00');
    }

    public function test_print_statement_view_renders_cleanly(): void
    {
        $response = $this->actingAs($this->adminA)->get('/reports/print');

        $response->assertStatus(200);
        $response->assertSee('Islamabad Dental Complex');
        $response->assertSeeText('Doctor Productivity & Financial Attribution');
    }
}
