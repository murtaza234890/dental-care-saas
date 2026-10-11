<x-layouts.app title="Patients Directory - DentalCare">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                Patients Directory
            </h2>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Clinical records, medical histories, patient demographics, and active treatment dossiers.
            </p>
        </div>

        <div>
            <a href="{{ route('patients.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition active:scale-[0.99]">
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Register New Patient</span>
            </a>
        </div>
    </div>

    <!-- Stats & Alert Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Total Patients</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1 block">{{ $totalPatients }}</span>
                <span class="text-[11px] text-slate-500 mt-0.5 block">Active medical profiles</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-rose-500 block">Medical Alerts</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-rose-700 mt-1 block">{{ $patientsWithAlerts }}</span>
                <span class="text-[11px] text-slate-500 mt-0.5 block">Allergies & chronic conditions</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 block">Isolation Status</span>
                <span class="text-sm font-bold text-emerald-800 mt-2 block">Tenant Scoped</span>
                <span class="text-[11px] text-slate-500 mt-0.5 block">Multi-tenant privacy active</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm mb-6">
        <form action="{{ route('patients.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <!-- Search Bar -->
            <div class="relative flex-1 min-w-[240px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, phone, or email..."
                    class="w-full rounded-xl border border-slate-300 pl-9 pr-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Blood Group Filter -->
            <select name="blood_group" onchange="this.form.submit()"
                class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                <option value="">Blood Group</option>
                @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                    <option value="{{ $bg }}" {{ $bloodGroup === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                @endforeach
            </select>

            <!-- Gender Filter -->
            <select name="gender" onchange="this.form.submit()"
                class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                <option value="">All Genders</option>
                <option value="male" {{ $gender === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ $gender === 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ $gender === 'other' ? 'selected' : '' }}>Other</option>
            </select>

            <!-- Medical Alert Filter -->
            <label class="flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 cursor-pointer hover:bg-slate-50 transition">
                <input type="checkbox" name="medical_alert" value="1" {{ $hasMedicalAlert ? 'checked' : '' }} onchange="this.form.submit()"
                    class="rounded text-rose-600 focus:ring-rose-500">
                <span>With Medical Alert</span>
            </label>

            @if($search || $bloodGroup || $gender || $hasMedicalAlert)
                <a href="{{ route('patients.index') }}" class="text-xs font-semibold text-rose-600 hover:underline px-2">
                    Clear Filters
                </a>
            @endif
        </form>
    </div>

    <!-- Patients Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Patient Name & Demographics</th>
                        <th class="px-6 py-4">Contact Info</th>
                        <th class="px-6 py-4">Medical Alert / Allergies</th>
                        <th class="px-6 py-4">Visits Count</th>
                        <th class="px-6 py-4">Outstanding Balance</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($patients as $patient)
                        @php
                            $balance = $patient->totalOutstandingBalance();
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Name & Demographics -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('patients.show', $patient->id) }}" class="font-bold text-slate-900 text-xs hover:text-cyan-700 flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-800 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($patient->name, 0, 1)) }}
                                    </span>
                                    <span>{{ $patient->name }}</span>
                                </a>
                                <div class="text-[11px] text-slate-400 mt-1 pl-10.5">
                                    @if($patient->date_of_birth)
                                        <span>{{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} yrs</span> &bull;
                                    @endif
                                    <span class="capitalize">{{ $patient->gender ?? 'Not specified' }}</span>
                                    @if($patient->blood_group)
                                        &bull; <span class="font-bold text-slate-600">{{ $patient->blood_group }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Contact -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <span class="font-mono font-semibold text-slate-800 block">{{ $patient->phone }}</span>
                                @if($patient->email)
                                    <span class="text-[11px] text-slate-400 block truncate max-w-[180px]">{{ $patient->email }}</span>
                                @endif
                            </td>

                            <!-- Medical Alert -->
                            <td class="px-6 py-4 text-xs">
                                @if($patient->medical_history)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                        {{ \Illuminate\Support\Str::limit($patient->medical_history, 40) }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">None reported</span>
                                @endif
                            </td>

                            <!-- Visits -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-slate-700">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-medium text-[11px]">
                                    {{ $patient->appointments_count }} visits
                                </span>
                            </td>

                            <!-- Outstanding Balance -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                @if($balance > 0)
                                    <span class="font-mono font-bold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200 text-xs">
                                        Rs. {{ number_format($balance, 0) }}
                                    </span>
                                @else
                                    <span class="font-medium text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 text-xs">
                                        Settled (Rs. 0)
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('patients.show', $patient->id) }}"
                                        class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs transition">
                                        View Dossier
                                    </a>

                                    <a href="{{ route('patients.edit', $patient->id) }}"
                                        class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Edit Profile">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-xs text-slate-400">
                                No patients found matching your search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($patients->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $patients->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
