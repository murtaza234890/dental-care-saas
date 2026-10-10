<x-layouts.app title="Record Clinical Procedure - DentalCare">
    <div class="max-w-5xl mx-auto" x-data="{
        selectedTeeth: {{ json_encode($selectedTeeth) }},
        toothInput: '{{ old('tooth_numbers', '') }}',
        toggleTooth(tooth) {
            let index = this.selectedTeeth.indexOf(tooth.toString());
            if (index > -1) {
                this.selectedTeeth.splice(index, 1);
            } else {
                this.selectedTeeth.push(tooth.toString());
            }
            this.selectedTeeth.sort();
            this.toothInput = this.selectedTeeth.join(', ');
        },
        hasTooth(tooth) {
            return this.selectedTeeth.includes(tooth.toString());
        },
        clearTeeth() {
            this.selectedTeeth = [];
            this.toothInput = '';
        },
        selectUpper() {
            let upper = ['18','17','16','15','14','13','12','11','21','22','23','24','25','26','27','28'];
            this.selectedTeeth = Array.from(new Set([...this.selectedTeeth, ...upper])).sort();
            this.toothInput = this.selectedTeeth.join(', ');
        },
        selectLower() {
            let lower = ['48','47','46','45','44','43','42','41','31','32','33','34','35','36','37','38'];
            this.selectedTeeth = Array.from(new Set([...this.selectedTeeth, ...lower])).sort();
            this.toothInput = this.selectedTeeth.join(', ');
        },
        scheduleFollowup: {{ old('schedule_followup') ? 'true' : 'false' }},
        createInvoice: {{ old('create_invoice', '1') ? 'true' : 'false' }},
        setDiagnosis(text) {
            let current = document.getElementById('diagnosis').value;
            document.getElementById('diagnosis').value = current ? current + '; ' + text : text;
        },
        setTreatment(text) {
            let current = document.getElementById('treatment_rendered').value;
            document.getElementById('treatment_rendered').value = current ? current + '; ' + text : text;
        },
        setPrescription(text) {
            let current = document.getElementById('prescription').value;
            document.getElementById('prescription').value = current ? current + '\n' + text : text;
        }
    }">
        <!-- Breadcrumb & Header -->
        <div class="mb-6">
            <a href="{{ route('treatments.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-600 hover:text-cyan-700 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Clinical Treatments Directory
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        Record Clinical Dental Procedure
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Interactive dental odontogram charting, diagnostic notes, prescriptions, and recall scheduling.
                    </p>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-cyan-50 border border-cyan-200 text-cyan-800 text-xs font-semibold self-start sm:self-auto">
                    <span>Clinical Odontogram Active</span>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800">
                <div class="font-bold mb-1 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Please correct the following errors:
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($selectedAppointment)
            <div class="mb-6 rounded-2xl bg-teal-50 border border-teal-200 p-4 text-xs text-teal-900 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold">
                        ✓
                    </span>
                    <div>
                        <span class="font-bold">Linked to Scheduled Consultation:</span>
                        <span>{{ $selectedAppointment->patient->name }} &bull; {{ \Carbon\Carbon::parse($selectedAppointment->appointment_date)->format('M d, Y') }} ({{ \Carbon\Carbon::parse($selectedAppointment->starts_at)->format('h:i A') }})</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-md bg-teal-100 text-teal-800 font-bold uppercase text-[10px]">
                    Status: {{ str_replace('_', ' ', $selectedAppointment->status) }}
                </span>
            </div>
        @endif

        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm">
            <form action="{{ route('treatments.store') }}" method="POST" class="space-y-8">
                @csrf

                @if($appointmentId)
                    <input type="hidden" name="appointment_id" value="{{ $appointmentId }}">
                @endif

                <!-- Section 1: Patient, Doctor & Service Setup -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-cyan-700 flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Clinical Encounter Details
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Patient Selector -->
                        <div>
                            <label for="patient_id" class="block text-xs font-semibold text-slate-700 mb-1">
                                Patient <span class="text-rose-500">*</span>
                            </label>
                            <select id="patient_id" name="patient_id" required
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                                <option value="">-- Choose Patient --</option>
                                @foreach($patients as $pt)
                                    <option value="{{ $pt->id }}" {{ (old('patient_id', $patientId) == $pt->id) ? 'selected' : '' }}>
                                        {{ $pt->name }} ({{ $pt->phone }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Doctor Selector -->
                        <div>
                            <label for="doctor_id" class="block text-xs font-semibold text-slate-700 mb-1">
                                Attending Dentist <span class="text-rose-500">*</span>
                            </label>
                            <select id="doctor_id" name="doctor_id" required
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                                @foreach($doctors as $doc)
                                    <option value="{{ $doc->id }}" {{ (old('doctor_id', auth()->id()) == $doc->id) ? 'selected' : '' }}>
                                        {{ $doc->name }} ({{ $doc->doctorProfile->specialization ?? 'Dentist' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Service Selector -->
                        <div>
                            <label for="service_id" class="block text-xs font-semibold text-slate-700 mb-1">
                                Dental Procedure / Service
                            </label>
                            <select id="service_id" name="service_id"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                                <option value="">-- General Procedure --</option>
                                @foreach($services as $srv)
                                    <option value="{{ $srv->id }}" {{ (old('service_id', $selectedAppointment->service_id ?? '') == $srv->id) ? 'selected' : '' }}>
                                        {{ $srv->name }} (Rs. {{ number_format($srv->standard_fee, 0) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Interactive Adult Dental Odontogram (FDI Charting) -->
                <div class="bg-slate-50/70 rounded-3xl p-6 border border-slate-200/80">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Adult Dental Odontogram (FDI Two-Digit Charting)
                            </h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Click on the teeth involved in this procedure. Selected teeth will be highlighted and recorded.
                            </p>
                        </div>

                        <!-- Quick Arch Controls -->
                        <div class="flex items-center gap-2 self-start sm:self-auto text-xs">
                            <button type="button" @click="selectUpper()" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold transition text-[11px]">
                                + Upper Arch
                            </button>
                            <button type="button" @click="selectLower()" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold transition text-[11px]">
                                + Lower Arch
                            </button>
                            <button type="button" @click="clearTeeth()" class="px-2.5 py-1 rounded-lg bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 font-semibold transition text-[11px]">
                                Clear
                            </button>
                        </div>
                    </div>

                    <!-- Maxillary (Upper) Arch -->
                    <div class="mb-5 bg-white p-4 rounded-2xl border border-slate-200/60 shadow-xs">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2.5 text-center">
                            Maxillary Arch (Upper Teeth)
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Upper Right (Quadrant 1: 18 - 11) -->
                            <div>
                                <span class="text-[9px] font-semibold text-slate-400 block text-right mb-1">Upper Right (Q1)</span>
                                <div class="flex justify-end gap-1.5 flex-wrap">
                                    @foreach(['18', '17', '16', '15', '14', '13', '12', '11'] as $t)
                                        <button type="button" @click="toggleTooth('{{ $t }}')"
                                            :class="hasTooth('{{ $t }}') ? 'bg-cyan-600 text-white border-cyan-700 shadow-md shadow-cyan-600/30' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                            class="w-8 h-10 rounded-xl border font-mono font-bold text-xs flex flex-col items-center justify-center transition active:scale-95">
                                            <span class="text-[9px] opacity-75">🦷</span>
                                            <span>{{ $t }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Upper Left (Quadrant 2: 21 - 28) -->
                            <div>
                                <span class="text-[9px] font-semibold text-slate-400 block text-left mb-1">Upper Left (Q2)</span>
                                <div class="flex justify-start gap-1.5 flex-wrap">
                                    @foreach(['21', '22', '23', '24', '25', '26', '27', '28'] as $t)
                                        <button type="button" @click="toggleTooth('{{ $t }}')"
                                            :class="hasTooth('{{ $t }}') ? 'bg-cyan-600 text-white border-cyan-700 shadow-md shadow-cyan-600/30' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                            class="w-8 h-10 rounded-xl border font-mono font-bold text-xs flex flex-col items-center justify-center transition active:scale-95">
                                            <span class="text-[9px] opacity-75">🦷</span>
                                            <span>{{ $t }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mandibular (Lower) Arch -->
                    <div class="mb-4 bg-white p-4 rounded-2xl border border-slate-200/60 shadow-xs">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Lower Right (Quadrant 4: 48 - 41) -->
                            <div>
                                <div class="flex justify-end gap-1.5 flex-wrap">
                                    @foreach(['48', '47', '46', '45', '44', '43', '42', '41'] as $t)
                                        <button type="button" @click="toggleTooth('{{ $t }}')"
                                            :class="hasTooth('{{ $t }}') ? 'bg-cyan-600 text-white border-cyan-700 shadow-md shadow-cyan-600/30' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                            class="w-8 h-10 rounded-xl border font-mono font-bold text-xs flex flex-col items-center justify-center transition active:scale-95">
                                            <span>{{ $t }}</span>
                                            <span class="text-[9px] opacity-75">🦷</span>
                                        </button>
                                    @endforeach
                                </div>
                                <span class="text-[9px] font-semibold text-slate-400 block text-right mt-1">Lower Right (Q4)</span>
                            </div>

                            <!-- Lower Left (Quadrant 3: 31 - 38) -->
                            <div>
                                <div class="flex justify-start gap-1.5 flex-wrap">
                                    @foreach(['31', '32', '33', '34', '35', '36', '37', '38'] as $t)
                                        <button type="button" @click="toggleTooth('{{ $t }}')"
                                            :class="hasTooth('{{ $t }}') ? 'bg-cyan-600 text-white border-cyan-700 shadow-md shadow-cyan-600/30' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                            class="w-8 h-10 rounded-xl border font-mono font-bold text-xs flex flex-col items-center justify-center transition active:scale-95">
                                            <span>{{ $t }}</span>
                                            <span class="text-[9px] opacity-75">🦷</span>
                                        </button>
                                    @endforeach
                                </div>
                                <span class="text-[9px] font-semibold text-slate-400 block text-left mt-1">Lower Left (Q3)</span>
                            </div>
                        </div>

                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-2.5 text-center">
                            Mandibular Arch (Lower Teeth)
                        </div>
                    </div>

                    <!-- Selected Teeth Summary & Custom Input -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-bold text-slate-700">Selected Teeth:</span>
                            <template x-if="selectedTeeth.length === 0">
                                <span class="text-xs text-slate-400 italic">None selected (or general mouth treatment)</span>
                            </template>
                            <template x-for="t in selectedTeeth" :key="t">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-cyan-100 text-cyan-800 text-xs font-mono font-bold">
                                    <span x-text="'#' + t"></span>
                                    <button type="button" @click="toggleTooth(t)" class="hover:text-rose-600">&times;</button>
                                </span>
                            </template>
                        </div>

                        <div class="w-full sm:w-60">
                            <input type="text" id="tooth_numbers" name="tooth_numbers" x-model="toothInput"
                                placeholder="Manual e.g. 11, 21 or deciduous 54"
                                class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-xs text-slate-900 font-mono focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Clinical Diagnosis & Treatment Rendered -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-cyan-700 flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Diagnostic Findings & Clinical Procedure
                    </h3>

                    <!-- Diagnosis -->
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-1">
                            <label for="diagnosis" class="block text-xs font-semibold text-slate-700">
                                Clinical Diagnosis <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                <span>Quick insert:</span>
                                @foreach(['Irreversible Pulpitis', 'Class II Dental Caries', 'Periapical Abscess', 'Impacted 3rd Molar', 'Chronic Gingivitis'] as $diag)
                                    <button type="button" @click="setDiagnosis('{{ $diag }}')"
                                        class="hover:text-cyan-700 underline">{{ $diag }}</button>
                                @endforeach
                            </div>
                        </div>
                        <textarea id="diagnosis" name="diagnosis" rows="2" required
                            placeholder="e.g. Deep occlusal caries with symptomatic irreversible pulpitis on tooth #16..."
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">{{ old('diagnosis') }}</textarea>
                    </div>

                    <!-- Treatment Rendered -->
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-1">
                            <label for="treatment_rendered" class="block text-xs font-semibold text-slate-700">
                                Treatment Rendered / Procedure Details <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                <span>Quick insert:</span>
                                @foreach(['Canals instrumented & Ca(OH)2 dressed', 'Class II Composite filling shade A2', 'Scaling & subgingival curettage', 'Surgical extraction under local anesthesia'] as $proc)
                                    <button type="button" @click="setTreatment('{{ $proc }}')"
                                        class="hover:text-cyan-700 underline">{{ explode(' ', $proc)[0] }}</button>
                                @endforeach
                            </div>
                        </div>
                        <textarea id="treatment_rendered" name="treatment_rendered" rows="3" required
                            placeholder="Detail steps performed, irrigation solutions, restorative materials, shades used..."
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">{{ old('treatment_rendered') }}</textarea>
                    </div>

                    <!-- Prescription -->
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-1">
                            <label for="prescription" class="block text-xs font-semibold text-slate-700">
                                Rx / Medical Prescription <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                <span>Quick Rx:</span>
                                <button type="button" @click="setPrescription('Amoxicillin 500mg 1 cap TID x 5 days\nIbuprofen 400mg 1 tab SOS PRN')" class="hover:text-emerald-700 underline">+ Amoxicillin & Ibuprofen</button>
                                <button type="button" @click="setPrescription('Augmentin 625mg 1 tab BID x 5 days\nParacetamol 500mg 1 tab SOS')" class="hover:text-emerald-700 underline">+ Augmentin</button>
                                <button type="button" @click="setPrescription('Chlorhexidine 0.2% mouthwash rinse 15ml BID x 7 days')" class="hover:text-emerald-700 underline">+ Chlorhexidine</button>
                            </div>
                        </div>
                        <textarea id="prescription" name="prescription" rows="3"
                            placeholder="e.g. Tab. Augmentin 625mg 1 tab twice daily for 5 days after food..."
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-mono text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/20">{{ old('prescription') }}</textarea>
                    </div>

                    <!-- Confidential Doctor Notes (Masked for receptionist) -->
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <label for="clinical_notes" class="block text-xs font-semibold text-amber-800">
                                Confidential Dentist Clinical Notes
                            </label>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                Doctor & Admin Access Only (Masked for Receptionist)
                            </span>
                        </div>
                        <textarea id="clinical_notes" name="clinical_notes" rows="2"
                            placeholder="Private doctor observations, patient compliance, prognosis, or internal notes..."
                            class="w-full rounded-xl border border-amber-300 bg-amber-50/30 px-3.5 py-2 text-xs text-slate-900 focus:border-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-600/20">{{ old('clinical_notes') }}</textarea>
                    </div>
                </div>

                <!-- Section 4: Automated Clinical Workflows (Follow-up & Invoice) -->
                <div class="pt-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
                        <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Automated Post-Treatment Workflows
                    </h3>

                    <div class="space-y-4">
                        <!-- Workflow 1: Complete Appointment -->
                        @if($appointmentId)
                            <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-teal-200 bg-teal-50/60 cursor-pointer">
                                <input type="checkbox" name="complete_appointment" value="1" checked
                                    class="rounded text-teal-600 focus:ring-teal-500">
                                <div class="text-xs">
                                    <span class="font-bold text-teal-950 block">Mark Linked Appointment as Completed</span>
                                    <span class="text-teal-700">Automatically transitions consultation status from checked-in/in-progress to completed.</span>
                                </div>
                            </label>
                        @endif

                        <!-- Workflow 2: Schedule Follow-up / Recall -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="schedule_followup" value="1" x-model="scheduleFollowup"
                                    class="rounded text-cyan-600 focus:ring-cyan-500">
                                <div class="text-xs">
                                    <span class="font-bold text-slate-900 block">Schedule Clinical Recall / Follow-Up Task</span>
                                    <span class="text-slate-500">Adds an actionable task for the front desk to contact the patient for recall.</span>
                                </div>
                            </label>

                            <div x-show="scheduleFollowup" class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                <div>
                                    <label for="followup_due_date" class="block text-[11px] font-semibold text-slate-700 mb-1">
                                        Recall Due Date *
                                    </label>
                                    <input type="date" id="followup_due_date" name="followup_due_date"
                                        value="{{ old('followup_due_date', \Carbon\Carbon::today()->addDays(7)->format('Y-m-d')) }}"
                                        min="{{ date('Y-m-d') }}"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                                </div>

                                <div>
                                    <label for="followup_reason" class="block text-[11px] font-semibold text-slate-700 mb-1">
                                        Recall Reason / Task *
                                    </label>
                                    <input type="text" id="followup_reason" name="followup_reason"
                                        value="{{ old('followup_reason', 'Post-procedure review & evaluation') }}"
                                        placeholder="e.g. Suture removal, Crown seating, RCT step 2"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                                </div>
                            </div>
                        </div>

                        <!-- Workflow 3: Auto-generate Invoice -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="create_invoice" value="1" x-model="createInvoice"
                                    class="rounded text-cyan-600 focus:ring-cyan-500">
                                <div class="text-xs">
                                    <span class="font-bold text-slate-900 block">Generate Draft Invoice for this Procedure</span>
                                    <span class="text-slate-500">Creates an invoice line item based on standard procedure fee and updates the patient's balance.</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('treatments.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Cancel
                    </a>

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-6 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Clinical Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
