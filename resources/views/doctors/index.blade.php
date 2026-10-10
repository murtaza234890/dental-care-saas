<x-layouts.app title="Doctor Schedules & Chair Roster - DentalCare">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Doctor Schedules & Chair Roster 🩺
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Manage dental surgeon shifts, active clinic chair days, consultation fees, and break intervals.
            </p>
        </div>

        @if($user->isClinicAdmin())
            <div>
                <a href="{{ route('doctors.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Onboard New Dentist
                </a>
            </div>
        @endif
    </div>

    <!-- Doctor Profiles Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
        @forelse($doctors as $doc)
            @php
                $prof = $doc->doctorProfile;
                $activeDays = $prof && $prof->working_days ? $prof->working_days : [];
                $canEdit = $user->isClinicAdmin() || $user->id === $doc->id;
            @endphp
            <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <!-- Doctor Header -->
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyan-600 to-teal-500 text-white font-bold text-base flex items-center justify-center shadow-md shadow-cyan-600/20">
                                {{ substr($doc->name, 0, 2) }}
                            </div>
                            <div>
                                <h3 class="font-extrabold text-slate-900 text-base leading-snug">
                                    {{ $doc->name }}
                                </h3>
                                <span class="text-xs font-semibold text-cyan-600 block">
                                    {{ $prof->specialization ?? 'General Dental Practitioner' }}
                                </span>
                            </div>
                        </div>

                        <!-- Active Status -->
                        @if($doc->is_active)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-500 border border-slate-200">
                                Inactive
                            </span>
                        @endif
                    </div>

                    <!-- Timings & Fees Spec -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2 mb-4 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Shift Hours:</span>
                            <span class="font-bold text-slate-800 font-mono">
                                {{ $prof && $prof->start_time ? \Carbon\Carbon::parse($prof->start_time)->format('h:i A') : '10:00 AM' }} &ndash;
                                {{ $prof && $prof->end_time ? \Carbon\Carbon::parse($prof->end_time)->format('h:i A') : '08:00 PM' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Break Time:</span>
                            <span class="font-semibold text-slate-700 font-mono">
                                @if($prof && $prof->break_start && $prof->break_end)
                                    {{ \Carbon\Carbon::parse($prof->break_start)->format('h:i A') }} &ndash; {{ \Carbon\Carbon::parse($prof->break_end)->format('h:i A') }}
                                @else
                                    <span class="text-slate-400 font-normal">No scheduled break</span>
                                @endif
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
                            <span class="text-slate-500 font-medium">Consultation Fee:</span>
                            <span class="font-bold text-cyan-700 font-mono">
                                Rs. {{ number_format($prof->consultation_fee ?? 0, 0) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Slot Duration:</span>
                            <span class="font-semibold text-slate-700">
                                {{ $prof->slot_duration_minutes ?? 30 }} Minutes
                            </span>
                        </div>
                    </div>

                    <!-- Working Days Badges -->
                    <div class="mb-4">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Chair Availability Days</span>
                        <div class="flex flex-wrap gap-1">
                            @foreach($daysOfWeek as $dayKey => $dayLabel)
                                @php
                                    $isDayActive = in_array($dayKey, $activeDays, true);
                                @endphp
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase {{ $isDayActive ? 'bg-cyan-100 text-cyan-800 border border-cyan-200' : 'bg-slate-100 text-slate-400 opacity-60' }}">
                                    {{ substr($dayLabel, 0, 3) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-400">
                        {{ $doc->doctor_appointments_count }} Upcoming Visits
                    </span>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('doctors.show', $doc->id) }}" class="font-bold text-cyan-600 hover:text-cyan-700 transition">
                            View Roster
                        </a>

                        @if($canEdit)
                            <a href="{{ route('doctors.edit', $doc->id) }}" class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-cyan-50 hover:text-cyan-700 transition">
                                Edit Schedule
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 rounded-3xl border border-slate-200/80 text-center text-slate-400">
                No doctors registered yet.
            </div>
        @endforelse
    </div>

    <!-- Weekly Clinic Chair Roster Matrix -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8">
        <div class="mb-6">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <span>Weekly Clinic Chair Roster Timetable</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-50 text-cyan-700 border border-cyan-200">
                    7 Days Overview
                </span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Quick schedule matrix showing which dentist is operating clinic dental chairs on each weekday.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border border-slate-200 rounded-2xl overflow-hidden">
                <thead class="bg-slate-50 text-slate-700 border-b border-slate-200 text-[11px] uppercase font-bold">
                    <tr>
                        <th class="p-4 border-r border-slate-200">Doctor / Specialist</th>
                        @foreach($daysOfWeek as $dayKey => $dayLabel)
                            <th class="p-3 text-center border-r border-slate-200 last:border-r-0">
                                {{ $dayLabel }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($doctors as $doc)
                        @php
                            $prof = $doc->doctorProfile;
                            $activeDays = $prof && $prof->working_days ? $prof->working_days : [];
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Doctor Name -->
                            <td class="p-4 border-r border-slate-200 font-bold text-slate-900 whitespace-nowrap">
                                <a href="{{ route('doctors.show', $doc->id) }}" class="hover:text-cyan-600 transition block">
                                    {{ $doc->name }}
                                </a>
                                <span class="text-[10px] text-slate-400 font-normal block">{{ $prof->specialization ?? 'Dentist' }}</span>
                            </td>

                            <!-- Weekday Schedule Cells -->
                            @foreach($daysOfWeek as $dayKey => $dayLabel)
                                @php
                                    $isOnDuty = in_array($dayKey, $activeDays, true);
                                @endphp
                                <td class="p-3 text-center border-r border-slate-200 last:border-r-0 {{ $isOnDuty ? 'bg-cyan-50/40' : 'bg-slate-50/20' }}">
                                    @if($isOnDuty)
                                        <div class="inline-block">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-cyan-600 text-white block mb-1">
                                                On Duty
                                            </span>
                                            <span class="text-[10px] font-mono text-slate-600 block whitespace-nowrap">
                                                {{ $prof && $prof->start_time ? \Carbon\Carbon::parse($prof->start_time)->format('h:i A') : '10 AM' }} &ndash;
                                                {{ $prof && $prof->end_time ? \Carbon\Carbon::parse($prof->end_time)->format('h:i A') : '8 PM' }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-slate-300 font-semibold text-[11px]">&mdash; Off &mdash;</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
