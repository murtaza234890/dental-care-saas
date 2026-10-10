<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\Patient;
use App\Models\PlatformInvoice;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SaaSSubscriptionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Clinic $clinic;
    protected User $owner;
    protected User $platformOwner;
    protected SubscriptionPlan $starterPlan;
    protected SubscriptionPlan $proPlan;
    protected SubscriptionPlan $enterprisePlan;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Subscription Plans
        $this->starterPlan = SubscriptionPlan::create([
            'name' => 'Starter Clinic',
            'slug' => 'starter',
            'price_monthly' => 3500.00,
            'price_annual' => 35000.00,
            'currency' => 'PKR',
            'max_doctors' => 1,
            'max_staff' => 2,
            'max_patients' => 200,
            'features' => ['Booking', 'Patients'],
            'is_active' => true,
        ]);

        $this->proPlan = SubscriptionPlan::create([
            'name' => 'Professional Practice',
            'slug' => 'professional',
            'price_monthly' => 8500.00,
            'price_annual' => 85000.00,
            'currency' => 'PKR',
            'max_doctors' => 3,
            'max_staff' => 6,
            'max_patients' => 1500,
            'features' => ['Odontogram', 'Invoices', 'Reports'],
            'is_active' => true,
        ]);

        $this->enterprisePlan = SubscriptionPlan::create([
            'name' => 'Enterprise Dental Hospital',
            'slug' => 'enterprise',
            'price_monthly' => 18000.00,
            'price_annual' => 180000.00,
            'currency' => 'PKR',
            'max_doctors' => 10,
            'max_staff' => 25,
            'max_patients' => 10000,
            'features' => ['Unlimited', 'SLA Support'],
            'is_active' => true,
        ]);

        // Platform Owner
        $this->platformOwner = User::create([
            'name' => 'SaaS Super Owner',
            'email' => 'super@dentalcare.test',
            'phone' => '0300-9999999',
            'password' => Hash::make('password'),
            'role' => 'platform_owner',
            'is_active' => true,
        ]);

        // Demo Clinic
        $this->clinic = Clinic::create([
            'name' => 'Al-Razi Dental Clinic',
            'slug' => 'al-razi-dental',
            'phone' => '0300-1234567',
            'email' => 'info@alrazi.pk',
            'currency' => 'PKR',
            'status' => 'active',
        ]);

        $this->owner = User::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Dr. Farhan Razi',
            'email' => 'dr.farhan@alrazi.pk',
            'phone' => '0300-1122334',
            'password' => Hash::make('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);
    }

    public function test_clinic_registration_assigns_free_trial_subscription(): void
    {
        $payload = [
            'clinic_name' => 'Islamabad Smile Studio',
            'clinic_phone' => '0300-7654321',
            'address' => 'Blue Area, Islamabad',
            'owner_name' => 'Dr. Hamza Ali',
            'email' => 'hamza@smilestudio.pk',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $res = $this->post('/register-clinic', $payload);
        $res->assertRedirect(route('dashboard'));

        $newClinic = Clinic::where('slug', 'islamabad-smile-studio')->first();
        $this->assertNotNull($newClinic);

        $subscription = $newClinic->subscription;
        $this->assertNotNull($subscription);
        $this->assertEquals('trialing', $subscription->status);
        $this->assertEquals($this->starterPlan->id, $subscription->plan_id);
        $this->assertTrue($subscription->trial_ends_at->isFuture());
    }

    public function test_starter_plan_limit_blocks_exceeding_doctors(): void
    {
        // Set clinic to Starter Plan (max 1 doctor)
        Subscription::create([
            'clinic_id' => $this->clinic->id,
            'plan_id' => $this->starterPlan->id,
            'status' => 'active',
            'billing_interval' => 'monthly',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        // Create 1st doctor (owner is already doctor/admin, but let's ensure 1 dentist exists)
        $this->actingAs($this->owner);

        // Attempting to onboard a dentist when doctor limit of 1 is reached
        $newDentistPayload = [
            'name' => 'Dr. Second Dentist',
            'email' => 'second.dentist@alrazi.pk',
            'phone' => '0300-2223334',
            'role' => 'dentist',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'specialization' => 'Orthodontist',
            'consultation_fee' => 2000,
        ];

        // First doctor: Owner has role 'clinic_admin', so creating 1st dentist is allowed
        $res1 = $this->post('/admin/staff', $newDentistPayload);
        $res1->assertRedirect(route('admin.staff.index'));

        // Second doctor: Exceeds max_doctors = 1 for Starter plan
        $thirdDentistPayload = [
            'name' => 'Dr. Third Dentist (Exceeds Limit)',
            'email' => 'third.dentist@alrazi.pk',
            'phone' => '0300-3334445',
            'role' => 'dentist',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $res2 = $this->post('/admin/staff', $thirdDentistPayload);
        $res2->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'third.dentist@alrazi.pk']);
    }

    public function test_upgrading_subscription_plan_updates_tier_and_records_platform_invoice(): void
    {
        $this->actingAs($this->owner);

        // Upgrade to Professional Plan
        $upgradePayload = [
            'plan_id' => $this->proPlan->id,
            'billing_interval' => 'monthly',
        ];

        $res = $this->post('/admin/subscription/change-plan', $upgradePayload);
        $res->assertRedirect(route('admin.subscription'));
        $res->assertSessionHas('status');

        $sub = $this->clinic->fresh()->subscription;
        $this->assertNotNull($sub);
        $this->assertEquals($this->proPlan->id, $sub->plan_id);
        $this->assertEquals('active', $sub->status);

        $this->assertDatabaseHas('platform_invoices', [
            'clinic_id' => $this->clinic->id,
            'subscription_id' => $sub->id,
            'amount' => $this->proPlan->price_monthly,
            'status' => 'paid',
        ]);
    }

    public function test_subscription_renewal_extends_period_and_records_invoice(): void
    {
        $sub = Subscription::create([
            'clinic_id' => $this->clinic->id,
            'plan_id' => $this->proPlan->id,
            'status' => 'active',
            'billing_interval' => 'monthly',
            'current_period_start' => now()->startOfMonth(),
            'current_period_end' => now()->addDays(5),
        ]);

        $this->actingAs($this->owner);

        $res = $this->post('/admin/subscription/renew');
        $res->assertRedirect(route('admin.subscription'));

        $sub->refresh();
        $this->assertEquals('active', $sub->status);
        $this->assertTrue($sub->current_period_end->gt(now()->addDays(20)));

        $this->assertDatabaseHas('platform_invoices', [
            'clinic_id' => $this->clinic->id,
            'subscription_id' => $sub->id,
            'status' => 'paid',
        ]);
    }

    public function test_platform_owner_can_view_subscriptions_and_override_tier(): void
    {
        Subscription::create([
            'clinic_id' => $this->clinic->id,
            'plan_id' => $this->starterPlan->id,
            'status' => 'trialing',
            'billing_interval' => 'monthly',
            'trial_ends_at' => now()->addDays(7),
        ]);

        $this->actingAs($this->platformOwner);

        $res = $this->get('/platform/subscriptions');
        $res->assertOk();
        $res->assertSee('SaaS Subscriptions');
        $res->assertSee($this->clinic->name);

        // Super Owner overrides clinic tier to Enterprise with 45 days extension
        $overridePayload = [
            'plan_id' => $this->enterprisePlan->id,
            'status' => 'active',
            'billing_interval' => 'yearly',
            'extend_days' => 45,
        ];

        $patchRes = $this->patch("/platform/subscriptions/{$this->clinic->id}", $overridePayload);
        $patchRes->assertRedirect();

        $sub = $this->clinic->fresh()->subscription;
        $this->assertEquals($this->enterprisePlan->id, $sub->plan_id);
        $this->assertEquals('active', $sub->status);
        $this->assertEquals('yearly', $sub->billing_interval);
    }

    public function test_platform_owner_can_record_offline_wire_payment(): void
    {
        $this->actingAs($this->platformOwner);

        $paymentPayload = [
            'amount' => 25500.00,
            'extend_months' => 3,
            'payment_reference' => 'MEEZAN_BANK_WIRE_#998811',
        ];

        $res = $this->post("/platform/subscriptions/{$this->clinic->id}/record-payment", $paymentPayload);
        $res->assertRedirect();

        $this->assertDatabaseHas('platform_invoices', [
            'clinic_id' => $this->clinic->id,
            'amount' => 25500.00,
            'status' => 'paid',
            'payment_reference' => 'MEEZAN_BANK_WIRE_#998811',
        ]);
    }

    public function test_suspended_subscription_blocks_mutations_and_redirects_to_billing(): void
    {
        Subscription::create([
            'clinic_id' => $this->clinic->id,
            'plan_id' => $this->starterPlan->id,
            'status' => 'suspended',
            'billing_interval' => 'monthly',
            'current_period_end' => now()->subDay(),
        ]);

        $this->actingAs($this->owner);

        // Attempting to create a new patient while subscription is suspended
        $patientPayload = [
            'name' => 'Blocked Patient',
            'phone' => '0300-8888888',
        ];

        $res = $this->post('/patients', $patientPayload);
        $res->assertRedirect(route('admin.subscription'));
        $res->assertSessionHasErrors('subscription');

        // Confirms no patient was added
        $this->assertDatabaseMissing('patients', ['name' => 'Blocked Patient']);
    }

    public function test_expired_14_day_trial_locks_dashboard_and_redirects_to_billing(): void
    {
        Subscription::create([
            'clinic_id' => $this->clinic->id,
            'plan_id' => $this->starterPlan->id,
            'status' => 'trialing',
            'billing_interval' => 'monthly',
            'trial_ends_at' => now()->subHours(2),
        ]);

        $this->actingAs($this->owner);

        // Accessing dashboard when trial is expired should redirect to subscription billing page
        $res = $this->get('/dashboard');
        $res->assertRedirect(route('admin.subscription'));
        $res->assertSessionHasErrors('subscription');
        $this->assertTrue(session('trial_expired') === true);
    }

    public function test_credit_card_checkout_activates_subscription_and_unlocks_dashboard(): void
    {
        Subscription::create([
            'clinic_id' => $this->clinic->id,
            'plan_id' => $this->starterPlan->id,
            'status' => 'trialing',
            'billing_interval' => 'monthly',
            'trial_ends_at' => now()->subDay(),
        ]);

        $this->actingAs($this->owner);

        // Dashboard is initially blocked
        $this->get('/dashboard')->assertRedirect(route('admin.subscription'));

        // Customer fills credit card billing modal and upgrades to Professional Plan
        $checkoutPayload = [
            'plan_id' => $this->proPlan->id,
            'billing_interval' => 'monthly',
            'cardholder_name' => 'Dr. Muhammad Usman',
            'card_number' => '4242 4242 4242 4242',
            'card_expiry' => '08/29',
            'card_cvc' => '888',
            'billing_country' => 'Pakistan',
            'billing_zip' => '54000',
        ];

        $res = $this->post('/admin/subscription/checkout', $checkoutPayload);
        $res->assertRedirect(route('dashboard'));
        $res->assertSessionHas('status');

        $sub = $this->clinic->fresh()->subscription;
        $this->assertEquals('active', $sub->status);
        $this->assertEquals($this->proPlan->id, $sub->plan_id);
        $this->assertNull($sub->trial_ends_at);
        $this->assertStringContainsString('Visa •••• 4242', $sub->payment_method);

        // Platform SaaS invoice created with paid status
        $this->assertDatabaseHas('platform_invoices', [
            'clinic_id' => $this->clinic->id,
            'subscription_id' => $sub->id,
            'amount' => $this->proPlan->price_monthly,
            'status' => 'paid',
        ]);

        // Dashboard is now fully accessible
        $dashboardRes = $this->get('/dashboard');
        $dashboardRes->assertOk();
    }

    public function test_doctor_schedule_controller_blocks_adding_doctor_beyond_plan_limit(): void
    {
        Subscription::create([
            'clinic_id' => $this->clinic->id,
            'plan_id' => $this->starterPlan->id,
            'status' => 'active',
            'billing_interval' => 'monthly',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        // Create 1 dentist for the clinic. Starter plan max_doctors is 1.
        User::create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Dr. First Dentist',
            'email' => 'first.dentist@test.com',
            'password' => bcrypt('secret123'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $this->actingAs($this->owner);

        // Visiting GET /doctors/create redirects with plan limit message
        $getRes = $this->get('/doctors/create');
        $getRes->assertRedirect(route('admin.subscription'));
        $getRes->assertSessionHasErrors('subscription');

        // Attempting to post a new doctor is blocked
        $postRes = $this->post('/doctors', [
            'name' => 'Dr. Extra Dentist',
            'email' => 'extra.dentist@test.com',
            'phone' => '0300-9998877',
            'role' => 'dentist',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $postRes->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('users', ['email' => 'extra.dentist@test.com']);
    }

    public function test_patient_quota_limit_blocks_new_patient_when_cap_reached(): void
    {
        $this->starterPlan->update(['max_patients' => 2]);

        Subscription::create([
            'clinic_id' => $this->clinic->id,
            'plan_id' => $this->starterPlan->id,
            'status' => 'active',
            'billing_interval' => 'monthly',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        // Create 2 patients to reach the quota cap
        Patient::create(['clinic_id' => $this->clinic->id, 'name' => 'Patient One', 'phone' => '0300-1111111']);
        Patient::create(['clinic_id' => $this->clinic->id, 'name' => 'Patient Two', 'phone' => '0300-2222222']);

        $this->actingAs($this->owner);

        // GET /patients/create redirects to billing
        $getRes = $this->get('/patients/create');
        $getRes->assertRedirect(route('admin.subscription'));
        $getRes->assertSessionHasErrors('subscription');

        // POST /patients is blocked
        $postRes = $this->post('/patients', [
            'name' => 'Third Patient Cap Exceeded',
            'phone' => '0300-5551234',
        ]);

        $postRes->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('patients', ['name' => 'Third Patient Cap Exceeded']);
    }
}
