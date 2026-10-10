<x-layouts.app title="Edit Doctor Schedule - DentalCare">
    @php
        $prof = $doctor->doctorProfile;
        $currentWorkingDays = old('working_days', $prof && $prof->working_days ? $prof->working_days : ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
        $startTime = old('start_time', $prof && $prof->start_time ? substr($prof->start_time, 0, 5) : '10:00');
        $endTime = old('end_time', $prof && $prof->end_time ? substr($prof->end_time, 0, 5) : '20:00');
        $breakStart = old('break_start', $prof && $prof->break_start ? substr($prof->break_start, 0, 5) : '');
        $breakEnd = old('break_end', $prof && $prof->break_end ? substr($prof->break_end, 0, 5) : '');
    @endphp

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
                        Edit Schedule & Profile: {{ $doctor->name }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Update chair shift hours, working weekdays, appointment slot durations, and consultation fee.
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
            <form action="{{ route('doctors.update', $doctor->id) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Section 1: Account Information -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-cyan-800 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        1. Account & Contact Details
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                                Full Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" required
                                value="{{ old('name', $doctor->name) }}"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                                Email Address <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" required
                                value="{{ old('email', $doctor->email) }}"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1">
                                Phone Number
                            </label>
                            <input type="text" id="phone" name="phone"
                                value="{{ old('phone', $doctor->phone) }}"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                                Change Password <span class="text-slate-400 font-normal">(Leave blank to keep current)</span>
                            </label>
                            <input type="password" id="password" name="password"
                                placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Clinical Profile -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-cyan-800 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        2. Clinical Specialization & Slot Settings
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label for="specialization" class="block text-xs font-semibold text-slate-700 mb-1">
                                Specialization Title <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="specialization" name="specialization" required
                                value="{{ old('specialization', $prof->specialization ?? 'General Dental Practitioner') }}"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="consultation_fee" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Consultation Fee (Rs.) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" id="consultation_fee" name="consultation_fee" required
                                    value="{{ old('consultation_fee', $prof->consultation_fee ?? 0) }}"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 font-mono focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                            </div>

                            <div>
                                <label for="slot_duration_minutes" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Appointment Slot Duration <span class="text-rose-500">*</span>
                                </label>
                                <select id="slot_duration_minutes" name="slot_duration_minutes" required
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                                    <option value="15" {{ old('slot_duration_minutes', $prof->slot_duration_minutes ?? 30) == 15 ? 'selected' : '' }}>15 Minutes</option>
                                    <option value="20" {{ old('slot_duration_minutes', $prof->slot_duration_minutes ?? 30) == 20 ? 'selected' : '' }}>20 Minutes</option>
                                    <option value="30" {{ old('slot_duration_minutes', $prof->slot_duration_minutes ?? 30) == 30 ? 'selected' : '' }}>30 Minutes</option>
                                    <option value="45" {{ old('slot_duration_minutes', $prof->slot_duration_minutes ?? 30) == 45 ? 'selected' : '' }}>45 Minutes</option>
                                    <option value="60" {{ old('slot_duration_minutes', $prof->slot_duration_minutes ?? 30) == 60 ? 'selected' : '' }}>60 Minutes</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="bio" class="block text-xs font-semibold text-slate-700 mb-1">
                                Doctor Profile Bio
                            </label>
                            <textarea id="bio" name="bio" rows="2"
                                class="w-full rounded-xl border border-slate-300 p-3 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">{{ old('bio', $prof->bio ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Chair Schedule & Timings -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-cyan-800 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        3. Chair Availability Days & Shift Timings
                    </h3>

                    <!-- Working Days Checkboxes -->
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-semibold text-slate-700">
                                Active Chair Days <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="selectAllWeekdays()" class="text-[11px] font-semibold text-cyan-600 hover:underline">Mon-Fri</button>
                                <span class="text-slate-300">&bull;</span>
                                <button type="button" onclick="selectAllDays()" class="text-[11px] font-semibold text-cyan-600 hover:underline">All 7 Days</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2">
                            @foreach($daysOfWeek as $dayKey => $dayLabel)
                                <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-cyan-50/50 cursor-pointer transition">
                                    <input type="checkbox" name="working_days[]" value="{{ $dayKey }}"
                                        class="day-checkbox rounded text-cyan-600 focus:ring-cyan-500"
                                        {{ in_array($dayKey, $currentWorkingDays, true) ? 'checked' : '' }}>
                                    <span class="text-xs font-bold text-slate-700">{{ substr($dayLabel, 0, 3) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Shift Hours & Break Hours -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div>
                            <label for="start_time" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Shift Start *
                            </label>
                            <input type="time" id="start_time" name="start_time" required
                                value="{{ $startTime }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono focus:border-cyan-600 focus:outline-none">
                        </div>

                        <div>
                            <label for="end_time" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Shift End *
                            </label>
                            <input type="time" id="end_time" name="end_time" required
                                value="{{ $endTime }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono focus:border-cyan-600 focus:outline-none">
                        </div>

                        <div>
                            <label for="break_start" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Break Start
                            </label>
                            <input type="time" id="break_start" name="break_start"
                                value="{{ $breakStart }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono focus:border-cyan-600 focus:outline-none">
                        </div>

                        <div>
                            <label for="break_end" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Break End
                            </label>
                            <input type="time" id="break_end" name="break_end"
                                value="{{ $breakEnd }}"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono focus:border-cyan-600 focus:outline-none">
                        </div>
                    </div>
                </div>

                @if($user->isClinicAdmin() && $user->id !== $doctor->id)
                    <!-- Section 4: Account Active Toggle -->
                    <div class="pt-2">
                        <label class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ $doctor->is_active ? 'checked' : '' }}
                                class="rounded text-cyan-600 focus:ring-cyan-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">Doctor Account Active</span>
                                <span class="text-[11px] text-slate-500">If unchecked, doctor will be hidden from public booking and receptionist appointment scheduler.</span>
                            </div>
                        </label>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('doctors.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Cancel
                    </a>

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-6 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Schedule Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
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
