<x-layouts.app title="Schedule Patient Recall - DentalCare">
    <div class="max-w-3xl mx-auto">
        <!-- Breadcrumb & Header -->
        <div class="mb-6">
            <a href="{{ route('follow-ups.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-600 hover:text-cyan-700 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Recall Queue
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        Schedule Clinical Recall Task
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Create an automatic reminder task for patient follow-up calls, checkups, or prosthesis delivery.
                    </p>
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

        @if($selectedTreatment)
            <div class="mb-6 rounded-2xl bg-cyan-50 border border-cyan-200 p-4 text-xs text-cyan-900 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-cyan-600 text-white flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3C4.5 3 3 5.5 3 8c0 4 2.5 7.5 3.5 11 .5 1.5 2 2 3.5 2s2-1 2.5-3.5c.5-2.5 1-2.5 1.5 0 .5 2.5 1 3.5 2.5 3.5s3-.5 3.5-2C21 15.5 21 12 21 8c0-2.5-1.5-5-4-5-2 0-3.5 1.5-5 1.5S9 3 7 3z"/></svg>
                    </span>
                    <div>
                        <span class="font-bold">Linking Recall to Treatment Record #{{ $selectedTreatment->id }}:</span>
                        <span>{{ $selectedTreatment->patient->name }} &bull; {{ $selectedTreatment->diagnosis }}</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-md bg-cyan-100 text-cyan-800 font-bold uppercase text-[10px]">
                    Tooth: {{ $selectedTreatment->tooth_numbers ?: 'General' }}
                </span>
            </div>
        @endif

        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm">
            <form action="{{ route('follow-ups.store') }}" method="POST" class="space-y-6">
                @csrf

                @if($treatmentRecordId)
                    <input type="hidden" name="treatment_record_id" value="{{ $treatmentRecordId }}">
                @endif

                <!-- Section 1: Patient Selection -->
                <div>
                    <label for="patient_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Patient File <span class="text-rose-500">*</span>
                    </label>
                    <select id="patient_id" name="patient_id" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        <option value="">-- Choose Patient --</option>
                        @foreach($patients as $pt)
                            <option value="{{ $pt->id }}" {{ (old('patient_id', $patientId) == $pt->id) ? 'selected' : '' }}>
                                {{ $pt->name }} &bull; {{ $pt->phone }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Section 2: Recall Reason with Quick Preset Chips -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="reason" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Recall Reason / Procedure <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400">Click a preset or type below</span>
                    </div>

                    <!-- Preset Chips -->
                    <div class="flex flex-wrap gap-1.5 mb-2.5">
                        <button type="button" onclick="setReason('Post-Extraction Healing Check')"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-700 border border-slate-200 transition">
                            + Post-Op Healing
                        </button>
                        <button type="button" onclick="setReason('Suture / Stitch Removal')"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-700 border border-slate-200 transition">
                            + Suture Removal
                        </button>
                        <button type="button" onclick="setReason('Permanent Crown / Bridge Cementation')"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-700 border border-slate-200 transition">
                            + Crown Fitting
                        </button>
                        <button type="button" onclick="setReason('Root Canal Therapy (RCT) 2nd Sitting')"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-700 border border-slate-200 transition">
                            + RCT Next Sitting
                        </button>
                        <button type="button" onclick="setReason('6-Month Routine Cleaning & Recall')"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-700 border border-slate-200 transition">
                            + 6-Month Routine Cleaning
                        </button>
                        <button type="button" onclick="setReason('Orthodontic Review & Retainer Check')"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-700 border border-slate-200 transition">
                            + Orthodontic Review
                        </button>
                    </div>

                    <input type="text" id="reason" name="reason" required
                        value="{{ old('reason', '') }}"
                        placeholder="e.g. Suture removal after surgical extraction of tooth #38"
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                </div>

                <!-- Section 3: Due Date with Quick Date Shortcuts -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="due_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Due Date <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400">Quick set shortcuts:</span>
                    </div>

                    <!-- Date Shortcuts -->
                    <div class="flex flex-wrap gap-1.5 mb-2.5">
                        <button type="button" onclick="addDays(3)"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-cyan-50 text-cyan-700 hover:bg-cyan-100 border border-cyan-200 transition">
                            In 3 Days
                        </button>
                        <button type="button" onclick="addDays(7)"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-cyan-50 text-cyan-700 hover:bg-cyan-100 border border-cyan-200 transition">
                            In 1 Week
                        </button>
                        <button type="button" onclick="addDays(14)"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-cyan-50 text-cyan-700 hover:bg-cyan-100 border border-cyan-200 transition">
                            In 2 Weeks
                        </button>
                        <button type="button" onclick="addDays(30)"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-cyan-50 text-cyan-700 hover:bg-cyan-100 border border-cyan-200 transition">
                            In 1 Month
                        </button>
                        <button type="button" onclick="addDays(180)"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-cyan-50 text-cyan-700 hover:bg-cyan-100 border border-cyan-200 transition">
                            In 6 Months
                        </button>
                    </div>

                    <input type="date" id="due_date" name="due_date" required min="{{ date('Y-m-d') }}"
                        value="{{ old('due_date', \Carbon\Carbon::tomorrow()->toDateString()) }}"
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                </div>

                <!-- Section 4: Staff Notes -->
                <div>
                    <label for="staff_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Staff Instructions & Clinical Reminders
                    </label>
                    <textarea id="staff_notes" name="staff_notes" rows="3"
                        placeholder="e.g. Check for pain or swelling; advise warm saline rinses before appointment..."
                        class="w-full rounded-xl border border-slate-300 p-3 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">{{ old('staff_notes') }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('follow-ups.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Cancel
                    </a>

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-6 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Schedule Recall
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function setReason(reason) {
            document.getElementById('reason').value = reason;
        }

        function addDays(days) {
            let targetDate = new Date();
            targetDate.setDate(targetDate.getDate() + days);
            let yyyy = targetDate.getFullYear();
            let mm = String(targetDate.getMonth() + 1).padStart(2, '0');
            let dd = String(targetDate.getDate()).padStart(2, '0');
            document.getElementById('due_date').value = yyyy + '-' + mm + '-' + dd;
        }
    </script>
</x-layouts.app>
