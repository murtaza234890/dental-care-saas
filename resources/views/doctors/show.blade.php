<x-layouts.app title="Doctor Dossier - DentalCare">
    @php
        $prof = $doctor->doctorProfile;
        $activeDays = $prof && $prof->working_days ? $prof->working_days : [];
        $canEdit = auth()->user()->isClinicAdmin() || auth()->id() === $doctor->id;
    @endphp

    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div>
            <a href="{{ route('doctors.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-600 hover:text-cyan-700 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Doctor Roster
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-600 to-teal-500 text-white font-bold text-xl flex items-center justify-center shadow-md shadow-cyan-600/20">
                        {{ substr($doctor->name, 0, 2) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                                {{ $doctor->name }}
                            </h2>
                            @if($doctor->is_active)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-500 border border-slate-200">Inactive</span>
                            @endif
                        </div>
                        <span class="text-sm font-semibold text-cyan-700 block">
                            {{ $prof->specialization ?? 'Dental Surgeon' }}
                        </span>
                    </div>
                </div>

                @if($canEdit)
                    <div>
                        <a href="{{ route('doctors.edit', $doctor->id) }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/20 hover:bg-cyan-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit Schedule
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Schedule Spec Overview Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Shift Timings</span>
                <span class="text-lg font-bold text-slate-900 font-mono block mt-1">
                    {{ $prof && $prof->start_time ? \Carbon\Carbon::parse($prof->start_time)->format('h:i A') : '10:00 AM' }} &ndash;
                    {{ $prof && $prof->end_time ? \Carbon\Carbon::parse($prof->end_time)->format('h:i A') : '08:00 PM' }}
                </span>
                <span class="text-[11px] text-slate-500">Daily clinic chair shift</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Rest / Break Time</span>
                <span class="text-lg font-bold text-slate-900 font-mono block mt-1">
                    @if($prof && $prof->break_start && $prof->break_end)
                        {{ \Carbon\Carbon::parse($prof->break_start)->format('h:i A') }} &ndash; {{ \Carbon\Carbon::parse($prof->break_end)->format('h:i A') }}
                    @else
                        No Break
                    @endif
                </span>
                <span class="text-[11px] text-slate-500">Excluded from slot booking</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Consultation Fee</span>
                <span class="text-lg font-bold text-cyan-700 font-mono block mt-1">
                    Rs. {{ number_format($prof->consultation_fee ?? 0, 0) }}
                </span>
                <span class="text-[11px] text-slate-500">Standard patient fee</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Slot Interval</span>
                <span class="text-lg font-bold text-slate-900 block mt-1">
                    {{ $prof->slot_duration_minutes ?? 30 }} Minutes
                </span>
                <span class="text-[11px] text-slate-500">Duration per chair booking</span>
            </div>
        </div>

        <!-- Working Days & Bio -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Chair Availability Schedule</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($daysOfWeek as $dayKey => $dayLabel)
                        @php
                            $isOnDuty = in_array($dayKey, $activeDays, true);
                        @endphp
                        <div class="p-3 rounded-2xl border flex flex-col items-center justify-center min-w-[90px] {{ $isOnDuty ? 'bg-cyan-50 border-cyan-200 text-cyan-900' : 'bg-slate-50 border-slate-200 text-slate-400 opacity-60' }}">
                            <span class="text-xs font-bold uppercase">{{ substr($dayLabel, 0, 3) }}</span>
                            <span class="text-[10px] font-semibold mt-0.5 {{ $isOnDuty ? 'text-cyan-700' : 'text-slate-400' }}">
                                {{ $isOnDuty ? 'On Duty' : 'Off' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($prof && $prof->bio)
                <div class="pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Doctor Biography</h4>
                    <p class="text-xs text-slate-700 leading-relaxed">{{ $prof->bio }}</p>
                </div>
            @endif
        </div>

        <!-- Upcoming Appointments for Doctor -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Upcoming Scheduled Visits</h3>
                    <p class="text-xs text-slate-500">Next scheduled chair appointments for this doctor</p>
                </div>
                <a href="{{ route('appointments.create', ['doctor_id' => $doctor->id]) }}"
                    class="px-3.5 py-1.5 rounded-xl bg-cyan-50 border border-cyan-200 text-cyan-700 text-xs font-bold hover:bg-cyan-100 transition">
                    + Book for this Doctor
                </a>
            </div>

            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Date & Time</th>
                        <th class="px-6 py-4">Patient</th>
                        <th class="px-6 py-4">Procedure</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($upcomingAppointments as $app)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 whitespace-nowrap font-mono">
                                <span class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($app->appointment_date)->format('M d, Y') }}</span>
                                <span class="text-slate-500 block text-[11px]">{{ \Carbon\Carbon::parse($app->starts_at)->format('h:i A') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('patients.show', $app->patient_id) }}" class="font-bold text-slate-900 hover:text-cyan-600 transition block">
                                    {{ $app->patient->name }}
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $app->patient->phone }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-700">
                                {{ $app->service->name ?? 'General Consultation' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border bg-emerald-50 text-emerald-700 border-emerald-200">
                                    {{ $app->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-xs text-slate-400">
                                No upcoming appointments scheduled for this doctor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
