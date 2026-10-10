<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\PlatformInvoice;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $starter = SubscriptionPlan::updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter Clinic',
                'price_monthly' => 9999.00,
                'price_annual' => 99990.00,
                'currency' => 'PKR',
                'max_doctors' => 1,
                'max_staff' => 2,
                'max_patients' => 200,
                'features' => [
                    'Online Booking Portal',
                    'Patient Directory & Dossier',
                    'Appointments Calendar',
                    'Basic Invoicing & Receipts',
                ],
                'is_active' => true,
            ]
        );

        $pro = SubscriptionPlan::updateOrCreate(
            ['slug' => 'professional'],
            [
                'name' => 'Professional Practice',
                'price_monthly' => 19999.00,
                'price_annual' => 199990.00,
                'currency' => 'PKR',
                'max_doctors' => 3,
                'max_staff' => 6,
                'max_patients' => 1500,
                'features' => [
                    'All Starter Features',
                    'Interactive FDI Odontogram',
                    'Treatment Plans & Prescriptions',
                    'Daily Follow-Up Recall Queue',
                    'Doctor Rostering & Timetables',
                    'Financial Analytics & Revenue Reports',
                ],
                'is_active' => true,
            ]
        );

        $enterprise = SubscriptionPlan::updateOrCreate(
            ['slug' => 'enterprise'],
            [
                'name' => 'Enterprise Dental Hospital',
                'price_monthly' => 29999.00,
                'price_annual' => 299990.00,
                'currency' => 'PKR',
                'max_doctors' => 10,
                'max_staff' => 25,
                'max_patients' => 10000,
                'features' => [
                    'All Professional Features',
                    'Multi-Chair Timetable',
                    'Doctor Productivity Analytics',
                    'Priority SLA Support',
                    'Unlimited Data Export',
                ],
                'is_active' => true,
            ]
        );

        // Assign subscriptions to existing clinics if they don't have one
        $clinics = Clinic::all();
        foreach ($clinics as $index => $clinic) {
            if (! $clinic->subscription()->exists()) {
                if ($index === 0) {
                    // Clinic 1: Active Professional subscription with a paid invoice
                    $sub = Subscription::create([
                        'clinic_id' => $clinic->id,
                        'plan_id' => $pro->id,
                        'status' => 'active',
                        'billing_interval' => 'monthly',
                        'current_period_start' => Carbon::now()->startOfMonth(),
                        'current_period_end' => Carbon::now()->addDays(20),
                        'payment_method' => 'card',
                    ]);

                    PlatformInvoice::create([
                        'clinic_id' => $clinic->id,
                        'subscription_id' => $sub->id,
                        'invoice_number' => 'INV-SAAS-' . Carbon::now()->format('Ym') . '-001',
                        'amount' => $pro->price_monthly,
                        'currency' => 'PKR',
                        'status' => 'paid',
                        'billing_period_start' => Carbon::now()->startOfMonth()->toDateString(),
                        'billing_period_end' => Carbon::now()->addDays(20)->toDateString(),
                        'paid_at' => Carbon::now()->startOfMonth(),
                        'payment_reference' => 'STRIPE_CH_MOCK_' . uniqid(),
                    ]);
                } else {
                    // Other clinics: 14-day free trial on Starter or Pro
                    Subscription::create([
                        'clinic_id' => $clinic->id,
                        'plan_id' => $starter->id,
                        'status' => 'trialing',
                        'billing_interval' => 'monthly',
                        'trial_ends_at' => Carbon::now()->addDays(12),
                        'current_period_start' => Carbon::now(),
                        'current_period_end' => Carbon::now()->addDays(12),
                        'payment_method' => 'trial',
                    ]);
                }
            }
        }
    }
}
