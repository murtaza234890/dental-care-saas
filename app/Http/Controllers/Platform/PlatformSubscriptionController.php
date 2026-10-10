<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\PlatformInvoice;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformSubscriptionController extends Controller
{
    /**
     * Display Platform SaaS Subscriptions Directory & MRR Telemetry.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'all');
        $planFilter = $request->query('plan', 'all');
        $search = trim($request->query('search', ''));

        $query = Clinic::with(['subscription.plan'])->withCount(['doctors', 'users', 'patients']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $clinics = $query->paginate(15)->withQueryString();
        $plans = SubscriptionPlan::where('is_active', true)->get();

        // Calculate Global SaaS Metrics
        $totalClinics = Clinic::count();
        $activeSubsCount = Subscription::where('status', 'active')->count();
        $trialSubsCount = Subscription::where('status', 'trialing')->count();
        $suspendedSubsCount = Subscription::where('status', 'suspended')
            ->orWhere(function ($q) {
                $q->where('status', 'past_due');
            })->count();

        // Calculate MRR
        $mrr = 0.0;
        $activeSubscriptions = Subscription::where('status', 'active')->with('plan')->get();
        foreach ($activeSubscriptions as $sub) {
            if ($sub->plan) {
                if ($sub->billing_interval === 'yearly') {
                    $mrr += (float) ($sub->plan->price_annual / 12);
                } else {
                    $mrr += (float) $sub->plan->price_monthly;
                }
            }
        }

        $arr = $mrr * 12;
        $totalSaaSRevenue = (float) PlatformInvoice::where('status', 'paid')->sum('amount');
        $failedInvoicesCount = PlatformInvoice::where('status', 'failed')->count();

        $metrics = [
            'total_clinics' => $totalClinics,
            'active_subscriptions' => $activeSubsCount,
            'trial_subscriptions' => $trialSubsCount,
            'suspended_subscriptions' => $suspendedSubsCount,
            'mrr' => $mrr,
            'arr' => $arr,
            'total_revenue' => $totalSaaSRevenue,
            'failed_invoices' => $failedInvoicesCount,
        ];

        return view('platform.subscriptions.index', compact(
            'clinics',
            'plans',
            'metrics',
            'statusFilter',
            'planFilter',
            'search'
        ));
    }

    /**
     * Super Admin manual override for clinic subscription tier and status.
     */
    public function updateSubscription(Request $request, Clinic $clinic): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'exists:subscription_plans,id'],
            'status' => ['required', 'string', 'in:active,trialing,past_due,suspended'],
            'billing_interval' => ['required', 'string', 'in:monthly,yearly'],
            'extend_days' => ['nullable', 'integer', 'min:0', 'max:365'],
        ]);

        $sub = $clinic->subscription;
        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        $now = Carbon::now();
        $extendDays = (int) ($validated['extend_days'] ?? 0);

        if ($sub) {
            $currentEnd = ($sub->current_period_end && $sub->current_period_end->isFuture())
                ? $sub->current_period_end
                : $now;

            $newEnd = $extendDays > 0
                ? $currentEnd->copy()->addDays($extendDays)
                : ($sub->current_period_end ?? $now->copy()->addMonth());

            $sub->update([
                'plan_id' => $plan->id,
                'status' => $validated['status'],
                'billing_interval' => $validated['billing_interval'],
                'current_period_end' => $newEnd,
                'trial_ends_at' => $validated['status'] === 'trialing' ? $newEnd : null,
            ]);
        } else {
            $newEnd = $now->copy()->addDays($extendDays > 0 ? $extendDays : 30);
            Subscription::create([
                'clinic_id' => $clinic->id,
                'plan_id' => $plan->id,
                'status' => $validated['status'],
                'billing_interval' => $validated['billing_interval'],
                'current_period_start' => $now,
                'current_period_end' => $newEnd,
                'trial_ends_at' => $validated['status'] === 'trialing' ? $newEnd : null,
            ]);
        }

        return redirect()->back()
            ->with('status', "Subscription for clinic '{$clinic->name}' updated successfully.");
    }

    /**
     * Super Admin record offline/wire payment for clinic subscription.
     */
    public function recordPayment(Request $request, Clinic $clinic): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'extend_months' => ['required', 'integer', 'min:1', 'max:24'],
        ]);

        $sub = $clinic->subscription;
        if (! $sub) {
            $defaultPlan = SubscriptionPlan::first();
            $sub = Subscription::create([
                'clinic_id' => $clinic->id,
                'plan_id' => $defaultPlan->id,
                'status' => 'active',
                'current_period_start' => Carbon::now(),
                'current_period_end' => Carbon::now()->addMonths($validated['extend_months']),
            ]);
        } else {
            $baseDate = ($sub->current_period_end && $sub->current_period_end->isFuture())
                ? $sub->current_period_end
                : Carbon::now();

            $sub->update([
                'status' => 'active',
                'current_period_end' => $baseDate->copy()->addMonths($validated['extend_months']),
            ]);
        }

        PlatformInvoice::create([
            'clinic_id' => $clinic->id,
            'subscription_id' => $sub->id,
            'invoice_number' => 'INV-SAAS-' . Carbon::now()->format('Ym') . '-' . strtoupper(substr(uniqid(), -4)),
            'amount' => $validated['amount'],
            'currency' => 'PKR',
            'status' => 'paid',
            'billing_period_start' => Carbon::now()->toDateString(),
            'billing_period_end' => $sub->current_period_end->toDateString(),
            'paid_at' => Carbon::now(),
            'payment_reference' => $validated['payment_reference'] ?? 'OFFLINE_PAYMENT',
        ]);

        return redirect()->back()
            ->with('status', "Manual subscription payment of PKR " . number_format($validated['amount']) . " recorded for clinic '{$clinic->name}'.");
    }
}
