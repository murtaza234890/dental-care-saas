<x-layouts.app title="Billing & Subscription Plans">
    <div class="space-y-8" x-data="{
        showCheckout: false,
        selectedPlanId: '{{ $currentPlan->id ?? ($plans->first()->id ?? 1) }}',
        selectedPlanName: '{{ $currentPlan->name ?? ($plans->first()->name ?? 'Starter Clinic') }}',
        selectedMonthlyPrice: {{ $currentPlan->price_monthly ?? 9999 }},
        selectedAnnualPrice: {{ $currentPlan->price_annual ?? 99990 }},
        billingInterval: 'monthly',
        get currentPrice() {
            return this.billingInterval === 'yearly' ? this.selectedAnnualPrice : this.selectedMonthlyPrice;
        },
        openCheckout(id, name, monthly, annual) {
            this.selectedPlanId = id;
            this.selectedPlanName = name;
            this.selectedMonthlyPrice = monthly;
            this.selectedAnnualPrice = annual;
            this.showCheckout = true;
        }
    }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Billing & Subscription Plans</h1>
                <p class="text-sm text-slate-500 mt-1">Manage your clinic's SaaS tier, resource quotas, payment card, and renewals.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" @click="showCheckout = true" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 text-white text-sm font-bold hover:from-cyan-700 hover:to-teal-700 shadow-md shadow-cyan-600/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    💳 Add Card & Upgrade
                </button>
            </div>
        </div>

        <!-- Trial Expired Paywall Banner -->
        @if(($subscription && $subscription->isTrialExpired()) || session('trial_expired'))
            <div class="p-6 rounded-2xl bg-gradient-to-r from-rose-600 via-red-600 to-amber-700 text-white shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-5 border border-rose-500/50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center flex-shrink-0 text-white shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider bg-white/20">Trial Expired</span>
                            <span class="text-xs text-rose-100 font-semibold">Dashboard Locked</span>
                        </div>
                        <h2 class="text-lg font-bold text-white mt-1">Your 14-Day Free Trial Has Ended</h2>
                        <p class="text-xs text-rose-100 max-w-2xl mt-0.5 leading-relaxed">
                            Your clinic patient files, doctor records, and medical dossiers remain 100% safe and intact. Add your payment card below to instantly unlock the clinical dashboard and resume operations.
                        </p>
                    </div>
                </div>
                <button type="button" @click="showCheckout = true" class="px-6 py-3 rounded-xl bg-white text-rose-900 font-black text-xs hover:bg-rose-50 shadow-lg transition flex-shrink-0">
                    Unlock Workspace With Card &rarr;
                </button>
            </div>
        @endif

        @if(session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1">
                @foreach($errors->all() as $err)
                    <p class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $err }}
                    </p>
                @endforeach
            </div>
        @endif

        <!-- Subscription Overview & Resource Quotas -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Active Subscription Card -->
            <div class="lg:col-span-1 bg-gradient-to-br from-slate-900 via-slate-800 to-cyan-950 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-8 -top-8 w-36 h-36 bg-cyan-500/10 rounded-full blur-2xl"></div>

                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase font-bold tracking-widest text-cyan-400">Current Plan</span>
                        @if($subscription?->status === 'active')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Active (Paid)</span>
                        @elseif($subscription?->status === 'trialing')
                            @if($subscription->isTrialExpired())
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30">Trial Expired</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">Trial ({{ $subscription->daysLeftInTrial() }}d left)</span>
                            @endif
                        @elseif($subscription?->status === 'past_due')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30">Past Due</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-700 text-slate-300">Inactive</span>
                        @endif
                    </div>

                    <h2 class="text-2xl font-black text-white mt-3">{{ $currentPlan->name ?? 'Starter Clinic' }}</h2>
                    <p class="text-xs text-slate-400 mt-1">Billing Interval: <span class="text-slate-200 capitalize font-medium">{{ $subscription->billing_interval ?? 'Monthly' }}</span></p>

                    <div class="mt-6 pt-5 border-t border-slate-700/60 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-300">
                            <span>Monthly Rate:</span>
                            <span class="font-bold text-white">{{ number_format($currentPlan->price_monthly ?? 9999) }} {{ $currentPlan->currency ?? 'PKR' }}</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span>Period End:</span>
                            <span class="font-bold text-white">{{ $subscription?->current_period_end ? $subscription->current_period_end->format('M d, Y') : ($subscription?->trial_ends_at ? $subscription->trial_ends_at->format('M d, Y') : 'N/A') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span>Payment Method:</span>
                            <span class="capitalize text-cyan-300 font-medium">{{ $subscription?->payment_method ?? 'Not set' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-700/60">
                    <button type="button" @click="showCheckout = true" class="w-full py-2.5 rounded-xl bg-cyan-500 text-slate-900 font-extrabold text-xs hover:bg-cyan-400 transition shadow-md">
                        Upgrade or Update Payment Card
                    </button>
                </div>
            </div>

            <!-- Resource Quotas & Usage Meters -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Resource Quotas & Allocation</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Real-time usage versus your current subscribed plan limits.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Doctors Quota -->
                    @php
                        $maxDocs = $currentPlan->max_doctors ?? 1;
                        $isUnlimitedDocs = $currentPlan->hasUnlimitedDoctors();
                        $docPercent = $isUnlimitedDocs ? 20 : min(100, round(($doctorsCount / $maxDocs) * 100));
                    @endphp
                    <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 space-y-2">
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-600">
                            <span>Dentists / Doctors</span>
                            <span class="text-slate-900 font-bold">{{ $doctorsCount }} / {{ $isUnlimitedDocs ? '∞' : $maxDocs }}</span>
                        </div>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                            <div class="bg-cyan-600 h-2 rounded-full" style="width: {{ $docPercent }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 block">{{ $isUnlimitedDocs ? 'Unlimited capacity' : ($maxDocs - $doctorsCount . ' doctor slots open') }}</span>
                    </div>

                    <!-- Staff Quota -->
                    @php
                        $maxStaff = $currentPlan->max_staff ?? 2;
                        $isUnlimitedStaff = $currentPlan->hasUnlimitedStaff();
                        $staffPercent = $isUnlimitedStaff ? 15 : min(100, round(($staffCount / $maxStaff) * 100));
                    @endphp
                    <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 space-y-2">
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-600">
                            <span>Staff Members</span>
                            <span class="text-slate-900 font-bold">{{ $staffCount }} / {{ $isUnlimitedStaff ? '∞' : $maxStaff }}</span>
                        </div>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                            <div class="bg-teal-600 h-2 rounded-full" style="width: {{ $staffPercent }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 block">{{ $isUnlimitedStaff ? 'Unlimited team' : ($maxStaff - $staffCount . ' staff seats open') }}</span>
                    </div>

                    <!-- Patients Quota -->
                    @php
                        $maxPatients = $currentPlan->max_patients ?? 200;
                        $isUnlimitedPatients = $currentPlan->hasUnlimitedPatients();
                        $patientPercent = $isUnlimitedPatients ? 10 : min(100, round(($patientsCount / $maxPatients) * 100));
                    @endphp
                    <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 space-y-2">
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-600">
                            <span>Active Patients</span>
                            <span class="text-slate-900 font-bold">{{ $patientsCount }} / {{ $isUnlimitedPatients ? '∞' : number_format($maxPatients) }}</span>
                        </div>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $patientPercent }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 block">{{ $isUnlimitedPatients ? 'Unlimited records' : 'Patient dossier cap' }}</span>
                    </div>
                </div>

                <!-- Enabled Features Checklist -->
                <div class="pt-2">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Enabled Tier Features:</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach(($currentPlan->features ?? []) as $feat)
                            <div class="flex items-center gap-2 text-xs text-slate-600">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ $feat }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Tier Switcher / Upgrade Section -->
        <div class="space-y-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Select Subscription Tier</h2>
                <p class="text-xs text-slate-500">Pick the plan that fits your dental practice size and workflow requirements.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                    @php
                        $isCurrent = $subscription && $subscription->plan_id === $plan->id;
                    @endphp
                    <div class="rounded-2xl border {{ $isCurrent ? 'border-cyan-500 ring-2 ring-cyan-500/20 bg-cyan-50/30' : 'border-slate-200 bg-white hover:border-slate-300' }} p-6 shadow-sm flex flex-col justify-between transition">
                        <div>
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-slate-900">{{ $plan->name }}</h3>
                                @if($isCurrent && $subscription?->status === 'active')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-cyan-600 text-white">Active Plan</span>
                                @elseif($isCurrent && $subscription?->status === 'trialing')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500 text-white">Trial Plan</span>
                                @endif
                            </div>

                            <div class="mt-4">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-3xl font-extrabold text-slate-900">{{ number_format($plan->price_monthly) }}</span>
                                    <span class="text-sm font-semibold text-slate-500">{{ $plan->currency }}/mo</span>
                                </div>
                                <span class="text-[11px] text-slate-400">or {{ number_format($plan->price_annual) }} {{ $plan->currency }}/year (save ~17%)</span>
                            </div>

                            <div class="mt-6 pt-5 border-t border-slate-100 space-y-2.5 text-xs text-slate-600">
                                <div class="flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>{{ $plan->hasUnlimitedDoctors() ? 'Unlimited' : $plan->max_doctors }} Dentist / Doctor(s)</span>
                                </div>
                                <div class="flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>{{ $plan->hasUnlimitedStaff() ? 'Unlimited' : $plan->max_staff }} Staff Members</span>
                                </div>
                                <div class="flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>{{ $plan->hasUnlimitedPatients() ? 'Unlimited' : number_format($plan->max_patients) }} Patient Dossiers</span>
                                </div>

                                <div class="pt-2 border-t border-slate-100 space-y-1.5">
                                    @foreach(($plan->features ?? []) as $feat)
                                        <div class="flex items-center gap-2 text-[11px] text-slate-500">
                                            <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>{{ $feat }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100">
                            <button type="button"
                                @click="openCheckout({{ $plan->id }}, '{{ $plan->name }}', {{ $plan->price_monthly }}, {{ $plan->price_annual }})"
                                class="w-full py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-cyan-700 transition shadow-sm flex items-center justify-center gap-2">
                                <span>💳 Pay & Upgrade with Card</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- SaaS Billing & Invoices History -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">SaaS Billing & Invoices History</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Automated invoices generated for workspace subscriptions.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/75 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-3.5">Invoice #</th>
                            <th class="px-6 py-3.5">Billing Period</th>
                            <th class="px-6 py-3.5">Amount</th>
                            <th class="px-6 py-3.5">Payment Method</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5">Paid At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($invoices as $inv)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $inv->invoice_number }}</td>
                                <td class="px-6 py-4">{{ $inv->billing_period_start ? $inv->billing_period_start->format('M d, Y') . ' — ' . $inv->billing_period_end->format('M d, Y') : 'Standard Billing' }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">{{ number_format($inv->amount) }} {{ $inv->currency }}</td>
                                <td class="px-6 py-4 uppercase text-[11px] font-mono text-slate-500">{{ $inv->payment_reference ?? 'Credit Card' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $inv->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500">{{ $inv->paid_at ? $inv->paid_at->format('M d, Y H:i') : 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                    No platform billing invoices recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($invoices->hasPages())
                <div class="px-6 py-3 border-t border-slate-200">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>

        <!-- Interactive Credit Card Checkout Modal -->
        <div x-show="showCheckout" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="showCheckout = false" class="bg-white rounded-3xl max-w-lg w-full p-7 text-left shadow-2xl space-y-6 relative border border-slate-200">
                <div class="flex items-center justify-between border-b pb-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Add Payment Card & Upgrade</h3>
                        <p class="text-xs text-slate-500">Secure 256-bit encrypted card billing checkout</p>
                    </div>
                    <button type="button" @click="showCheckout = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('admin.subscription.checkout') }}" class="space-y-4">
                    @csrf

                    <!-- Selected Plan Preview -->
                    <div class="p-4 rounded-2xl bg-cyan-50/60 border border-cyan-200 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-cyan-700 tracking-wider block">Selected Plan</span>
                            <span class="text-sm font-extrabold text-slate-900" x-text="selectedPlanName"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-black text-cyan-800" x-text="'PKR ' + currentPrice.toLocaleString()"></span>
                            <span class="text-[10px] text-slate-500 block" x-text="billingInterval === 'yearly' ? 'billed annually' : 'billed monthly'"></span>
                        </div>
                    </div>

                    <!-- Hidden Plan ID -->
                    <input type="hidden" name="plan_id" :value="selectedPlanId">

                    <!-- Billing Interval Selector -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Billing Interval</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer transition hover:bg-slate-50" :class="billingInterval === 'monthly' ? 'bg-cyan-50/50 border-cyan-400 ring-2 ring-cyan-400/20' : ''">
                                <input type="radio" name="billing_interval" value="monthly" x-model="billingInterval" class="text-cyan-600">
                                <div>
                                    <span class="text-xs font-bold text-slate-800 block">Monthly</span>
                                    <span class="text-[10px] text-slate-500">Billed each month</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer transition hover:bg-slate-50" :class="billingInterval === 'yearly' ? 'bg-cyan-50/50 border-cyan-400 ring-2 ring-cyan-400/20' : ''">
                                <input type="radio" name="billing_interval" value="yearly" x-model="billingInterval" class="text-cyan-600">
                                <div>
                                    <span class="text-xs font-bold text-slate-800 block">Yearly <span class="text-emerald-600 font-extrabold text-[10px]">Save ~17%</span></span>
                                    <span class="text-[10px] text-slate-500">12 months upfront</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Cardholder Name -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cardholder Full Name</label>
                        <input type="text" name="cardholder_name" required value="{{ auth()->user()->name }}" placeholder="Dr. Ahmed Khan"
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <!-- Card Number -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1 flex items-center justify-between">
                            <span>Card Number</span>
                            <span class="text-[10px] font-mono text-slate-400 flex items-center gap-1">
                                💳 Visa / Mastercard
                            </span>
                        </label>
                        <input type="text" name="card_number" required placeholder="4242 4242 4242 4242" maxlength="19"
                            class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-300 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <!-- Expiry & CVC -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Expiry (MM/YY)</label>
                            <input type="text" name="card_expiry" required placeholder="12/28" maxlength="5"
                                class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-300 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">CVC / CVV</label>
                            <input type="text" name="card_cvc" required placeholder="888" maxlength="4"
                                class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl border border-slate-300 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                        </div>
                    </div>

                    <!-- Billing Address / Zip -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Country</label>
                            <input type="text" name="billing_country" value="Pakistan" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Postal / ZIP Code</label>
                            <input type="text" name="billing_zip" value="44000" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <button type="button" @click="showCheckout = false" class="px-4 py-2.5 text-xs font-semibold rounded-xl text-slate-600 hover:bg-slate-100">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md shadow-emerald-600/25 transition">
                            🔒 Pay & Activate Workspace
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
