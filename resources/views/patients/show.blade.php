<x-layouts.app title="Patient Dossier - {{ $patient->name }}">
    <div x-data="{ activeTab: 'overview' }" class="space-y-6">
        <!-- Top Bar & Breadcrumb -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('patients.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-600 hover:text-cyan-700 transition mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Patients Directory
                </a>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        {{ $patient->name }}
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        File #PT-{{ str_pad($patient->id, 5, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-3">
                <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Schedule Appointment
                </a>

                <a href="{{ route('patients.edit', $patient->id) }}"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Profile
                </a>
            </div>
        </div>

        <!-- High-Priority Clinical Alert Banner (If Medical History is Present) -->
        @if ($patient->medical_history)
            <div class="rounded-3xl bg-rose-50 border-2 border-rose-300 p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center font-bold text-lg shrink-0 shadow-md shadow-rose-600/30">
                        ⚠️
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-xs font-black tracking-wider uppercase text-rose-800">
                                Critical Clinical Medical Alert and Drug Allergies
                            </span>
                            <span class="text-[11px] font-bold text-rose-700 bg-rose-100/80 px-2 py-0.5 rounded-md">
                                Mandatory Pre-Treatment Review
                            </span>
                        </div>
                        <p class="mt-1 text-sm font-semibold text-rose-950 leading-relaxed">
                            {{ $patient->medical_history }}
                        </p>
                        <p class="mt-1.5 text-[11px] text-rose-700">
                            Check contraindications before prescribing NSAIDs, local anesthesia with vasoconstrictor, or invasive surgical extraction.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Patient Header Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-center">
                <!-- Patient Profile Summary -->
                <div class="md:col-span-2 flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-600 to-teal-600 text-white flex items-center justify-center font-extrabold text-2xl shadow-lg shadow-cyan-600/25 shrink-0">
                        {{ substr($patient->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl font-extrabold text-slate-900">{{ $patient->name }}</h3>
                            @if($patient->blood_group)
                                <span class="px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 text-xs font-extrabold">
                                    {{ $patient->blood_group }}
                                </span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-slate-500 font-medium">
                            @if($patient->date_of_birth)
                                <span>{{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} years old</span>
                                <span>&bull;</span>
                            @endif
                            <span class="capitalize">{{ $patient->gender ?? 'Unspecified' }}</span>
                            <span>&bull;</span>
                            <span class="font-mono">{{ $patient->phone }}</span>
                        </div>
                        @if($patient->email)
                            <div class="text-xs text-slate-400 mt-0.5 font-mono">
                                {{ $patient->email }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Financial Quick Stat -->
                <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200/60 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Outstanding Due</span>
                        <div class="mt-1 font-mono text-xl font-extrabold {{ $outstandingBalance > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                            Rs. {{ number_format($outstandingBalance, 0) }}
                        </div>
                        <span class="text-[10px] text-slate-500">
                            {{ $outstandingBalance > 0 ? 'Requires settlement' : 'Account in good standing' }}
                        </span>
                    </div>
                    <div class="w-10 h-10 rounded-xl {{ $outstandingBalance > 0 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }} flex items-center justify-center font-bold">
                        ₨
                    </div>
                </div>

                <!-- Visits Stat -->
                <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200/60 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Total Clinical Visits</span>
                        <div class="mt-1 font-mono text-xl font-extrabold text-cyan-800">
                            {{ $patient->appointments->count() }}
                        </div>
                        <span class="text-[10px] text-slate-500">
                            Registered {{ $patient->created_at->format('M d, Y') }}
                        </span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold">
                        🦷
                    </div>
                </div>
            </div>
        </div>

        <!-- Dossier Tabs Navigation -->
        <div class="border-b border-slate-200">
            <nav class="flex space-x-2 sm:space-x-4 overflow-x-auto" aria-label="Tabs">
                <button type="button" @click="activeTab = 'overview'"
                    :class="activeTab === 'overview' ? 'border-cyan-600 text-cyan-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                    class="whitespace-nowrap py-3 px-3 border-b-2 text-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profile & Demographics
                </button>

                <button type="button" @click="activeTab = 'appointments'"
                    :class="activeTab === 'appointments' ? 'border-cyan-600 text-cyan-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                    class="whitespace-nowrap py-3 px-3 border-b-2 text-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Appointments
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700">{{ $patient->appointments->count() }}</span>
                </button>

                <button type="button" @click="activeTab = 'treatments'"
                    :class="activeTab === 'treatments' ? 'border-cyan-600 text-cyan-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                    class="whitespace-nowrap py-3 px-3 border-b-2 text-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Clinical Records
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700">{{ $treatmentRecords->count() }}</span>
                </button>

                <button type="button" @click="activeTab = 'billing'"
                    :class="activeTab === 'billing' ? 'border-cyan-600 text-cyan-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                    class="whitespace-nowrap py-3 px-3 border-b-2 text-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    Invoices & Ledger
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700">{{ $patient->invoices->count() }}</span>
                </button>

                <button type="button" @click="activeTab = 'followups'"
                    :class="activeTab === 'followups' ? 'border-cyan-600 text-cyan-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                    class="whitespace-nowrap py-3 px-3 border-b-2 text-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Follow-Ups & Recalls
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700">{{ $patient->followUps->count() }}</span>
                </button>
            </nav>
        </div>

        <!-- Tab 1: Overview & Profile -->
        <div x-show="activeTab === 'overview'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Demographic Information -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                        <span>Personal Demographics</span>
                        <a href="{{ route('patients.edit', $patient->id) }}" class="text-cyan-600 hover:underline lowercase font-semibold">edit</a>
                    </h4>

                    <dl class="space-y-3 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">Full Legal Name</dt>
                            <dd class="font-bold text-slate-900">{{ $patient->name }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">Primary Phone</dt>
                            <dd class="font-mono font-bold text-slate-900">{{ $patient->phone }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">Email Address</dt>
                            <dd class="text-slate-800">{{ $patient->email ?? 'Not provided' }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">Date of Birth</dt>
                            <dd class="text-slate-800">
                                @if($patient->date_of_birth)
                                    {{ $patient->date_of_birth->format('M d, Y') }} ({{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} years old)
                                @else
                                    Not provided
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">Gender</dt>
                            <dd class="capitalize text-slate-800">{{ $patient->gender ?? 'Not specified' }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">Blood Group</dt>
                            <dd class="font-bold text-slate-900">{{ $patient->blood_group ?? 'Not specified' }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <dt class="text-slate-500">Residential Address</dt>
                            <dd class="text-slate-800 text-right max-w-[260px]">{{ $patient->address ?? 'Not recorded' }}</dd>
                        </div>
                        <div class="flex justify-between py-1">
                            <dt class="text-slate-500">Registered On</dt>
                            <dd class="text-slate-600">{{ $patient->created_at->format('M d, Y - h:i A') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Medical Alert & Emergency Information -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm flex flex-col justify-between">
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-rose-600 mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                            <span>Clinical Safety & Allergies</span>
                            <span class="w-2.5 h-2.5 rounded-full {{ $patient->medical_history ? 'bg-rose-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                        </h4>

                        @if ($patient->medical_history)
                            <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 mb-4 text-xs">
                                <span class="font-bold text-rose-800 block mb-1">Active Medical Alert:</span>
                                <p class="text-rose-950 font-medium leading-relaxed">{{ $patient->medical_history }}</p>
                            </div>
                        @else
                            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 mb-4 text-xs text-emerald-800">
                                <span class="font-bold block mb-1">No Active Medical Alerts</span>
                                Patient has no recorded chronic systemic disorders or medication allergies.
                            </div>
                        @endif

                        <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100 space-y-2 text-xs">
                            <div class="font-bold text-slate-700">Standard Clinic Pre-Op Checklist:</div>
                            <ul class="space-y-1.5 text-slate-600 list-disc list-inside">
                                <li>Verify blood pressure before local anesthetic administration.</li>
                                <li>Confirm zero contraindications with current medications.</li>
                                <li>Signed informed consent required before surgical procedures.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-slate-400">Tenant Data Isolation: Active</span>
                        <a href="{{ route('patients.edit', $patient->id) }}"
                            class="text-xs font-bold text-rose-700 hover:underline">
                            Update Medical Alerts &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Appointments -->
        <div x-show="activeTab === 'appointments'" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Appointment History</h3>
                    <p class="text-xs text-slate-500">All past and upcoming consultations scheduled for this patient.</p>
                </div>
                <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-cyan-600 text-white text-xs font-bold hover:bg-cyan-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Appointment
                </a>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">Date & Time</th>
                            <th class="px-6 py-4">Dentist / Doctor</th>
                            <th class="px-6 py-4">Service</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Notes</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($patient->appointments as $apt)
                            @php
                                $statusColors = [
                                    'confirmed' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'checked_in' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'in_progress' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($apt->appointment_date)->format('M d, Y') }}</div>
                                    <div class="font-mono text-slate-500 text-[11px]">
                                        {{ \Carbon\Carbon::parse($apt->starts_at)->format('h:i A') }} - {{ \Carbon\Carbon::parse($apt->ends_at)->format('h:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold text-slate-800">{{ $apt->doctor->name }}</span>
                                    <span class="block text-[11px] text-slate-400">{{ $apt->doctor->doctorProfile->specialization ?? 'General Dentist' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-semibold text-slate-700">{{ $apt->service->name ?? 'General Consultation' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold border uppercase tracking-wider {{ $statusColors[$apt->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                        {{ str_replace('_', ' ', $apt->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500 max-w-[200px] truncate">
                                    {{ $apt->patient_notes ?? $apt->cancellation_reason ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('appointments.index', ['date' => \Carbon\Carbon::parse($apt->appointment_date)->format('Y-m-d')]) }}"
                                        class="text-cyan-600 hover:text-cyan-800 font-bold">
                                        View in Schedule &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-xs text-slate-400">
                                    No appointments scheduled yet for this patient.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 3: Clinical Treatment Records -->
        <div x-show="activeTab === 'treatments'" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Clinical Treatment Dossier</h3>
                    <p class="text-xs text-slate-500">Procedures performed, diagnostic records, tooth numbers, and prescriptions.</p>
                </div>
                @if(!auth()->user()->isReceptionist())
                    <a href="{{ route('treatments.create', ['patient_id' => $patient->id]) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-cyan-600 text-white text-xs font-bold hover:bg-cyan-700 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Record Procedure
                    </a>
                @endif
            </div>

            <div class="space-y-4">
                @forelse($treatmentRecords as $record)
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
                        <!-- Record Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-cyan-100 text-cyan-800 flex items-center justify-center font-bold text-sm">
                                    🦷
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-sm">
                                        {{ $record->service->name ?? 'Clinical Procedure' }}
                                    </h4>
                                    <span class="text-xs text-slate-500">
                                        Rendered by <span class="font-semibold text-slate-700">{{ $record->doctor->name }}</span> &bull; {{ $record->created_at->format('M d, Y - h:i A') }}
                                    </span>
                                </div>
                            </div>

                            @if($record->tooth_numbers)
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 font-mono text-xs font-bold self-start sm:self-auto">
                                    <span>Tooth #:</span>
                                    <span class="text-cyan-700">{{ $record->tooth_numbers }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Diagnosis & Treatment Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Diagnosis</span>
                                <p class="text-slate-800 font-medium">{{ $record->diagnosis ?: 'No formal diagnosis logged' }}</p>
                            </div>

                            <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Treatment Rendered</span>
                                <p class="text-slate-800 font-medium">{{ $record->treatment_rendered ?: 'General examination and hygiene care' }}</p>
                            </div>
                        </div>

                        <!-- Prescription (if available) -->
                        @if($record->prescription)
                            <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-100 text-xs">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 block mb-1">Rx / Prescription</span>
                                <p class="font-mono text-emerald-950 font-semibold whitespace-pre-line">{{ $record->prescription }}</p>
                            </div>
                        @endif

                        <!-- Clinical Notes with Receptionist Confidentiality Mask -->
                        <div class="pt-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Dentist Clinical Notes</span>

                            @if($user->isReceptionist())
                                <div class="rounded-2xl bg-slate-100 border border-slate-200 p-3.5 flex items-center gap-2.5 text-xs text-slate-500 italic">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <span>Confidential Clinical Record &bull; Hidden for Receptionist role under patient privacy policy.</span>
                                </div>
                            @else
                                <div class="rounded-2xl bg-amber-50/50 border border-amber-200/80 p-3.5 text-xs text-slate-800 leading-relaxed font-sans">
                                    {{ $record->clinical_notes ?: 'No additional confidential notes recorded.' }}
                                </div>
                            @endif
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex justify-end">
                            <a href="{{ route('treatments.show', $record->id) }}"
                                class="text-xs font-bold text-cyan-600 hover:text-cyan-800 flex items-center gap-1">
                                View Full Clinical Dossier &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-3xl p-12 text-center text-xs text-slate-400 border border-slate-200/90 shadow-sm">
                        No clinical treatment records found for this patient yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tab 4: Invoices & Ledger -->
        <div x-show="activeTab === 'billing'" class="space-y-6">
            <!-- Billing Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Total Invoiced</span>
                    <span class="text-2xl font-black font-mono text-slate-900 mt-1 block">
                        Rs. {{ number_format($patient->invoices->sum('total_amount'), 0) }}
                    </span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 block">Total Paid</span>
                    <span class="text-2xl font-black font-mono text-emerald-700 mt-1 block">
                        Rs. {{ number_format($totalPaid, 0) }}
                    </span>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-500 block">Outstanding Balance</span>
                    <span class="text-2xl font-black font-mono {{ $outstandingBalance > 0 ? 'text-rose-600' : 'text-slate-900' }} mt-1 block">
                        Rs. {{ number_format($outstandingBalance, 0) }}
                    </span>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-700">Invoices & Billing History</h4>
                </div>

                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">Invoice #</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Total Amount</th>
                            <th class="px-6 py-4">Paid</th>
                            <th class="px-6 py-4">Balance</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($patient->invoices as $inv)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                    {{ $inv->invoice_number }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $inv->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 font-mono font-semibold text-slate-900">
                                    Rs. {{ number_format($inv->total_amount, 0) }}
                                </td>
                                <td class="px-6 py-4 font-mono text-emerald-700 font-semibold">
                                    Rs. {{ number_format($inv->paid_amount, 0) }}
                                </td>
                                <td class="px-6 py-4 font-mono font-bold {{ $inv->balance_amount > 0 ? 'text-rose-600' : 'text-slate-600' }}">
                                    Rs. {{ number_format($inv->balance_amount, 0) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($inv->status === 'paid')
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Paid
                                        </span>
                                    @elseif($inv->status === 'partially_paid')
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                            Partial
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-rose-50 text-rose-700 border border-rose-200">
                                            Unpaid
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-xs text-slate-400">
                                    No invoices issued for this patient yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Payments Recorded Table -->
            @if($patient->payments->isNotEmpty())
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-100">
                        <h4 class="font-bold text-xs uppercase tracking-wider text-slate-700">Receipts & Payment Transactions</h4>
                    </div>

                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Method</th>
                                <th class="px-6 py-4">Reference</th>
                                <th class="px-6 py-4">Received By</th>
                                <th class="px-6 py-4 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($patient->payments as $pmt)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ \Carbon\Carbon::parse($pmt->payment_date)->format('M d, Y - h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 uppercase font-bold text-slate-700 text-[11px]">
                                        {{ str_replace('_', ' ', $pmt->payment_method) }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-slate-500">
                                        {{ $pmt->transaction_reference ?: 'Direct Counter Cash' }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-700 font-medium">
                                        {{ $pmt->receivedBy->name ?? 'Front Desk Staff' }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-bold text-emerald-700">
                                        Rs. {{ number_format($pmt->amount, 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Tab 5: Follow-Ups & Recalls -->
        <div x-show="activeTab === 'followups'" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Follow-Up & Clinical Recall Tasks</h3>
                    <p class="text-xs text-slate-500">Scheduled recalls, post-treatment reviews, and periodic check-up reminders.</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">Due Date</th>
                            <th class="px-6 py-4">Reason / Procedure</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Last Contacted</th>
                            <th class="px-6 py-4">Staff Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($patient->followUps as $fu)
                            @php
                                $isOverdue = $fu->status === 'pending' && $fu->due_date->isPast();
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold {{ $isOverdue ? 'text-rose-600' : 'text-slate-900' }}">
                                        {{ $fu->due_date->format('M d, Y') }}
                                    </span>
                                    @if($isOverdue)
                                        <span class="block text-[10px] font-bold text-rose-500 uppercase tracking-wider">Overdue</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-800">
                                    {{ $fu->reason }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($fu->status === 'completed')
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Completed
                                        </span>
                                    @elseif($fu->status === 'contacted')
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                            Contacted
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    {{ $fu->last_contacted_at ? $fu->last_contacted_at->format('M d, Y - h:i A') : 'Not contacted yet' }}
                                </td>
                                <td class="px-6 py-4 text-slate-600 max-w-[240px]">
                                    {{ $fu->staff_notes ?: '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-xs text-slate-400">
                                    No active recall or follow-up tasks recorded for this patient.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
