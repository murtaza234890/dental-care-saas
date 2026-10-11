<x-layouts.app title="Edit Treatment - #{{ $treatment->id }}">
    <div class="max-w-5xl mx-auto" x-data="treatmentEditForm()">
        <!-- Breadcrumb & Header -->
        <div class="mb-6">
            <a href="{{ route('treatments.show', $treatment->id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-600 hover:text-cyan-700 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Procedure Dossier
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        Edit Clinical Treatment Record
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Editing procedure details for <span class="font-bold text-slate-800">{{ $treatment->patient->name }}</span> (Record #{{ $treatment->id }}).
                    </p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm">
            <form action="{{ route('treatments.update', $treatment->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Service Selection -->
                <div>
                    <label for="service_id" class="block text-xs font-semibold text-slate-700 mb-1">
                        Dental Procedure / Service
                    </label>
                    <select id="service_id" name="service_id"
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        <option value="">-- General Procedure --</option>
                        @foreach($services as $srv)
                            <option value="{{ $srv->id }}" {{ (old('service_id', $treatment->service_id) == $srv->id) ? 'selected' : '' }}>
                                {{ $srv->name }} (Rs. {{ number_format($srv->standard_fee, 0) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Odontogram Charting -->
                <div class="bg-slate-50/70 rounded-3xl p-5 border border-slate-200/80">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Dental Odontogram (FDI Charting)</h4>
                        <button type="button" @click="clearTeeth()" class="text-xs text-rose-600 hover:underline font-semibold">Clear Teeth</button>
                    </div>

                    <!-- Upper Teeth -->
                    <div class="bg-white p-3 rounded-2xl border border-slate-200/60 mb-3">
                        <div class="flex justify-center gap-1.5 flex-wrap">
                            @foreach(['18','17','16','15','14','13','12','11','21','22','23','24','25','26','27','28'] as $t)
                                <button type="button" @click="toggleTooth('{{ $t }}')"
                                    :class="hasTooth('{{ $t }}') ? 'bg-cyan-600 text-white border-cyan-700 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                    class="w-8 h-10 rounded-xl border font-mono font-bold text-xs flex flex-col items-center justify-center transition">
                                    <svg class="w-2.5 h-2.5 text-current opacity-70 mb-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3C4.5 3 3 5.5 3 8c0 4 2.5 7.5 3.5 11 .5 1.5 2 2 3.5 2s2-1 2.5-3.5c.5-2.5 1-2.5 1.5 0 .5 2.5 1 3.5 2.5 3.5s3-.5 3.5-2C21 15.5 21 12 21 8c0-2.5-1.5-5-4-5-2 0-3.5 1.5-5 1.5S9 3 7 3z"/></svg>
                                    <span>{{ $t }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Lower Teeth -->
                    <div class="bg-white p-3 rounded-2xl border border-slate-200/60 mb-3">
                        <div class="flex justify-center gap-1.5 flex-wrap">
                            @foreach(['48','47','46','45','44','43','42','41','31','32','33','34','35','36','37','38'] as $t)
                                <button type="button" @click="toggleTooth('{{ $t }}')"
                                    :class="hasTooth('{{ $t }}') ? 'bg-cyan-600 text-white border-cyan-700 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                    class="w-8 h-10 rounded-xl border font-mono font-bold text-xs flex flex-col items-center justify-center transition">
                                    <span>{{ $t }}</span>
                                    <svg class="w-2.5 h-2.5 text-current opacity-70 mb-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3C4.5 3 3 5.5 3 8c0 4 2.5 7.5 3.5 11 .5 1.5 2 2 3.5 2s2-1 2.5-3.5c.5-2.5 1-2.5 1.5 0 .5 2.5 1 3.5 2.5 3.5s3-.5 3.5-2C21 15.5 21 12 21 8c0-2.5-1.5-5-4-5-2 0-3.5 1.5-5 1.5S9 3 7 3z"/></svg>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label for="tooth_numbers" class="block text-[11px] font-semibold text-slate-700 mb-1">Tooth Numbers String</label>
                        <input type="text" id="tooth_numbers" name="tooth_numbers" x-model="toothInput"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                    </div>
                </div>

                <!-- Diagnosis -->
                <div>
                    <label for="diagnosis" class="block text-xs font-semibold text-slate-700 mb-1">
                        Clinical Diagnosis <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="diagnosis" name="diagnosis" rows="2" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">{{ old('diagnosis', $treatment->diagnosis) }}</textarea>
                </div>

                <!-- Treatment Rendered -->
                <div>
                    <label for="treatment_rendered" class="block text-xs font-semibold text-slate-700 mb-1">
                        Treatment Rendered / Procedure Steps <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="treatment_rendered" name="treatment_rendered" rows="3" required
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">{{ old('treatment_rendered', $treatment->treatment_rendered) }}</textarea>
                </div>

                <!-- Prescription -->
                <div>
                    <label for="prescription" class="block text-xs font-semibold text-slate-700 mb-1">
                        Prescription / Medications
                    </label>
                    <textarea id="prescription" name="prescription" rows="3"
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-mono text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/20">{{ old('prescription', $treatment->prescription) }}</textarea>
                </div>

                <!-- Confidential Clinical Notes -->
                <div>
                    <label for="clinical_notes" class="block text-xs font-semibold text-amber-800 mb-1">
                        Confidential Clinical Notes (Doctor & Admin Only)
                    </label>
                    <textarea id="clinical_notes" name="clinical_notes" rows="2"
                        class="w-full rounded-xl border border-amber-300 bg-amber-50/20 px-3.5 py-2 text-xs text-slate-900 focus:border-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-600/20">{{ old('clinical_notes', $treatment->clinical_notes) }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" onclick="if(confirm('Are you sure you want to archive this treatment record?')) { document.getElementById('delete-treatment-form').submit(); }"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-rose-200 text-xs font-bold text-rose-600 hover:bg-rose-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Archive Record
                    </button>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('treatments.show', $treatment->id) }}"
                            class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                            Cancel
                        </a>

                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-6 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>

            <form id="delete-treatment-form" action="{{ route('treatments.destroy', $treatment->id) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <script>
        function treatmentEditForm() {
            return {
                selectedTeeth: {{ json_encode($selectedTeeth) }},
                toothInput: '{{ old('tooth_numbers', $treatment->tooth_numbers) }}',
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
                }
            };
        }
    </script>
</x-layouts.app>
