<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformInvoice;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Display clinic active subscription, plan tiers, usage meters, and billing history.
     */
    public function index(Request $request): View
    {
        $clinic = Auth::user()->clinic;
        $subscription = $clinic->subscription()->with('plan')->first();
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price_monthly')->get();
        $invoices = PlatformInvoice::where('clinic_id', $clinic->id)->latest()->paginate(10);

        $doctorsCount = $clinic->doctors()->count();
        $staffCount = $clinic->users()->count();
        $patientsCount = $clinic->patients()->count();

        $currentPlan = $subscription?->plan ?? $plans->firstWhere('slug', 'starter');

        return view('admin.subscription.index', compact(
            'clinic',
            'subscription',
            'plans',
            'invoices',
            'doctorsCount',
            'staffCount',
            'patientsCount',
            'currentPlan'
        ));
    }

    /**
     * Upgrade or change the clinic's SaaS subscription tier.
     */
    public function changePlan(Request $request): RedirectResponse
    {
        $clinic = Auth::user()->clinic;

        $validated = $request->validate([
            'plan_id' => ['required', 'exists:subscription_plans,id'],
            'billing_interval' => ['required', 'string', 'in:monthly,yearly'],
        ]);

        $newPlan = SubscriptionPlan::findOrFail($validated['plan_id']);
        $doctorsCount = $clinic->doctors()->count();
        $staffCount = $clinic->users()->count();

        // Prevent downgrade if current staff/doctors exceed target tier
        if (! $newPlan->hasUnlimitedDoctors() && $doctorsCount > $newPlan->max_doctors) {
            return redirect()->back()
                ->withErrors(['plan_id' => "Cannot switch to {$newPlan->name}. You currently have {$doctorsCount} doctors, but this plan allows a maximum of {$newPlan->max_doctors}."]);
        }

        if (! $newPlan->hasUnlimitedStaff() && $staffCount > $newPlan->max_staff) {
            return redirect()->back()
                ->withErrors(['plan_id' => "Cannot switch to {$newPlan->name}. You currently have {$staffCount} staff members, but this plan allows a maximum of {$newPlan->max_staff}."]);
        }

        $now = Carbon::now();
        $interval = $validated['billing_interval'];
        $periodEnd = $interval === 'yearly' ? $now->copy()->addYear() : $now->copy()->addMonth();
        $price = $interval === 'yearly' ? $newPlan->price_annual : $newPlan->price_monthly;

        $subscription = $clinic->subscription;

        if ($subscription) {
            $subscription->update([
                'plan_id' => $newPlan->id,
                'billing_interval' => $interval,
                'status' => 'active',
                'current_period_start' => $now,
                'current_period_end' => $periodEnd,
                'payment_method' => 'card',
            ]);
        } else {
            $subscription = Subscription::create([
                'clinic_id' => $clinic->id,
                'plan_id' => $newPlan->id,
                'billing_interval' => $interval,
                'status' => 'active',
                'current_period_start' => $now,
                'current_period_end' => $periodEnd,
                'payment_method' => 'card',
            ]);
        }

        // Record a SaaS billing invoice for this subscription upgrade
        PlatformInvoice::create([
            'clinic_id' => $clinic->id,
            'subscription_id' => $subscription->id,
            'invoice_number' => 'INV-SAAS-' . Carbon::now()->format('Ym') . '-' . strtoupper(substr(uniqid(), -4)),
            'amount' => $price,
            'currency' => $newPlan->currency,
            'status' => 'paid',
            'billing_period_start' => $now->toDateString(),
            'billing_period_end' => $periodEnd->toDateString(),
            'paid_at' => $now,
            'payment_reference' => 'CARD_TXN_' . strtoupper(uniqid()),
        ]);

        return redirect()->route('admin.subscription')
            ->with('status', "Your workspace plan was successfully updated to '{$newPlan->name}'.");
    }

    /**
     * Simulate renewing current plan subscription.
     */
    public function renew(Request $request): RedirectResponse
    {
        $clinic = Auth::user()->clinic;
        $subscription = $clinic->subscription;

        if (! $subscription || ! $subscription->plan) {
            return redirect()->route('admin.subscription')
                ->withErrors(['error' => 'No active plan found to renew. Please choose a subscription tier.']);
        }

        $plan = $subscription->plan;
        $now = Carbon::now();
        $interval = $subscription->billing_interval ?: 'monthly';

        // Calculate new period start and end
        $baseDate = ($subscription->current_period_end && $subscription->current_period_end->isFuture())
            ? $subscription->current_period_end
            : $now;

        $newPeriodEnd = $interval === 'yearly' ? $baseDate->copy()->addYear() : $baseDate->copy()->addMonth();
        $price = $interval === 'yearly' ? $plan->price_annual : $plan->price_monthly;

        $subscription->update([
            'status' => 'active',
            'current_period_start' => $now,
            'current_period_end' => $newPeriodEnd,
            'payment_method' => 'card',
        ]);

        PlatformInvoice::create([
            'clinic_id' => $clinic->id,
            'subscription_id' => $subscription->id,
            'invoice_number' => 'INV-SAAS-' . Carbon::now()->format('Ym') . '-' . strtoupper(substr(uniqid(), -4)),
            'amount' => $price,
            'currency' => $plan->currency,
            'status' => 'paid',
            'billing_period_start' => $now->toDateString(),
            'billing_period_end' => $newPeriodEnd->toDateString(),
            'paid_at' => $now,
            'payment_reference' => 'RENEWAL_TXN_' . strtoupper(uniqid()),
        ]);

        return redirect()->route('admin.subscription')
            ->with('status', "Subscription renewed successfully until {$newPeriodEnd->format('M d, Y')}.");
    }
}
