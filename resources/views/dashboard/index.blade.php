<x-layouts.app title="Clinic Dashboard - DentalCare">
    <!-- Early Trial Upgrade Alert Banner -->
    @if($clinic->subscription && $clinic->subscription->isTrialing())
        <div class="mb-8 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-slate-900 via-cyan-950 to-slate-900 text-white shadow-lg border border-cyan-800/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center flex-shrink-0 text-cyan-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 px-2.5 py-0.5 rounded-full">14-Day Free Trial</span>
                        <span class="text-xs text-slate-300 font-semibold">{{ $clinic->subscription->daysLeftInTrial() }} days remaining</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1 max-w-2xl leading-relaxed">You are currently testing DentalCare with Starter quotas. Upgrade before the trial elapses to unlock additional doctor chairs and ensure uninterrupted clinical operations.</p>
                </div>
            </div>
            <a href="{{ route('admin.subscription') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold shadow-sm transition flex-shrink-0">
                <span>Upgrade Plan</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    @endif

    <!-- Welcome Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                Welcome back, {{ $user->name }}
            </h2>
            <p class="mt-1 text-xs sm:text-sm text-slate-500 flex items-center gap-2">
                <span>{{ $clinic->name }}</span>
                <span class="text-slate-300">&bull;</span>
                <span>{{ $clinic->address ?? 'Main Branch' }}</span>
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Currency: {{ $clinic->currency }}
            </span>
            <a href="{{ route('appointments.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>New Appointment</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
        <!-- Today's Appointments -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Today's Schedule</span>
                <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $todayAppointments->count() }}</span>
                <span class="text-xs text-slate-500">patients</span>
            </div>
            <div class="mt-3 text-xs text-cyan-700 bg-cyan-50/70 rounded-lg px-2.5 py-1.5 flex items-center justify-between">
                <span>{{ $todayAppointments->where('status', 'confirmed')->count() }} Confirmed</span>
                <span>{{ $todayAppointments->where('status', 'checked_in')->count() }} Checked In</span>
            </div>
        </div>

        <!-- Total Patients -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Registered Patients</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalPatients }}</span>
                <span class="text-xs text-slate-500">active profiles</span>
            </div>
            <div class="mt-3 text-xs text-blue-700 bg-blue-50/70 rounded-lg px-2.5 py-1.5">
                Isolated to {{ $clinic->name }}
            </div>
        </div>

        <!-- Today Collections -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Today's Collections</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $clinic->currency }} {{ number_format($todayCollections, 0) }}</span>
            </div>
            <div class="mt-3 text-xs text-emerald-700 bg-emerald-50/70 rounded-lg px-2.5 py-1.5 flex items-center justify-between">
                <span>Receivables:</span>
                <span class="font-semibold">{{ $clinic->currency }} {{ number_format($totalOutstandingBalance, 0) }}</span>
            </div>
        </div>

        <!-- Pending Follow-ups -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Due Recalls (7 Days)</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $pendingFollowUpsCount }}</span>
                <span class="text-xs text-slate-500">tasks pending</span>
            </div>
            <div class="mt-3 text-xs text-amber-700 bg-amber-50/70 rounded-lg px-2.5 py-1.5">
                Post-op checks & recall reminders
            </div>
        </div>
    </div>

    <!-- Appointments & Operations Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Today's Schedule Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Today's Appointment Schedule</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Live roster for {{ \Carbon\Carbon::today()->format('l, M d') }}</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg">
                        {{ $todayAppointments->count() }} Appointments
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50/80 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3">Time</th>
                                <th class="px-5 py-3">Patient</th>
                                <th class="px-5 py-3">Doctor</th>
                                <th class="px-5 py-3">Procedure</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($todayAppointments as $app)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-5 py-3.5 whitespace-nowrap font-mono text-xs font-semibold text-slate-800">
                                        {{ \Carbon\Carbon::parse($app->starts_at)->format('h:i A') }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <div class="font-bold text-slate-900 text-xs">{{ $app->patient->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $app->patient->phone }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-xs text-slate-700">
                                        {{ $app->doctor->name }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                        {{ $app->service->name ?? 'General Consultation' }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        @php
                                            $statusClasses = [
                                                'confirmed' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
                                                'checked_in' => 'bg-cyan-50 text-cyan-700 ring-1 ring-cyan-600/20',
                                                'in_progress' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
                                                'completed' => 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20',
                                                'pending' => 'bg-slate-100 text-slate-700 ring-1 ring-slate-600/20',
                                                'cancelled' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20',
                                            ][$app->status] ?? 'bg-slate-100 text-slate-700 ring-1 ring-slate-600/20';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold capitalize {{ $statusClasses }}">
                                            {{ str_replace('_', ' ', $app->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-xs text-slate-400">
                                        No appointments scheduled for today.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs">
                <a href="{{ route('appointments.index') }}" class="font-semibold text-cyan-700 hover:text-cyan-800 transition inline-flex items-center gap-1">
                    <span>Open Appointments Calendar</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <span class="text-slate-400 text-[11px]">Auto-refreshed</span>
            </div>
        </div>

        <!-- Role Scope & Practice Information -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 sm:p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Session & Permissions</h3>
                <p class="text-xs text-slate-500 mb-4">Current authenticated scope</p>

                <div class="space-y-3 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <span class="text-slate-600">Tenant Slug</span>
                        <span class="font-mono font-bold text-slate-800">/{{ $clinic->slug }}</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <span class="text-slate-600">Active Role</span>
                        <span class="font-bold text-cyan-700 capitalize">{{ str_replace('_', ' ', $user->role) }}</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <span class="text-slate-600">Clinical Dossiers</span>
                        @if($user->isReceptionist())
                            <span class="text-rose-600 font-semibold">Masked Notes</span>
                        @else
                            <span class="text-emerald-600 font-semibold">Authorized Full Access</span>
                        @endif
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <span class="text-slate-600">Revenue Ledger</span>
                        @if($user->isDentist())
                            <span class="text-slate-500 font-semibold">Personal Cases</span>
                        @elseif($user->isReceptionist())
                            <span class="text-cyan-600 font-semibold">Counter Receipts Only</span>
                        @else
                            <span class="text-emerald-600 font-semibold">Clinic Financials</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Tenant isolation is enforced across queries via <code class="font-mono text-slate-600">BelongsToClinic</code>.
                </p>
            </div>
        </div>
    </div>

    <!-- Today's Recall & Follow-Up Tasks Widget -->
    @if(isset($todayFollowUps) && $todayFollowUps->count() > 0)
        <div class="mt-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span>Clinical Recalls Due Today</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 ring-1 ring-amber-600/20">
                            {{ $todayFollowUps->count() }} Due
                        </span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Post-op follow-ups, suture removals, and routine recall calls</p>
                </div>
                <a href="{{ route('follow-ups.index', ['filter' => 'today']) }}" class="text-xs font-bold text-cyan-600 hover:text-cyan-700 transition inline-flex items-center gap-1">
                    <span>View Recall Queue</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($todayFollowUps as $tfu)
                    <div class="p-4 sm:px-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:bg-slate-50/60 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-bold text-xs shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <a href="{{ route('patients.show', $tfu->patient_id) }}" class="text-xs font-bold text-slate-900 hover:text-cyan-600 transition">
                                    {{ $tfu->patient->name }}
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono ml-2">{{ $tfu->patient->phone }}</span>
                                <span class="block text-xs text-slate-600 mt-0.5">{{ $tfu->reason }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-center">
                            <form action="{{ route('follow-ups.status', $tfu->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="contacted">
                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition border border-slate-200">
                                    Mark Contacted
                                </button>
                            </form>
                            <a href="{{ route('appointments.create', ['patient_id' => $tfu->patient_id]) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-cyan-600 hover:bg-cyan-700 text-white transition shadow-sm">
                                Book Slot
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-layouts.app>
