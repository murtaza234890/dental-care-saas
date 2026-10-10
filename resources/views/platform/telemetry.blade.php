<x-layouts.app title="Platform Telemetry & GTV - DentalCare SaaS">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-cyan-600 mb-1">
                <a href="{{ route('platform.clinics.index') }}" class="hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    All Clinics Registry
                </a>
                <span>/</span>
                <span class="text-slate-400">System Telemetry</span>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 flex items-center gap-2">
                Platform Telemetry & SaaS Metrics 📡
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Cross-tenant system utilization, cumulative gross transaction value (GTV), and operational health indicators.
            </p>
        </div>

        <div>
            <a href="{{ route('platform.clinics.index') }}"
                class="px-4 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-2xl hover:bg-slate-50 transition shadow-sm">
                View All Clinics
            </a>
        </div>
    </div>

    <!-- Infrastructure Status Bar -->
    <div class="mb-8 p-4 rounded-3xl bg-slate-900 text-white flex flex-wrap items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-xs font-bold tracking-wide">Multi-Tenant Scoping Isolation: <strong class="text-emerald-400">ENFORCED (BelongsToClinic)</strong></span>
        </div>
        <div class="flex items-center gap-6 text-xs text-slate-400">
            <span>Database: <strong class="text-white">MySQL (dentalcare)</strong></span>
            <span>Tenants: <strong class="text-cyan-400">{{ $totalClinics }}</strong></span>
            <span>Active Ratio: <strong class="text-emerald-400">{{ $totalClinics > 0 ? round(($activeClinics / $totalClinics) * 100) : 100 }}%</strong></span>
        </div>
    </div>

    <!-- 4 Big SaaS Financial & Scale KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- 1. Cumulative Platform GTV -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Platform GTV</span>
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-black text-slate-900 font-mono">PKR {{ number_format($totalGTV, 0) }}</span>
                <span class="text-[11px] font-semibold text-slate-500 mt-1 block">Cumulative invoices billed</span>
            </div>
        </div>

        <!-- 2. Realized Platform Collections -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Realized Collections</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-black text-emerald-600 font-mono">PKR {{ number_format($totalCollected, 0) }}</span>
                <span class="text-[11px] font-semibold text-emerald-700 mt-1 block">Due balance: PKR {{ number_format($totalReceivables, 0) }}</span>
            </div>
        </div>

        <!-- 3. Platform Appointments Volume -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Booked Appointments</span>
                <div class="w-10 h-10 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalAppointments) }}</span>
                <span class="text-[11px] font-semibold text-slate-500 mt-1 block">{{ $completionRate }}% fulfillment rate</span>
            </div>
        </div>

        <!-- 4. Global Patients Base -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Patients</span>
                <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalPatients) }}</span>
                <span class="text-[11px] font-semibold text-slate-500 mt-1 block">{{ number_format($totalTreatments) }} treatments charted</span>
            </div>
        </div>
    </div>

    <!-- 2-Column: Team Breakdown & Tenant Leaderboard -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- User Roles Breakdown Across Platform -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 mb-1 flex items-center gap-2">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Cross-Tenant User Roster Distribution
            </h3>
            <p class="text-xs text-slate-400 mb-4">{{ $totalUsers }} active practitioners and administrative staff across all clinics.</p>

            <div class="space-y-4">
                @php
                    $roles = [
                        ['label' => 'Dental Surgeons / Specialists', 'count' => $dentistsCount, 'color' => 'bg-cyan-600'],
                        ['label' => 'Clinic Administrators', 'count' => $adminsCount, 'color' => 'bg-purple-600'],
                        ['label' => 'Frontdesk Receptionists', 'count' => $receptionistsCount, 'color' => 'bg-amber-500'],
                    ];
                @endphp

                @foreach($roles as $r)
                    @php
                        $pct = $totalUsers > 0 ? round(($r['count'] / $totalUsers) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span class="text-slate-800">{{ $r['label'] }}</span>
                            <span class="text-slate-900 font-mono">{{ $r['count'] }} <span class="text-slate-400">({{ $pct }}%)</span></span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="h-full rounded-full {{ $r['color'] }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tenant Clinics Volume Leaderboard -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 mb-1 flex items-center gap-2">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Top Tenant Clinics Leaderboard
            </h3>
            <p class="text-xs text-slate-400 mb-4">Ranked by appointment bookings & patient adoption volume.</p>

            <div class="space-y-3">
                @forelse($leaderboardClinics as $idx => $lead)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-cyan-600/10 text-cyan-700 font-black text-xs flex items-center justify-center">
                                #{{ $idx + 1 }}
                            </span>
                            <div>
                                <a href="{{ route('platform.clinics.show', $lead) }}" class="font-bold text-slate-900 hover:text-cyan-600">
                                    {{ $lead->name }}
                                </a>
                                <span class="block text-[10px] text-slate-400 font-mono">/clinic/{{ $lead->slug }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-cyan-700">{{ $lead->appointments_count }} appts</span>
                            <span class="block text-[10px] text-slate-400">{{ $lead->patients_count }} patients &bull; {{ $lead->users_count }} staff</span>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-slate-400">
                        No clinics registered yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
