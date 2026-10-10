<x-layouts.app title="Platform SaaS Subscriptions & MRR">
    <div class="space-y-8" x-data="{ activeModal: null, currentClinic: null }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-100 text-cyan-800 border border-cyan-200 uppercase tracking-wider">Super Owner</span>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">SaaS Subscriptions & MRR Dashboard</h1>
                </div>
                <p class="text-sm text-slate-500 mt-1">Global subscription lifecycle, recurring revenues, plan allocations, and tenant monetization.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('platform.telemetry') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 shadow-sm transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Platform Telemetry
                </a>
            </div>
        </div>

        @if(session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- SaaS Financial Telemetry Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- MRR -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 text-xs font-semibold">
                    <span>Monthly Recurring (MRR)</span>
                    <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-900 mt-2">PKR {{ number_format($metrics['mrr']) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Contracted monthly baseline</div>
            </div>

            <!-- ARR -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 text-xs font-semibold">
                    <span>Annual Run-Rate (ARR)</span>
                    <span class="p-1.5 rounded-lg bg-cyan-50 text-cyan-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-900 mt-2">PKR {{ number_format($metrics['arr']) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Annualized forward revenue</div>
            </div>

            <!-- Active vs Trial Subscriptions -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 text-xs font-semibold">
                    <span>Active vs Trial Accounts</span>
                    <span class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-2xl font-black text-emerald-600">{{ $metrics['active_subscriptions'] }}</span>
                    <span class="text-xs text-slate-400">paying /</span>
                    <span class="text-lg font-bold text-amber-600">{{ $metrics['trial_subscriptions'] }}</span>
                    <span class="text-xs text-slate-400">trial</span>
                </div>
                <div class="text-[11px] text-slate-400 mt-1">Total: {{ $metrics['total_clinics'] }} workspaces registered</div>
            </div>

            <!-- Total SaaS Billing Collected -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 text-xs font-semibold">
                    <span>Total SaaS Collected</span>
                    <span class="p-1.5 rounded-lg bg-teal-50 text-teal-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-900 mt-2">PKR {{ number_format($metrics['total_revenue']) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Platform subscription invoices</div>
            </div>
        </div>

        <!-- Clinics Subscriptions Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Clinic Workspaces & Subscription Status</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Manage subscription limits, manual overrides, trial extensions, and offline payments.</p>
                </div>

                <form method="GET" action="{{ route('platform.subscriptions.index') }}" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search clinic..."
                        class="px-3 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 w-48">
                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition">
                        Search
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/75 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-3.5">Clinic Workspace</th>
                            <th class="px-6 py-3.5">Subscribed Plan</th>
                            <th class="px-6 py-3.5">Subscription Status</th>
                            <th class="px-6 py-3.5">Usage / Quota</th>
                            <th class="px-6 py-3.5">Renewal / Expiry</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($clinics as $clinic)
                            @php
                                $sub = $clinic->subscription;
                                $plan = $sub?->plan;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $clinic->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">/clinic/{{ $clinic->slug }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($plan)
                                        <div class="font-semibold text-slate-800">{{ $plan->name }}</div>
                                        <div class="text-[11px] text-slate-400">{{ number_format($plan->price_monthly) }} {{ $plan->currency }}/mo ({{ $sub->billing_interval }})</div>
                                    @else
                                        <span class="text-slate-400 italic">No plan assigned</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if($sub?->status === 'active')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                                    @elseif($sub?->status === 'trialing')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">Trial ({{ $sub->daysLeftInTrial() }}d)</span>
                                    @elseif($sub?->status === 'past_due')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-50 text-rose-700 border border-rose-200">Past Due</span>
                                    @elseif($sub?->status === 'suspended')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-red-100 text-red-800 border border-red-300">Suspended</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-600">None</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-[11px] text-slate-600 space-y-0.5">
                                    <div>Doctors: <strong class="text-slate-800">{{ $clinic->doctors_count }}</strong> / {{ $plan ? ($plan->hasUnlimitedDoctors() ? '∞' : $plan->max_doctors) : '—' }}</div>
                                    <div>Staff: <strong class="text-slate-800">{{ $clinic->users_count }}</strong> / {{ $plan ? ($plan->hasUnlimitedStaff() ? '∞' : $plan->max_staff) : '—' }}</div>
                                    <div>Patients: <strong class="text-slate-800">{{ $clinic->patients_count }}</strong></div>
                                </td>

                                <td class="px-6 py-4 text-[11px]">
                                    @if($sub?->current_period_end)
                                        <div class="font-medium text-slate-800">{{ $sub->current_period_end->format('M d, Y') }}</div>
                                        <div class="text-slate-400">{{ $sub->daysLeftInPeriod() }} days remaining</div>
                                    @elseif($sub?->trial_ends_at)
                                        <div class="font-medium text-amber-700">{{ $sub->trial_ends_at->format('M d, Y') }}</div>
                                        <div class="text-slate-400">Trial ending</div>
                                    @else
                                        <span class="text-slate-400">N/A</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Edit Modal Trigger -->
                                        <button type="button"
                                            @click="activeModal = 'edit-{{ $clinic->id }}'"
                                            class="px-2.5 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition">
                                            Manage Plan
                                        </button>

                                        <!-- Record Payment Trigger -->
                                        <button type="button"
                                            @click="activeModal = 'pay-{{ $clinic->id }}'"
                                            class="px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 text-xs font-semibold transition">
                                            + Payment
                                        </button>
                                    </div>

                                    <!-- Edit Plan Modal -->
                                    <div x-show="activeModal === 'edit-{{ $clinic->id }}'" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                                        <div @click.away="activeModal = null" class="bg-white rounded-2xl max-w-md w-full p-6 text-left shadow-2xl space-y-4">
                                            <div class="flex items-center justify-between border-b pb-3">
                                                <h3 class="text-base font-bold text-slate-900">Manage Subscription: {{ $clinic->name }}</h3>
                                                <button type="button" @click="activeModal = null" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                                            </div>

                                            <form method="POST" action="{{ route('platform.subscriptions.update', $clinic) }}" class="space-y-4">
                                                @csrf
                                                @method('PATCH')

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Plan Tier</label>
                                                    <select name="plan_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                                                        @foreach($plans as $p)
                                                            <option value="{{ $p->id }}" {{ ($sub?->plan_id === $p->id) ? 'selected' : '' }}>
                                                                {{ $p->name }} (PKR {{ number_format($p->price_monthly) }}/mo)
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status</label>
                                                        <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                                                            <option value="active" {{ ($sub?->status === 'active') ? 'selected' : '' }}>Active</option>
                                                            <option value="trialing" {{ ($sub?->status === 'trialing') ? 'selected' : '' }}>Trialing</option>
                                                            <option value="past_due" {{ ($sub?->status === 'past_due') ? 'selected' : '' }}>Past Due</option>
                                                            <option value="suspended" {{ ($sub?->status === 'suspended') ? 'selected' : '' }}>Suspended</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Billing Interval</label>
                                                        <select name="billing_interval" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                                                            <option value="monthly" {{ ($sub?->billing_interval === 'monthly') ? 'selected' : '' }}>Monthly</option>
                                                            <option value="yearly" {{ ($sub?->billing_interval === 'yearly') ? 'selected' : '' }}>Yearly</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Extend Access (Days)</label>
                                                    <input type="number" name="extend_days" value="0" min="0" max="365" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                                                    <span class="text-[10px] text-slate-400">Add extra trial or paid period days to this clinic.</span>
                                                </div>

                                                <div class="flex items-center justify-end gap-2 pt-3 border-t">
                                                    <button type="button" @click="activeModal = null" class="px-3 py-2 text-xs font-semibold rounded-xl text-slate-600 hover:bg-slate-100">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-900 text-white hover:bg-slate-800">Save Subscription</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Record Offline Payment Modal -->
                                    <div x-show="activeModal === 'pay-{{ $clinic->id }}'" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                                        <div @click.away="activeModal = null" class="bg-white rounded-2xl max-w-md w-full p-6 text-left shadow-2xl space-y-4">
                                            <div class="flex items-center justify-between border-b pb-3">
                                                <h3 class="text-base font-bold text-slate-900">Record Payment: {{ $clinic->name }}</h3>
                                                <button type="button" @click="activeModal = null" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                                            </div>

                                            <form method="POST" action="{{ route('platform.subscriptions.record-payment', $clinic) }}" class="space-y-4">
                                                @csrf

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Amount Paid (PKR)</label>
                                                    <input type="number" name="amount" value="{{ $plan ? $plan->price_monthly : 8500 }}" required min="1" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Extend Validity By</label>
                                                    <select name="extend_months" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                                                        <option value="1">1 Month</option>
                                                        <option value="3">3 Months (Quarterly)</option>
                                                        <option value="6">6 Months (Half-Yearly)</option>
                                                        <option value="12">12 Months (1 Year)</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Transaction Reference</label>
                                                    <input type="text" name="payment_reference" placeholder="e.g. Bank Wire #TXN-998822" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                                                </div>

                                                <div class="flex items-center justify-end gap-2 pt-3 border-t">
                                                    <button type="button" @click="activeModal = null" class="px-3 py-2 text-xs font-semibold rounded-xl text-slate-600 hover:bg-slate-100">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">Record & Activate</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                    No clinics registered on platform yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($clinics->hasPages())
                <div class="px-6 py-3 border-t border-slate-200">
                    {{ $clinics->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
