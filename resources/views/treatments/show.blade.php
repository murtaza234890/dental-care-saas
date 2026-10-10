<x-layouts.app title="Clinical Record - #{{ $treatment->id }}">
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Top Bar & Breadcrumb -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('treatments.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-600 hover:text-cyan-700 transition mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Treatments Directory
                </a>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        Clinical Procedure Dossier
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        Record #TR-{{ str_pad($treatment->id, 5, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print Record
                </button>

                @if(!auth()->user()->isReceptionist())
                    <a href="{{ route('treatments.edit', $treatment->id) }}"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-cyan-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-cyan-700 shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Record
                    </a>
                @endif
            </div>
        </div>

        <!-- High-Priority Medical Alert Banner (if patient has medical allergies) -->
        @if ($treatment->patient->medical_history)
            <div class="rounded-3xl bg-rose-50 border-2 border-rose-300 p-4 shadow-sm flex items-start gap-3.5">
                <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center font-bold text-base shrink-0 shadow-md shadow-rose-600/30">
                    ⚠️
                </div>
                <div>
                    <span class="text-xs font-black tracking-wider uppercase text-rose-800 block">
                        Patient Medical Alert & Drug Allergies
                    </span>
                    <p class="text-xs font-bold text-rose-950 mt-0.5">
                        {{ $treatment->patient->medical_history }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Patient & Procedure Overview Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Patient Info -->
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-cyan-100 text-cyan-800 flex items-center justify-center font-extrabold text-xl shrink-0">
                        {{ substr($treatment->patient->name, 0, 1) }}
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Patient Profile</span>
                        <a href="{{ route('patients.show', $treatment->patient_id) }}" class="text-base font-extrabold text-slate-900 hover:text-cyan-700 block">
                            {{ $treatment->patient->name }}
                        </a>
                        <div class="text-xs text-slate-500 font-mono mt-0.5">
                            {{ $treatment->patient->phone }}
                        </div>
                        @if($treatment->patient->blood_group)
                            <span class="inline-block mt-1 px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold">
                                {{ $treatment->patient->blood_group }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Attending Dentist -->
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Attending Dentist</span>
                    <span class="text-base font-extrabold text-slate-900 block mt-0.5">
                        {{ $treatment->doctor->name }}
                    </span>
                    <span class="text-xs text-slate-500 block">
                        {{ $treatment->doctor->doctorProfile->specialization ?? 'General Dentist' }}
                    </span>
                    <span class="text-[11px] text-slate-400 font-mono block mt-1">
                        License: {{ $treatment->doctor->doctorProfile->license_number ?? 'PMDC Verified' }}
                    </span>
                </div>

                <!-- Encounter Meta -->
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Procedure Date & Service</span>
                    <span class="text-base font-extrabold text-cyan-800 block mt-0.5">
                        {{ $treatment->service->name ?? 'Clinical Procedure' }}
                    </span>
                    <span class="text-xs text-slate-500 block">
                        {{ $treatment->created_at->format('l, M d, Y - h:i A') }}
                    </span>
                    @if($treatment->appointment)
                        <span class="inline-flex items-center gap-1 mt-1 text-[11px] font-semibold text-teal-700">
                            ✓ Linked to Appointment #{{ $treatment->appointment_id }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Odontogram Charting Visualizer -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <span>Anatomical Dental Charting</span>
                        @if($treatment->tooth_numbers)
                            <span class="px-2.5 py-0.5 rounded-lg bg-cyan-100 text-cyan-800 font-mono font-bold text-xs">
                                Tooth #: {{ $treatment->tooth_numbers }}
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-600 font-mono text-xs">
                                Generalized / Full Dentition
                            </span>
                        @endif
                    </h3>
                </div>
                <span class="text-[11px] text-slate-400">FDI Two-Digit Standard</span>
            </div>

            @php
                $treatedTeeth = array_map('trim', explode(',', $treatment->tooth_numbers ?? ''));
            @endphp

            <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                <!-- Upper Arch -->
                <div class="mb-3">
                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block text-center mb-1">Maxillary Arch (Upper)</span>
                    <div class="flex justify-center gap-1.5 flex-wrap">
                        @foreach(['18','17','16','15','14','13','12','11','21','22','23','24','25','26','27','28'] as $t)
                            @php
                                $isTreated = in_array($t, $treatedTeeth);
                            @endphp
                            <div class="w-8 h-10 rounded-xl border font-mono font-bold text-xs flex flex-col items-center justify-center {{ $isTreated ? 'bg-cyan-600 text-white border-cyan-700 shadow-md shadow-cyan-600/30 ring-2 ring-cyan-400' : 'bg-white text-slate-400 border-slate-200' }}">
                                <span class="text-[9px]">🦷</span>
                                <span>{{ $t }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Lower Arch -->
                <div>
                    <div class="flex justify-center gap-1.5 flex-wrap">
                        @foreach(['48','47','46','45','44','43','42','41','31','32','33','34','35','36','37','38'] as $t)
                            @php
                                $isTreated = in_array($t, $treatedTeeth);
                            @endphp
                            <div class="w-8 h-10 rounded-xl border font-mono font-bold text-xs flex flex-col items-center justify-center {{ $isTreated ? 'bg-cyan-600 text-white border-cyan-700 shadow-md shadow-cyan-600/30 ring-2 ring-cyan-400' : 'bg-white text-slate-400 border-slate-200' }}">
                                <span>{{ $t }}</span>
                                <span class="text-[9px]">🦷</span>
                            </div>
                        @endforeach
                    </div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block text-center mt-1">Mandibular Arch (Lower)</span>
                </div>
            </div>
        </div>

        <!-- Clinical Findings: Diagnosis & Treatment Rendered -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Diagnosis Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Clinical Diagnosis</span>
                <p class="text-sm font-semibold text-slate-900 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    {{ $treatment->diagnosis }}
                </p>
            </div>

            <!-- Treatment Rendered Card -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Treatment Rendered</span>
                <p class="text-sm font-semibold text-slate-900 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    {{ $treatment->treatment_rendered }}
                </p>
            </div>
        </div>

        <!-- Prescription Rx Slip -->
        @if($treatment->prescription)
            <div class="bg-white rounded-3xl p-6 border border-emerald-200 shadow-sm bg-gradient-to-br from-white to-emerald-50/20">
                <div class="flex items-center justify-between pb-3 border-b border-emerald-100 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-bold font-serif text-emerald-800">℞</span>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-900">
                            Prescription & Medication Orders
                        </h3>
                    </div>
                    <span class="text-[11px] text-emerald-700 font-semibold">Doctor Signed</span>
                </div>
                <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-200 text-xs font-mono font-bold text-emerald-950 whitespace-pre-line leading-relaxed">
                    {{ $treatment->prescription }}
                </div>
                <div class="mt-2 text-[10px] text-slate-400 text-right">
                    Prescribed by {{ $treatment->doctor->name }} &bull; {{ $treatment->created_at->format('M d, Y') }}
                </div>
            </div>
        @endif

        <!-- Doctor's Confidential Notes (with Receptionist Confidentiality Mask) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Dentist Clinical Notes
                </h3>
                <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded">Confidential</span>
            </div>

            @if($user->isReceptionist())
                <div class="rounded-2xl bg-slate-100 border border-slate-200 p-4 text-xs text-slate-500 italic flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Confidential Clinical Record &bull; Hidden for Receptionist role under clinic medical ethics policy.</span>
                </div>
            @else
                <div class="rounded-2xl bg-amber-50/40 border border-amber-200/80 p-4 text-xs text-slate-800 leading-relaxed font-sans">
                    {{ $treatment->clinical_notes ?: 'No private clinical notes recorded for this procedure.' }}
                </div>
            @endif
        </div>

        <!-- Follow-ups & Billing Linked Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Linked Follow-ups -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-3">Scheduled Follow-Up / Recall</span>
                @if($treatment->followUps->isNotEmpty())
                    @foreach($treatment->followUps as $fu)
                        <div class="p-4 rounded-2xl bg-cyan-50/60 border border-cyan-100 text-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-cyan-900">{{ $fu->reason }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-cyan-200 text-cyan-800">
                                    {{ $fu->status }}
                                </span>
                            </div>
                            <div class="text-cyan-700">Due: {{ $fu->due_date->format('l, M d, Y') }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-400 text-center">
                        No recall follow-ups scheduled for this procedure.
                    </div>
                @endif
            </div>

            <!-- Linked Invoice -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-3">Billing & Financial Invoice</span>
                @if($linkedInvoice)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-bold text-slate-900">{{ $linkedInvoice->invoice_number }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $linkedInvoice->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $linkedInvoice->status }}
                            </span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Amount Billed:</span>
                            <span class="font-mono font-bold text-slate-900">Rs. {{ number_format($linkedInvoice->total_amount, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Balance Due:</span>
                            <span class="font-mono font-bold text-rose-600">Rs. {{ number_format($linkedInvoice->balance_amount, 0) }}</span>
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-400 text-center">
                        No direct invoice linked. Procedure can be billed from the patient's billing tab.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
