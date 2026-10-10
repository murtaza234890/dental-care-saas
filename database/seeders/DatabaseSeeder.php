<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DentalService;
use App\Models\DoctorProfile;
use App\Models\FollowUp;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\TreatmentRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with demo data.
     */
    public function run(): void
    {
        // 1. Platform Super Owner
        $platformOwner = User::create([
            'name' => 'SaaS Platform Owner',
            'email' => 'admin@dentalcare.test',
            'phone' => '0300-0000000',
            'password' => Hash::make('password'),
            'role' => 'platform_owner',
            'is_active' => true,
        ]);

        // 2. Demo Clinic A: City Dental & Implant Centre
        $clinic = Clinic::create([
            'name' => 'City Dental & Implant Centre',
            'slug' => 'city-dental',
            'phone' => '0300-1234567',
            'email' => 'info@citydental.pk',
            'address' => 'Plaza 14, Sector F-7 Markaz, Islamabad',
            'currency' => 'PKR',
            'timezone' => 'Asia/Karachi',
            'status' => 'active',
        ]);

        // Explicitly set clinic context for seed operations
        app()->instance('current_clinic_id', $clinic->id);

        // 3. Clinic Admin / Lead Dentist
        $leadDoctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Ahmed Khan',
            'email' => 'dr.ahmed@citydental.test',
            'phone' => '0301-1112233',
            'password' => Hash::make('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'clinic_id' => $clinic->id,
            'user_id' => $leadDoctor->id,
            'specialization' => 'Endodontist & Implant Specialist',
            'consultation_fee' => 2000.00,
            'slot_duration_minutes' => 30,
            'bio' => 'BDS, FCPS (Endodontics) with 10+ years experience in root canal & implants.',
            'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
            'start_time' => '10:00:00',
            'end_time' => '20:00:00',
            'break_start' => '14:00:00',
            'break_end' => '15:00:00',
        ]);

        // 4. Associate Dentist
        $assocDoctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Sara Ali',
            'email' => 'dr.sara@citydental.test',
            'phone' => '0302-2223344',
            'password' => Hash::make('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'clinic_id' => $clinic->id,
            'user_id' => $assocDoctor->id,
            'specialization' => 'Orthodontist & Aesthetic Dentist',
            'consultation_fee' => 1500.00,
            'slot_duration_minutes' => 30,
            'bio' => 'BDS, MSc (Orthodontics), specializing in braces and smile makeover.',
            'working_days' => ['monday', 'wednesday', 'friday', 'saturday'],
            'start_time' => '11:00:00',
            'end_time' => '19:00:00',
            'break_start' => '14:00:00',
            'break_end' => '15:00:00',
        ]);

        // 5. Front Desk Receptionist
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Ayesha Noor',
            'email' => 'receptionist@citydental.test',
            'phone' => '0303-3334455',
            'password' => Hash::make('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        // 6. Dental Services Catalog
        $services = [
            'consultation' => DentalService::create([
                'clinic_id' => $clinic->id,
                'name' => 'Dental Consultation & Examination',
                'description' => 'Comprehensive oral examination, intraoral photos & treatment plan.',
                'standard_fee' => 1500.00,
                'estimated_duration_minutes' => 20,
                'is_active' => true,
            ]),
            'scaling' => DentalService::create([
                'clinic_id' => $clinic->id,
                'name' => 'Scaling & Polishing (Teeth Cleaning)',
                'description' => 'Ultrasonic scaling followed by prophylaxis polishing.',
                'standard_fee' => 3500.00,
                'estimated_duration_minutes' => 30,
                'is_active' => true,
            ]),
            'composite' => DentalService::create([
                'clinic_id' => $clinic->id,
                'name' => 'Composite Tooth Restoration (Filling)',
                'description' => 'Light-cured tooth-colored composite filling per surface.',
                'standard_fee' => 4000.00,
                'estimated_duration_minutes' => 30,
                'is_active' => true,
            ]),
            'rct' => DentalService::create([
                'clinic_id' => $clinic->id,
                'name' => 'Root Canal Treatment (Molar)',
                'description' => 'Complete bi-canal or multi-canal pulp extirpation, cleaning & obturation.',
                'standard_fee' => 15000.00,
                'estimated_duration_minutes' => 45,
                'is_active' => true,
            ]),
            'crown' => DentalService::create([
                'clinic_id' => $clinic->id,
                'name' => 'Zirconia Ceramic Crown',
                'description' => 'High-strength monolithic zirconia crown with shade matching.',
                'standard_fee' => 22000.00,
                'estimated_duration_minutes' => 30,
                'is_active' => true,
            ]),
        ];

        // 7. Sample Patients
        $patient1 = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Hamza Tariq',
            'phone' => '0321-5551234',
            'email' => 'hamza.tariq@gmail.test',
            'date_of_birth' => '1995-04-12',
            'gender' => 'male',
            'blood_group' => 'B+',
            'address' => 'House 45, Street 2, G-11/3, Islamabad',
            'medical_history' => 'Allergic to Penicillin. Mild anxiety during dental procedures.',
        ]);

        $patient2 = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Bilal Sheikh',
            'phone' => '0333-6667890',
            'email' => 'bilal.sheikh@gmail.test',
            'date_of_birth' => '1988-11-20',
            'gender' => 'male',
            'blood_group' => 'O+',
            'address' => 'Bahria Town Phase 4, Rawalpindi',
            'medical_history' => 'Type 2 Diabetic (Controlled). Reports gum bleeding.',
        ]);

        $patient3 = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Zainab Bibi',
            'phone' => '0345-7778901',
            'email' => 'zainab.bibi@gmail.test',
            'date_of_birth' => '2001-08-15',
            'gender' => 'female',
            'blood_group' => 'A+',
            'address' => 'F-10/2, Islamabad',
            'medical_history' => 'No known systemic health conditions or allergies.',
        ]);

        $today = Carbon::today()->format('Y-m-d');
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        // 8. Sample Appointments
        $app1 = Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient1->id,
            'doctor_id' => $leadDoctor->id,
            'service_id' => $services['rct']->id,
            'appointment_date' => $today,
            'starts_at' => '10:00:00',
            'ends_at' => '10:45:00',
            'status' => 'confirmed',
            'patient_notes' => 'Severe throbbing pain in lower right jaw since 2 days.',
        ]);

        $app2 = Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient2->id,
            'doctor_id' => $assocDoctor->id,
            'service_id' => $services['scaling']->id,
            'appointment_date' => $today,
            'starts_at' => '11:30:00',
            'ends_at' => '12:00:00',
            'status' => 'checked_in',
            'patient_notes' => 'Routine 6-month cleaning visit.',
        ]);

        $app3 = Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient3->id,
            'doctor_id' => $assocDoctor->id,
            'service_id' => $services['composite']->id,
            'appointment_date' => $tomorrow,
            'starts_at' => '15:00:00',
            'ends_at' => '15:30:00',
            'status' => 'pending',
            'patient_notes' => 'Dark spot on upper molar, food packing.',
        ]);

        // 9. Sample Clinical Treatment Record
        $treatmentRecord = TreatmentRecord::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient1->id,
            'doctor_id' => $leadDoctor->id,
            'appointment_id' => $app1->id,
            'service_id' => $services['rct']->id,
            'tooth_numbers' => '46',
            'diagnosis' => 'Irreversible Pulpitis with symptomatic apical periodontitis in tooth #46.',
            'treatment_rendered' => 'Access cavity prepared under Local Anesthesia (Lignocaine 2%). Working length determined with apex locator. Canals instrumented and medicated with Calcium Hydroxide paste. Closed with temporary dressing (Cavit).',
            'clinical_notes' => 'Patient tolerated procedure well. Note: Advised to avoid hard foods on right side until permanent crown is fitted.',
            'prescription' => "1. Tab Augmentin 625mg (1+1) for 5 days\n2. Tab Brufen 400mg (1+1+1) SOS for pain\n3. Warm saline mouth rinses 3x daily",
        ]);

        // 10. Sample Financial Invoice & Partial Payment
        $invoice = Invoice::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient1->id,
            'appointment_id' => $app1->id,
            'invoice_number' => 'INV-2026-0001',
            'total_amount' => 15000.00,
            'discount_amount' => 0.00,
            'paid_amount' => 5000.00,
            'balance_amount' => 10000.00,
            'status' => 'partially_paid',
            'due_date' => Carbon::now()->addDays(14)->format('Y-m-d'),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'service_id' => $services['rct']->id,
            'description' => 'Root Canal Treatment (Molar #46)',
            'unit_price' => 15000.00,
            'quantity' => 1,
            'total_price' => 15000.00,
        ]);

        Payment::create([
            'clinic_id' => $clinic->id,
            'invoice_id' => $invoice->id,
            'patient_id' => $patient1->id,
            'received_by_user_id' => $receptionist->id,
            'amount' => 5000.00,
            'payment_method' => 'cash',
            'transaction_reference' => 'RCP-001',
            'payment_date' => Carbon::now(),
            'notes' => 'Advance payment received on visit 1. Remaining balance Rs. 10,000 due on crown fitting.',
        ]);

        // 11. Sample Follow-up Task
        FollowUp::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient1->id,
            'treatment_record_id' => $treatmentRecord->id,
            'due_date' => Carbon::now()->addDays(14)->format('Y-m-d'),
            'reason' => 'Root Canal Step 2: Canal obturation & Crown impression for tooth #46',
            'status' => 'pending',
            'staff_notes' => 'Patient needs call 1 day before due date to confirm time slot.',
        ]);
    }
}
