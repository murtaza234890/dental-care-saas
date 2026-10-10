<x-layouts.app title="Onboard New Doctor - DentalCare">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb & Header -->
        <div class="mb-6">
            <a href="{{ route('doctors.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-600 hover:text-cyan-700 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Doctor Roster
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        Onboard New Dental Surgeon
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Register doctor login credentials, dental specialty, consultation fees, and chair working schedule.
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

        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm">
            <form action="{{ route('doctors.store') }}" method="POST" class="space-y-8">
                @csrf

                <!-- Section 1: Doctor Account Credentials -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-cyan-800 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        1. Doctor Account & Login Credentials
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                                Full Name (with Title) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" required
                                value="{{ old('name', 'Dr. ') }}"
                                placeholder="Dr. Muhammad Usman"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                                Login Email Address <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" required
                                value="{{ old('email') }}"
                                placeholder="dr.usman@clinic.com"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1">
                                Mobile / WhatsApp Phone
                            </label>
                            <input type="text" id="phone" name="phone"
                                value="{{ old('phone') }}"
                                placeholder="0300-1234567"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                                Temporary Login Password <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" id="password" name="password" required
                                placeholder="Minimum 6 characters"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Clinical Profile & Specialization -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-cyan-800 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        2. Clinical Specialization & Pricing
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="specialization" class="block text-xs font-semibold text-slate-700">
                                    Specialty / Clinical Title <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400">Click a title to auto-fill</span>
                            </div>

                            <!-- Specialization Chips -->
                            <div class="flex flex-wrap gap-1.5 mb-2.5">
                                <button type="button" onclick="setSpecialty('General Dental Practitioner')" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-700 border border-slate-200 transition">+ General Dentist</button>
                                <button type="button" onclick="setSpecialty('Orthodontist & Dentofacial Orthopaedics')" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-700 border border-slate-200 transition">+ Orthodontist</button>
                                <button type="button" onclick="setSpecialty('Endodontist (Root Canal Specialist)')" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-700 border border-slate-200 transition">+ Endodontist</button>
                                <button type="button" onclick="setSpecialty('Oral & Maxillofacial Surgeon')" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-700 border border-slate-200 transition">+ Oral Surgeon</button>
                                <button type="button" onclick="setSpecialty('Prosthodontist & Implantologist')" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-700 border border-slate-200 transition">+ Prosthodontist</button>
                                <button type="button" onclick="setSpecialty('Periodontist & Gum Specialist')" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-cyan-50 hover:text-cyan-700 text-slate-700 border border-slate-200 transition">+ Periodontist</button>
                            </div>

                            <input type="text" id="specialization" name="specialization" required
                                value="{{ old('specialization', 'General Dental Practitioner') }}"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="consultation_fee" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Consultation Fee (Rs.) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" id="consultation_fee" name="consultation_fee" required
                                    value="{{ old('consultation_fee', 1500) }}"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 font-mono focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                            </div>

                            <div>
                                <label for="slot_duration_minutes" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Appointment Slot Duration <span class="text-rose-500">*</span>
                                </label>
                                <select id="slot_duration_minutes" name="slot_duration_minutes" required
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                                    <option value="15" {{ old('slot_duration_minutes') == '15' ? 'selected' : '' }}>15 Minutes (Quick checkup)</option>
                                    <option value="20" {{ old('slot_duration_minutes') == '20' ? 'selected' : '' }}>20 Minutes</option>
                                    <option value="30" {{ old('slot_duration_minutes', '30') == '30' ? 'selected' : '' }}>30 Minutes (Standard)</option>
                                    <option value="45" {{ old('slot_duration_minutes') == '45' ? 'selected' : '' }}>45 Minutes</option>
                                    <option value="60" {{ old('slot_duration_minutes') == '60' ? 'selected' : '' }}>60 Minutes (Long procedure)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="bio" class="block text-xs font-semibold text-slate-700 mb-1">
                                Doctor Bio & Credentials (Visible on Public Booking Portal)
                            </label>
                            <textarea id="bio" name="bio" rows="2"
                                placeholder="e.g. BDS, RDS, FCPS Resident with over 8 years of clinical experience in smile design..."
                                class="w-full rounded-xl border border-slate-300 p-3 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">{{ old('bio') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Chair Schedule & Working Days -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-cyan-800 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        3. Chair Availability Days & Shift Timings
                    </h3>

                    <!-- Working Days Multi-checkboxes -->
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-semibold text-slate-700">
                                Working Days on Chair <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="selectAllWeekdays()" class="text-[11px] font-semibold text-cyan-600 hover:underline">Mon-Fri</button>
                                <span class="text-slate-300">&bull;</span>
                                <button type="button" onclick="selectAllDays()" class="text-[11px] font-semibold text-cyan-600 hover:underline">All 7 Days</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2">
                            @php
                                $oldDays = old('working_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
                            @endphp
                            @foreach($daysOfWeek as $dayKey => $dayLabel)
                                <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-cyan-50/50 cursor-pointer transition">
                                    <input type="checkbox" name="working_days[]" value="{{ $dayKey }}"
                                        class="day-checkbox rounded text-cyan-600 focus:ring-cyan-500"
                                        {{ in_array($dayKey, $oldDays, true) ? 'checked' : '' }}>
                                    <span class="text-xs font-bold text-slate-700">{{ substr($dayLabel, 0, 3) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Shift Hours & Break Hours -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div>
                            <label for="start_time" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Shift Start Time *
                            </label>
                            <input type="time" id="start_time" name="start_time" required
                                value="{{ old('start_time', '10:00') }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono focus:border-cyan-600 focus:outline-none">
                        </div>

                        <div>
                            <label for="end_time" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Shift End Time *
                            </label>
                            <input type="time" id="end_time" name="end_time" required
                                value="{{ old('end_time', '20:00') }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono focus:border-cyan-600 focus:outline-none">
                        </div>

                        <div>
                            <label for="break_start" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Break Start (Optional)
                            </label>
                            <input type="time" id="break_start" name="break_start"
                                value="{{ old('break_start', '14:00') }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono focus:border-cyan-600 focus:outline-none">
                        </div>

                        <div>
                            <label for="break_end" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Break End (Optional)
                            </label>
                            <input type="time" id="break_end" name="break_end"
                                value="{{ old('break_end', '15:00') }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono focus:border-cyan-600 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('doctors.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Cancel
                    </a>

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-6 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Complete Doctor Onboarding
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function setSpecialty(title) {
            document.getElementById('specialization').value = title;
        }

        function selectAllWeekdays() {
            let weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
            document.querySelectorAll('.day-checkbox').forEach(cb => {
                cb.checked = weekdays.includes(cb.value);
            });
        }

        function selectAllDays() {
            document.querySelectorAll('.day-checkbox').forEach(cb => {
                cb.checked = true;
            });
        }
    </script>
</x-layouts.app>
