<x-layouts.app title="All Clinics Registry - Platform Super Owner">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                All Clinics Registry & SaaS Tenants
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Centralized tenant supervision, subscription status controls, and cross-clinic workspace telemetry.
            </p>
        </div>

        <div>
            <a href="{{ route('platform.telemetry') }}"
                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-600 to-teal-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                View Platform Telemetry & GTV
            </a>
        </div>
    </div>

    <!-- Alert / Status Notifications -->
    @if(session('status'))
        <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm font-semibold text-emerald-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    <!-- Global SaaS KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- 1. Total Clinics -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Clinics</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $metrics['total_clinics'] }}</span>
                <span class="text-[11px] font-semibold text-slate-500 mt-0.5 block">SaaS tenant instances</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>

        <!-- 2. Active Clinics -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Active Clinics</span>
                <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $metrics['active_clinics'] }}</span>
                <span class="text-[11px] font-semibold text-emerald-700 mt-0.5 block">
                    {{ $metrics['suspended_clinics'] }} suspended &bull; {{ $metrics['inactive_clinics'] }} paused
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- 3. Total Platform Patients -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Patients</span>
                <span class="text-2xl font-black text-cyan-700 mt-1 block">{{ number_format($metrics['total_patients']) }}</span>
                <span class="text-[11px] font-semibold text-slate-500 mt-0.5 block">Registered across all clinics</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <!-- 4. Platform Gross Turnover -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Platform GTV</span>
                <span class="text-2xl font-black text-indigo-700 mt-1 block">PKR {{ number_format($metrics['total_gtv'], 0) }}</span>
                <span class="text-[11px] font-semibold text-slate-500 mt-0.5 block">
                    PKR {{ number_format($metrics['total_collected'], 0) }} collected
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm mb-6">
        <form method="GET" action="{{ route('platform.clinics.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3 justify-between">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search clinic by name, slug, phone, email, or address..."
                    class="w-full pl-10 pr-4 py-2.5 text-xs font-semibold rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
            </div>

            <div class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()"
                    class="text-xs font-semibold px-3 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition text-slate-700">
                    <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="suspended" {{ $statusFilter === 'suspended' ? 'selected' : '' }}>Suspended Only</option>
                    <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>

                <button type="submit"
                    class="px-4 py-2.5 text-xs font-bold rounded-2xl bg-slate-900 text-white hover:bg-slate-800 transition shadow-sm">
                    Filter
                </button>

                @if($statusFilter !== 'all' || !empty($search))
                    <a href="{{ route('platform.clinics.index') }}"
                        class="px-3 py-2.5 text-xs font-semibold rounded-2xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Multi-Tenant Clinics Table -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-4">Clinic & Portal URL</th>
                        <th class="px-6 py-4">Contact Info</th>
                        <th class="px-6 py-4 text-center">Team Staff</th>
                        <th class="px-6 py-4 text-center">Patients</th>
                        <th class="px-6 py-4 text-center">Appointments</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($clinics as $c)
                        @php
                            $statusColors = [
                                'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'suspended' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'inactive' => 'bg-slate-100 text-slate-600 border-slate-200',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition group">
                            <!-- Clinic Name & Slug -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-extrabold text-slate-900 text-sm">
                                    <a href="{{ route('platform.clinics.show', $c) }}" class="hover:text-cyan-600 transition">
                                        {{ $c->name }}
                                    </a>
                                </div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-xs font-mono text-cyan-600">/clinic/{{ $c->slug }}</span>
                                    <a href="{{ route('clinic.show', $c->slug) }}" target="_blank" title="View public clinic website"
                                        class="text-slate-400 hover:text-cyan-600">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>
                            </td>

                            <!-- Contact Info -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600">
                                <div>{{ $c->phone ?? 'No phone' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $c->email ?? '-' }}</div>
                            </td>

                            <!-- Staff Count -->
                            <td class="px-6 py-4 text-center whitespace-nowrap font-bold text-slate-800">
                                {{ $c->users_count }}
                            </td>

                            <!-- Patients Count -->
                            <td class="px-6 py-4 text-center whitespace-nowrap font-bold text-slate-800">
                                {{ $c->patients_count }}
                            </td>

                            <!-- Appointments Count -->
                            <td class="px-6 py-4 text-center whitespace-nowrap font-bold text-cyan-700">
                                {{ $c->appointments_count }}
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $statusColors[$c->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                    @if($c->status === 'active')
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    @endif
                                    {{ $c->status }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Status Update Form Quick Dropdown -->
                                    <form method="POST" action="{{ route('platform.clinics.status', $c) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        @if($c->status === 'active')
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="submit"
                                                onclick="return confirm('Suspend {{ $c->name }}? Public booking will be immediately paused.');"
                                                class="px-2.5 py-1 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 border border-rose-200 transition"
                                                title="Suspend clinic tenant">
                                                Suspend
                                            </button>
                                        @else
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit"
                                                class="px-2.5 py-1 rounded-xl text-xs font-bold text-emerald-600 hover:bg-emerald-50 border border-emerald-200 transition"
                                                title="Reactivate clinic tenant">
                                                Activate
                                            </button>
                                        @endif
                                    </form>

                                    <!-- Inspect Details -->
                                    <a href="{{ route('platform.clinics.show', $c) }}"
                                        class="px-3 py-1 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition"
                                        title="View complete tenant dossier">
                                        Inspect
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No clinics found matching the specified search criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clinics->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $clinics->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
