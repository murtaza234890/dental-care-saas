<x-layouts.app title="Appointments Calendar - DentalCare">
    <!-- Header with Date Navigator & New Booking Action -->
    <div class="mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Appointments & Daily Schedule 📅
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Manage patient visits, front-desk check-ins, and doctor chair schedules.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('appointments.create', ['date' => $selectedDate, 'doctor_id' => $selectedDoctorId]) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Appointment
            </a>
        </div>
    </div>

    <!-- Date Navigation & Quick Day Switcher -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm mb-6 flex flex-wrap items-center justify-between gap-4">
        @php
            $currentDateObj = \Carbon\Carbon::parse($selectedDate);
            $prevDate = $currentDateObj->copy()->subDay()->format('Y-m-d');
            $nextDate = $currentDateObj->copy()->addDay()->format('Y-m-d');
            $todayDate = \Carbon\Carbon::today()->format('Y-m-d');
        @endphp

        <div class="flex items-center gap-2">
            <a href="{{ route('appointments.index', array_merge(request()->all(), ['date' => $prevDate])) }}"
                title="Previous Day"
                class="p-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>

            <form action="{{ route('appointments.index') }}" method="GET" class="flex items-center gap-2" id="dateFilterForm">
                @if($selectedDoctorId) <input type="hidden" name="doctor_id" value="{{ $selectedDoctorId }}"> @endif
                @if($selectedStatus) <input type="hidden" name="status" value="{{ $selectedStatus }}"> @endif

                <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                    class="rounded-xl border border-slate-300 px-3.5 py-1.5 text-xs font-bold text-slate-800 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
            </form>

            <a href="{{ route('appointments.index', array_merge(request()->all(), ['date' => $nextDate])) }}"
                title="Next Day"
                class="p-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>

            @if($selectedDate !== $todayDate)
                <a href="{{ route('appointments.index', array_merge(request()->all(), ['date' => $todayDate])) }}"
                    class="px-3 py-1.5 rounded-xl bg-cyan-50 border border-cyan-200 text-cyan-700 text-xs font-bold hover:bg-cyan-100 transition">
                    Jump to Today
                </a>
            @endif
        </div>

        <!-- Filter Controls: Doctor & Status -->
        <form action="{{ route('appointments.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <input type="hidden" name="date" value="{{ $selectedDate }}">

            <!-- Doctor Filter -->
            @if(!auth()->user()->isDentist())
                <select name="doctor_id" onchange="this.form.submit()"
                    class="rounded-xl border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                    <option value="">All Dentists</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ $selectedDoctorId == $doc->id ? 'selected' : '' }}>
                            {{ $doc->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <!-- Status Filter -->
            <select name="status" onchange="this.form.submit()"
                class="rounded-xl border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                <option value="">All Statuses</option>
                <option value="confirmed" {{ $selectedStatus === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="checked_in" {{ $selectedStatus === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                <option value="in_progress" {{ $selectedStatus === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ $selectedStatus === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $selectedStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <!-- Search by Patient Name / Phone -->
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search patient..."
                    class="rounded-xl border border-slate-300 pl-8 pr-3 py-1.5 text-xs text-slate-700 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </form>
    </div>

    <!-- Quick Day Metrics Pills -->
    <div class="grid grid-cols-2 sm:grid-cols-6 gap-3 mb-6 text-xs">
        <div class="bg-white p-3 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-slate-400 block text-[10px] font-bold uppercase">Total Scheduled</span>
            <span class="text-lg font-bold text-slate-900">{{ $metrics['total'] }}</span>
        </div>
        <div class="bg-white p-3 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-emerald-500 block text-[10px] font-bold uppercase">Confirmed</span>
            <span class="text-lg font-bold text-emerald-700">{{ $metrics['confirmed'] }}</span>
        </div>
        <div class="bg-white p-3 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-cyan-500 block text-[10px] font-bold uppercase">Checked In</span>
            <span class="text-lg font-bold text-cyan-700">{{ $metrics['checked_in'] }}</span>
        </div>
        <div class="bg-white p-3 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-amber-500 block text-[10px] font-bold uppercase">In Treatment</span>
            <span class="text-lg font-bold text-amber-700">{{ $metrics['in_progress'] }}</span>
        </div>
        <div class="bg-white p-3 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-blue-500 block text-[10px] font-bold uppercase">Completed</span>
            <span class="text-lg font-bold text-blue-700">{{ $metrics['completed'] }}</span>
        </div>
        <div class="bg-white p-3 rounded-xl border border-slate-200/80 shadow-sm">
            <span class="text-rose-400 block text-[10px] font-bold uppercase">Cancelled</span>
            <span class="text-lg font-bold text-rose-600">{{ $metrics['cancelled'] }}</span>
        </div>
    </div>

    @if ($errors->has('reschedule'))
        <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-xs font-semibold text-rose-800">
            {{ $errors->first('reschedule') }}
        </div>
    @endif

    <!-- Appointments Table Schedule -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Time Slot</th>
                        <th class="px-6 py-4">Patient Information</th>
                        <th class="px-6 py-4">Assigned Dentist</th>
                        <th class="px-6 py-4">Procedure / Notes</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($appointments as $app)
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Time Slot -->
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs">
                                <span class="font-extrabold text-slate-900 block">
                                    {{ \Carbon\Carbon::parse($app->starts_at)->format('h:i A') }}
                                </span>
                                <span class="text-[11px] text-slate-400 block">
                                    to {{ \Carbon\Carbon::parse($app->ends_at)->format('h:i A') }}
                                </span>
                            </td>

                            <!-- Patient -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-xs">{{ $app->patient->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $app->patient->phone }}</div>
                                @if($app->patient->medical_history)
                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        ⚠️ Alert: {{ \Illuminate\Support\Str::limit($app->patient->medical_history, 30) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Dentist -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-700">
                                <span class="font-semibold text-slate-800 block">{{ $app->doctor->name }}</span>
                                <span class="text-[11px] text-slate-400 block">{{ $app->doctor->doctorProfile->specialization ?? 'Dentist' }}</span>
                            </td>

                            <!-- Procedure -->
                            <td class="px-6 py-4 text-xs">
                                <span class="font-semibold text-slate-800 block">{{ $app->service->name ?? 'General Visit' }}</span>
                                @if($app->patient_notes)
                                    <span class="text-[11px] text-slate-400 italic block mt-0.5">"{{ $app->patient_notes }}"</span>
                                @endif
                            </td>

                            <!-- Status Pill -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'confirmed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'checked_in' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                        'in_progress' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'completed' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'pending' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'no_show' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    ][$app->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border capitalize {{ $statusClasses }}">
                                    {{ str_replace('_', ' ', $app->status) }}
                                </span>
                            </td>

                            <!-- Lifecycle Action Buttons -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($app->status === 'confirmed')
                                        <!-- Check-In Action for Front Desk -->
                                        <form action="{{ route('appointments.status', $app->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="checked_in">
                                            <button type="submit"
                                                class="px-2.5 py-1 rounded-lg bg-cyan-50 text-cyan-700 border border-cyan-200 hover:bg-cyan-100 font-bold transition">
                                                Check In
                                            </button>
                                        </form>
                                    @elseif($app->status === 'checked_in')
                                        <!-- Chair Start Treatment Action -->
                                        <form action="{{ route('appointments.status', $app->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="in_progress">
                                            <button type="submit"
                                                class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 font-bold transition">
                                                Start Treatment
                                            </button>
                                        </form>
                                    @elseif($app->status === 'in_progress')
                                        <!-- Complete Treatment Action -->
                                        <form action="{{ route('appointments.status', $app->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit"
                                                class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 font-bold transition">
                                                Complete
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($app->status, ['in_progress', 'completed']) && !auth()->user()->isReceptionist())
                                        <a href="{{ route('treatments.create', ['appointment_id' => $app->id]) }}"
                                            class="px-2 py-1 rounded-lg bg-teal-50 text-teal-700 border border-teal-200 hover:bg-teal-100 font-bold transition flex items-center gap-1 text-[11px]" title="Document Dental Procedure">
                                            <span>🦷 Record</span>
                                        </a>
                                    @endif

                                    <!-- Quick Reschedule Trigger -->
                                    @if(!in_array($app->status, ['completed', 'cancelled']))
                                        <button type="button" onclick="openRescheduleModal('{{ $app->id }}', '{{ $app->appointment_date->format('Y-m-d') }}', '{{ substr($app->starts_at, 0, 5) }}', '{{ addslashes($app->patient->name) }}')"
                                            class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Reschedule">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </button>

                                        <!-- Cancel Action -->
                                        <form action="{{ route('appointments.status', $app->id) }}" method="POST" onsubmit="return confirm('Cancel this appointment?');">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit"
                                                class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Cancel Appointment">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-xs text-slate-400">
                                No appointments found for {{ \Carbon\Carbon::parse($selectedDate)->format('l, d F Y') }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reschedule Modal -->
    <div id="rescheduleModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200">
            <h3 class="text-base font-bold text-slate-900 mb-1">Reschedule Appointment</h3>
            <p class="text-xs text-slate-500 mb-4" id="modalPatientName">Patient: </p>

            <form id="rescheduleForm" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="reschedule_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">New Date *</label>
                    <input type="date" id="reschedule_date" name="appointment_date" required min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                        class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                </div>

                <div>
                    <label for="reschedule_time" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">New Time (HH:MM) *</label>
                    <input type="time" id="reschedule_time" name="starts_at" required
                        class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                </div>

                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeRescheduleModal()"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl bg-cyan-600 text-white text-xs font-bold uppercase tracking-wider shadow-md shadow-cyan-600/20 hover:bg-cyan-700 transition">
                        Confirm Reschedule
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRescheduleModal(appointmentId, currentDate, currentTime, patientName) {
            document.getElementById('modalPatientName').innerText = 'Patient: ' + patientName;
            document.getElementById('reschedule_date').value = currentDate;
            document.getElementById('reschedule_time').value = currentTime;
            document.getElementById('rescheduleForm').action = '/appointments/' + appointmentId + '/reschedule';
            document.getElementById('rescheduleModal').classList.remove('hidden');
        }

        function closeRescheduleModal() {
            document.getElementById('rescheduleModal').classList.add('hidden');
        }
    </script>
</x-layouts.app>
