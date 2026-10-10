<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DentalService;
use App\Models\FollowUp;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\TreatmentRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprehensiveViewRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_application_views_render_successfully_without_errors(): void
    {
        // 1. Setup clinic & users
        $clinic = Clinic::create([
            'name' => 'Islamabad Premier Dental',
            'slug' => 'premier-dental',
            'phone' => '051-1234567',
            'email' => 'contact@premierdental.test',
            'status' => 'active',
        ]);

        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Bilal Qureshi',
            'email' => 'dr.bilal@premierdental.test',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Zainab Bibi',
            'phone' => '0312-9876543',
            'email' => 'zainab@example.test',
            'gender' => 'female',
            'date_of_birth' => '1995-04-12',
        ]);

        $service = DentalService::create([
            'clinic_id' => $clinic->id,
            'name' => 'Root Canal Treatment (RCT)',
            'category' => 'Endodontics',
            'estimated_duration_minutes' => 60,
            'standard_fee' => 12000,
            'is_active' => true,
        ]);

        $appointment = Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'service_id' => $service->id,
            'appointment_date' => Carbon::tomorrow()->toDateString(),
            'starts_at' => Carbon::tomorrow()->setTime(10, 0),
            'ends_at' => Carbon::tomorrow()->setTime(11, 0),
            'status' => 'confirmed',
        ]);

        $treatment = TreatmentRecord::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'service_id' => $service->id,
            'appointment_id' => $appointment->id,
            'tooth_numbers' => '16, 26',
            'diagnosis' => 'Irreversible pulpitis on tooth #16',
            'treatment_rendered' => 'Biomechanical preparation and obturation',
            'clinical_notes' => 'Tolerated well under local anaesthesia.',
            'prescription' => 'Amoxicillin 500mg TDS x 5 days, Ibuprofen 400mg BD x 3 days',
        ]);

        $invoice = Invoice::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'appointment_id' => $appointment->id,
            'invoice_number' => 'INV-202610-0099',
            'total_amount' => 12000,
            'discount_amount' => 1000,
            'paid_amount' => 5000,
            'balance_amount' => 6000,
            'status' => 'partially_paid',
            'due_date' => Carbon::today(),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'service_id' => $service->id,
            'description' => 'Root Canal Therapy - #16',
            'unit_price' => 12000,
            'quantity' => 1,
            'total_price' => 12000,
        ]);

        $payment = Payment::create([
            'clinic_id' => $clinic->id,
            'invoice_id' => $invoice->id,
            'patient_id' => $patient->id,
            'received_by_user_id' => $doctor->id,
            'amount' => 5000,
            'payment_method' => 'cash',
            'payment_date' => Carbon::now(),
        ]);

        // 2. Public Pages
        $this->get('/login')->assertStatus(200)->assertSee('Sign in to your clinic workspace');
        $this->get('/register-clinic')->assertStatus(200)->assertSee('Register Your Dental Clinic');
        $this->get("/clinic/{$clinic->slug}")->assertStatus(200)->assertSee($clinic->name);

        // 3. Authenticated App Pages
        $this->actingAs($doctor)->get('/dashboard')->assertStatus(200)->assertSee('Islamabad Premier Dental');
        $this->actingAs($doctor)->get('/appointments')->assertStatus(200)->assertSee('Daily Schedule');
        $this->actingAs($doctor)->get('/appointments/create')->assertStatus(200)->assertSee('Schedule New Appointment');

        $this->actingAs($doctor)->get('/patients')->assertStatus(200)->assertSee('Patients Directory');
        $this->actingAs($doctor)->get('/patients/create')->assertStatus(200)->assertSee('Register New Patient');
        $this->actingAs($doctor)->get("/patients/{$patient->id}")->assertStatus(200)->assertSee('Zainab Bibi');
        $this->actingAs($doctor)->get("/patients/{$patient->id}/edit")->assertStatus(200)->assertSee('Edit Patient');

        $this->actingAs($doctor)->get('/treatments')->assertStatus(200)->assertSee('Clinical Treatments and Odontogram');
        $this->actingAs($doctor)->get('/treatments/create')->assertStatus(200)->assertSee('Record Clinical Procedure');
        $this->actingAs($doctor)->get("/treatments/{$treatment->id}")->assertStatus(200)->assertSee('Clinical Procedure Dossier');
        $this->actingAs($doctor)->get("/treatments/{$treatment->id}/edit")->assertStatus(200)->assertSee('Edit Treatment');

        $followUp = FollowUp::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'treatment_record_id' => $treatment->id,
            'due_date' => Carbon::tomorrow(),
            'reason' => 'Post-op checkup',
            'status' => 'pending',
        ]);

        $this->actingAs($doctor)->get('/invoices')->assertStatus(200)->assertSee('Billing and Financial Ledger');
        $this->actingAs($doctor)->get('/invoices/create')->assertStatus(200)->assertSee('Generate Patient Invoice');
        $this->actingAs($doctor)->get("/invoices/{$invoice->id}")->assertStatus(200)->assertSee('INV-202610-0099');
        $this->actingAs($doctor)->get("/payments/{$payment->id}/receipt")->assertStatus(200)->assertSee('Print Official Receipt');

        $this->actingAs($doctor)->get('/follow-ups')->assertStatus(200)->assertSee('Clinical Recall Queue');
        $this->actingAs($doctor)->get('/follow-ups/create')->assertStatus(200)->assertSee('Schedule Clinical Recall Task');
        $this->actingAs($doctor)->get("/follow-ups/{$followUp->id}/edit")->assertStatus(200)->assertSee('Edit Follow-Up Recall Task');

        $this->actingAs($doctor)->get('/doctors')->assertStatus(200)->assertSee('Doctor Schedules');
        $this->actingAs($doctor)->get('/doctors/create')->assertStatus(200)->assertSee('Onboard New Dental Surgeon');
        $this->actingAs($doctor)->get("/doctors/{$doctor->id}")->assertStatus(200)->assertSee($doctor->name);
        $this->actingAs($doctor)->get("/doctors/{$doctor->id}/edit")->assertStatus(200)->assertSee('Edit Schedule');

        $this->actingAs($doctor)->get('/services')->assertStatus(200)->assertSee('Services Catalog & Pricing Engine');
        $this->actingAs($doctor)->get('/services/create')->assertStatus(200)->assertSee('Add Dental Procedure');
        $this->actingAs($doctor)->get("/services/{$service->id}/edit")->assertStatus(200)->assertSee('Edit Dental Procedure');

        $this->actingAs($doctor)->get('/reports')->assertStatus(200)->assertSee('Financial Analytics');
        $this->actingAs($doctor)->get('/reports/print')->assertStatus(200)->assertSee('Executive Statement');

        $this->actingAs($doctor)->get('/admin/staff')->assertStatus(200)->assertSee('Clinic Staff');
        $this->actingAs($doctor)->get('/admin/staff/create')->assertStatus(200)->assertSee('Onboard Clinic Staff Member');
        $this->actingAs($doctor)->get("/admin/staff/{$doctor->id}/edit")->assertStatus(200)->assertSee('Edit Staff Member');
        $this->actingAs($doctor)->get('/admin/settings')->assertStatus(200)->assertSee('Clinic Workspace Settings');

        // 4. Platform Super Owner Pages
        $platformOwner = User::create([
            'name' => 'SaaS Super Owner',
            'email' => 'superadmin@dentalcare.test',
            'password' => bcrypt('password'),
            'role' => 'platform_owner',
            'is_active' => true,
        ]);

        $this->actingAs($platformOwner)->get('/dashboard')->assertStatus(200)->assertSee('SaaS Platform Overview');
        $this->actingAs($platformOwner)->get('/platform/clinics')->assertStatus(200)->assertSee('All Clinics Registry');
        $this->actingAs($platformOwner)->get("/platform/clinics/{$clinic->id}")->assertStatus(200)->assertSee($clinic->name);
        $this->actingAs($platformOwner)->get('/platform/telemetry')->assertStatus(200)->assertSee('Platform Telemetry');
    }
}
