<x-layouts.app title="New Appointment - DentalCare">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('appointments.index', ['date' => $defaultDate]) }}" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700 flex items-center gap-1 mb-1">
                    &larr; Back to Appointments Schedule
                </a>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                    Schedule New Appointment
                </h2>
                <p class="text-xs text-slate-500">
                    Book an appointment directly from front-desk reception or clinic management.
                </p>
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

        <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-sm">
            <form action="{{ route('appointments.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Patient Selection Mode -->
                <div class="border-b border-slate-100 pb-6">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">
                        Patient Selection
                    </label>

                    <div class="flex items-center gap-6 mb-4 text-xs font-medium text-slate-700">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="patient_type" value="existing" checked onchange="togglePatientMode('existing')"
                                class="text-cyan-600 focus:ring-cyan-500">
                            <span>Existing Patient File</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="patient_type" value="new" onchange="togglePatientMode('new')"
                                class="text-cyan-600 focus:ring-cyan-500">
                            <span>Register New Patient</span>
                        </label>
                    </div>

                    <!-- Existing Patient Dropdown -->
                    <div id="existing_patient_box">
                        <label for="patient_id" class="block text-xs font-semibold text-slate-700 mb-1">Select Patient *</label>
                        <select id="patient_id" name="patient_id"
                            class="block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                            <option value="">-- Choose Existing Patient --</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" {{ old('patient_id', $defaultPatientId ?? null) == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->phone }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- New Patient Inputs -->
                    <div id="new_patient_box" class="hidden space-y-3">
                        <div>
                            <label for="patient_name" class="block text-xs font-semibold text-slate-700">Patient Full Name *</label>
                            <input type="text" id="patient_name" name="patient_name" value="{{ old('patient_name') }}" placeholder="e.g. Tariq Mehmood"
                                class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="patient_phone" class="block text-xs font-semibold text-slate-700">Phone / WhatsApp *</label>
                                <input type="text" id="patient_phone" name="patient_phone" value="{{ old('patient_phone') }}" placeholder="0300-1234567"
                                    class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                            </div>
                            <div>
                                <label for="patient_email" class="block text-xs font-semibold text-slate-700">Email (Optional)</label>
                                <input type="email" id="patient_email" name="patient_email" value="{{ old('patient_email') }}" placeholder="patient@example.com"
                                    class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctor & Service Selection -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="doctor_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Assigned Dentist *</label>
                        <select id="doctor_id" name="doctor_id" required
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                            <option value="">-- Choose Doctor --</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}" {{ (old('doctor_id', $defaultDoctorId) == $doc->id) ? 'selected' : '' }}>
                                    {{ $doc->name }} ({{ $doc->doctorProfile->specialization ?? 'General Dentist' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="service_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Treatment Procedure</label>
                        <select id="service_id" name="service_id"
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                            <option value="">General Consultation / Checkup</option>
                            @foreach($services as $srv)
                                <option value="{{ $srv->id }}" {{ old('service_id') == $srv->id ? 'selected' : '' }}>
                                    {{ $srv->name }} (Rs. {{ number_format($srv->standard_fee, 0) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Date & Time Slot -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="appointment_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Appointment Date *</label>
                        <input type="date" id="appointment_date" name="appointment_date" required
                            value="{{ old('appointment_date', $defaultDate) }}"
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                    </div>

                    <div>
                        <label for="starts_at" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Time Slot (HH:MM) *</label>
                        <input type="time" id="starts_at" name="starts_at" required
                            value="{{ old('starts_at', '10:00') }}"
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="patient_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Visit Notes / Chief Complaint</label>
                    <textarea id="patient_notes" name="patient_notes" rows="2" placeholder="e.g. Crown measurement visit, tooth filling review"
                        class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">{{ old('patient_notes') }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('appointments.index', ['date' => $defaultDate]) }}"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 text-white text-xs font-bold uppercase tracking-wider shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                        Schedule Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePatientMode(mode) {
            const existingBox = document.getElementById('existing_patient_box');
            const newBox = document.getElementById('new_patient_box');
            const patientId = document.getElementById('patient_id');
            const patientName = document.getElementById('patient_name');
            const patientPhone = document.getElementById('patient_phone');

            if (mode === 'existing') {
                existingBox.classList.remove('hidden');
                newBox.classList.add('hidden');
                patientId.required = true;
                patientName.required = false;
                patientPhone.required = false;
            } else {
                existingBox.classList.add('hidden');
                newBox.classList.remove('hidden');
                patientId.required = false;
                patientName.required = true;
                patientPhone.required = true;
            }
        }
    </script>
</x-layouts.app>
