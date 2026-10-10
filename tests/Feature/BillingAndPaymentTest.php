<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DentalService;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingAndPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_staff_can_view_invoices_directory_and_kpis(): void
    {
        $clinic = Clinic::create(['name' => 'Metro Dental', 'slug' => 'metro-dental']);
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Ayesha Desk',
            'email' => 'ayesha@metrodental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Hamza Ali',
            'phone' => '0300-7778889',
        ]);

        $invoice = Invoice::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'invoice_number' => 'INV-202610-0001',
            'total_amount' => 12000,
            'discount_amount' => 1000,
            'paid_amount' => 4000,
            'balance_amount' => 8000,
            'status' => 'partially_paid',
            'due_date' => Carbon::today(),
        ]);

        $response = $this->actingAs($receptionist)->get('/invoices');

        $response->assertStatus(200);
        $response->assertSee('Billing and Financial Ledger');
        $response->assertSee('INV-202610-0001');
        $response->assertSee('Hamza Ali');
        $response->assertSee('Partial');
    }

    public function test_staff_can_create_invoice_with_line_items_and_discount(): void
    {
        $clinic = Clinic::create(['name' => 'Metro Dental', 'slug' => 'metro-dental']);
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Ayesha Desk',
            'email' => 'ayesha@metrodental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Kamran Akmal',
            'phone' => '0321-4443322',
        ]);

        $service1 = DentalService::create([
            'clinic_id' => $clinic->id,
            'name' => 'Scaling & Polishing',
            'estimated_duration_minutes' => 30,
            'standard_fee' => 4000,
            'is_active' => true,
        ]);

        $service2 = DentalService::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dental X-Ray (IOPA)',
            'estimated_duration_minutes' => 15,
            'standard_fee' => 1500,
            'is_active' => true,
        ]);

        $response = $this->actingAs($receptionist)->post('/invoices', [
            'patient_id' => $patient->id,
            'due_date' => Carbon::today()->format('Y-m-d'),
            'discount_amount' => 500,
            'items' => [
                [
                    'service_id' => $service1->id,
                    'description' => 'Full mouth scaling and stain polishing',
                    'unit_price' => 4000,
                    'quantity' => 1,
                ],
                [
                    'service_id' => $service2->id,
                    'description' => 'IOPA radiograph tooth 16 and 46',
                    'unit_price' => 1500,
                    'quantity' => 2,
                ],
            ],
        ]);

        $invoice = Invoice::where('patient_id', $patient->id)->first();
        $this->assertNotNull($invoice);
        $response->assertRedirect(route('invoices.show', $invoice->id));

        // Subtotal = 4000 + (1500 * 2) = 7000
        // Discount = 500
        // Total = 6500, Paid = 0, Balance = 6500
        $this->assertEquals(6500, (float) $invoice->total_amount);
        $this->assertEquals(500, (float) $invoice->discount_amount);
        $this->assertEquals(0, (float) $invoice->paid_amount);
        $this->assertEquals(6500, (float) $invoice->balance_amount);
        $this->assertEquals('unpaid', $invoice->status);
        $this->assertCount(2, $invoice->items);
    }

    public function test_staff_can_record_partial_and_full_payments(): void
    {
        $clinic = Clinic::create(['name' => 'Metro Dental', 'slug' => 'metro-dental']);
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Ayesha Desk',
            'email' => 'ayesha@metrodental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Kamran Akmal',
            'phone' => '0321-4443322',
        ]);

        $invoice = Invoice::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'invoice_number' => 'INV-202610-0002',
            'total_amount' => 10000,
            'discount_amount' => 0,
            'paid_amount' => 0,
            'balance_amount' => 10000,
            'status' => 'unpaid',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Surgical Extraction',
            'unit_price' => 10000,
            'quantity' => 1,
            'total_price' => 10000,
        ]);

        // 1. Record Partial Payment of Rs. 4,000
        $partialResponse = $this->actingAs($receptionist)->post("/invoices/{$invoice->id}/payments", [
            'amount' => 4000,
            'payment_method' => 'cash',
            'transaction_reference' => 'Cash at reception',
        ]);

        $partialResponse->assertRedirect(route('invoices.show', $invoice->id));
        $invoice->refresh();

        $this->assertEquals(4000, (float) $invoice->paid_amount);
        $this->assertEquals(6000, (float) $invoice->balance_amount);
        $this->assertEquals('partially_paid', $invoice->status);
        $this->assertCount(1, $invoice->payments);

        // 2. Record Remaining Payment of Rs. 6,000 via POS Card
        $fullResponse = $this->actingAs($receptionist)->post("/invoices/{$invoice->id}/payments", [
            'amount' => 6000,
            'payment_method' => 'card',
            'transaction_reference' => 'POS-SLIP-9847',
        ]);

        $fullResponse->assertRedirect(route('invoices.show', $invoice->id));
        $invoice->refresh();

        $this->assertEquals(10000, (float) $invoice->paid_amount);
        $this->assertEquals(0, (float) $invoice->balance_amount);
        $this->assertEquals('paid', $invoice->status);
        $this->assertCount(2, $invoice->payments);
    }

    public function test_payment_exceeding_balance_is_rejected(): void
    {
        $clinic = Clinic::create(['name' => 'Metro Dental', 'slug' => 'metro-dental']);
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Ayesha Desk',
            'email' => 'ayesha@metrodental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Kamran Akmal',
            'phone' => '0321-4443322',
        ]);

        $invoice = Invoice::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'invoice_number' => 'INV-202610-0003',
            'total_amount' => 5000,
            'discount_amount' => 0,
            'paid_amount' => 0,
            'balance_amount' => 5000,
            'status' => 'unpaid',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Scaling',
            'unit_price' => 5000,
            'quantity' => 1,
            'total_price' => 5000,
        ]);

        // Attempt to pay Rs. 6,000 on a 5,000 balance
        $response = $this->actingAs($receptionist)->post("/invoices/{$invoice->id}/payments", [
            'amount' => 6000,
            'payment_method' => 'cash',
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertEquals(0, (float) $invoice->fresh()->paid_amount);
    }

    public function test_staff_can_view_and_print_payment_receipt(): void
    {
        $clinic = Clinic::create(['name' => 'Metro Dental', 'slug' => 'metro-dental']);
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Ayesha Desk',
            'email' => 'ayesha@metrodental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Sohail Tanvir',
            'phone' => '0300-1234567',
        ]);

        $invoice = Invoice::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'invoice_number' => 'INV-202610-0004',
            'total_amount' => 8000,
            'discount_amount' => 0,
            'paid_amount' => 8000,
            'balance_amount' => 0,
            'status' => 'paid',
        ]);

        $payment = Payment::create([
            'clinic_id' => $clinic->id,
            'invoice_id' => $invoice->id,
            'patient_id' => $patient->id,
            'received_by_user_id' => $receptionist->id,
            'amount' => 8000,
            'payment_method' => 'bank_transfer',
            'transaction_reference' => 'RAAST-88291039',
            'payment_date' => Carbon::now(),
        ]);

        $response = $this->actingAs($receptionist)->get("/payments/{$payment->id}/receipt");

        $response->assertStatus(200);
        $response->assertSee('Metro Dental');
        $response->assertSee('Sohail Tanvir');
        $response->assertSee('Rs. 8,000');
        $response->assertSee('RAAST-88291039');
        $response->assertSee('BANK TRANSFER');
    }

    public function test_cross_clinic_tenant_isolation_is_enforced_for_billing(): void
    {
        $clinicA = Clinic::create(['name' => 'Clinic A', 'slug' => 'clinic-a']);
        $clinicB = Clinic::create(['name' => 'Clinic B', 'slug' => 'clinic-b']);

        $userA = User::create([
            'clinic_id' => $clinicA->id,
            'name' => 'Staff A',
            'email' => 'staff.a@clinica.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $patientB = Patient::create([
            'clinic_id' => $clinicB->id,
            'name' => 'Patient B',
            'phone' => '0311-0000000',
        ]);

        $invoiceB = Invoice::create([
            'clinic_id' => $clinicB->id,
            'patient_id' => $patientB->id,
            'invoice_number' => 'INV-CLINIC-B-001',
            'total_amount' => 9000,
            'balance_amount' => 9000,
            'status' => 'unpaid',
        ]);

        // Staff from Clinic A attempts to view invoice of Clinic B
        $response = $this->actingAs($userA)->get("/invoices/{$invoiceB->id}");
        $response->assertStatus(404);

        // Staff from Clinic A attempts to record payment on Clinic B invoice
        $paymentResponse = $this->actingAs($userA)->post("/invoices/{$invoiceB->id}/payments", [
            'amount' => 5000,
            'payment_method' => 'cash',
        ]);
        $paymentResponse->assertStatus(404);
    }
}
