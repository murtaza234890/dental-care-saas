<x-layouts.app title="Clinic Dashboard - DentalCare">
    <!-- Welcome Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Welcome back, {{ $user->name }} 👋
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Clinic: <strong class="text-slate-700">{{ $clinic->name }}</strong> &bull; {{ $clinic->address ?? 'Islamabad' }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-700 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Currency: {{ $clinic->currency }}
            </span>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Today's Appointments -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Today's Appointments</span>
                <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $todayAppointments->count() }}</span>
                <span class="text-xs text-slate-500">patients scheduled</span>
            </div>
            <div class="mt-3 text-xs text-cyan-700 bg-cyan-50/60 rounded-lg px-2.5 py-1">
                {{ $todayAppointments->where('status', 'confirmed')->count() }} Confirmed &bull; {{ $todayAppointments->where('status', 'checked_in')->count() }} Checked In
            </div>
        </div>

        <!-- Total Patients -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Registered Patients</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalPatients }}</span>
                <span class="text-xs text-slate-500">clinic records</span>
            </div>
            <div class="mt-3 text-xs text-blue-700 bg-blue-50/60 rounded-lg px-2.5 py-1">
                Isolated to {{ $clinic->name }}
            </div>
        </div>

        <!-- Today Collections -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Today's Collections</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $clinic->currency }} {{ number_format($todayCollections, 0) }}</span>
            </div>
            <div class="mt-3 text-xs text-emerald-700 bg-emerald-50/60 rounded-lg px-2.5 py-1">
                Outstanding Balance: {{ $clinic->currency }} {{ number_format($totalOutstandingBalance, 0) }}
            </div>
        </div>

        <!-- Pending Follow-ups -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Due Follow-Ups (7 Days)</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $pendingFollowUpsCount }}</span>
                <span class="text-xs text-slate-500">recalls pending</span>
            </div>
            <div class="mt-3 text-xs text-amber-700 bg-amber-50/60 rounded-lg px-2.5 py-1">
                Crown fittings & dental hygiene
            </div>
        </div>
    </div>

    <!-- Appointments & Operations Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Today's Schedule Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Today's Appointment Schedule</h3>
                    <p class="text-xs text-slate-500">All scheduled patient appointments for today</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg">
                    {{ $todayAppointments->count() }} Records
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50/80 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Time</th>
                            <th class="px-6 py-3.5">Patient</th>
                            <th class="px-6 py-3.5">Doctor</th>
                            <th class="px-6 py-3.5">Treatment / Service</th>
                            <th class="px-6 py-3.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($todayAppointments as $app)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-xs font-semibold text-slate-800">
                                    {{ \Carbon\Carbon::parse($app->starts_at)->format('h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 text-xs">{{ $app->patient->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $app->patient->phone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-700">
                                    {{ $app->doctor->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600">
                                    {{ $app->service->name ?? 'General Consultation' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'confirmed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'checked_in' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                            'in_progress' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'completed' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'pending' => 'bg-slate-100 text-slate-700 border-slate-200',
                                            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ][$app->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border capitalize {{ $statusClasses }}">
                                        {{ str_replace('_', ' ', $app->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-xs text-slate-400">
                                    No appointments scheduled for today.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Role-Specific Action Card / Clinical Activity -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Clinic Security & Role Scope</h3>
                <p class="text-xs text-slate-500 mb-4">Current session permissions matrix</p>

                <div class="space-y-3 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <span class="text-slate-600">Active Tenant ID</span>
                        <span class="font-mono font-bold text-slate-800">#{{ $clinic->id }} ({{ $clinic->slug }})</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <span class="text-slate-600">Assigned Role</span>
                        <span class="font-bold text-cyan-700 capitalize">{{ str_replace('_', ' ', $user->role) }}</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <span class="text-slate-600">Confidential Medical Notes</span>
                        @if($user->isReceptionist())
                            <span class="text-rose-600 font-semibold">Restricted (No Access)</span>
                        @else
                            <span class="text-emerald-600 font-semibold">Authorized Access</span>
                        @endif
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <span class="text-slate-600">Financial Reports</span>
                        @if($user->isDentist())
                            <span class="text-slate-500 font-semibold">Read-only summaries</span>
                        @elseif($user->isReceptionist())
                            <span class="text-cyan-600 font-semibold">Billing collections only</span>
                        @else
                            <span class="text-emerald-600 font-semibold">Full Clinic Revenue</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <p class="text-[11px] text-slate-400">
                    System strictly enforces tenant isolation across all database queries via <code>BelongsToClinic</code>.
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
