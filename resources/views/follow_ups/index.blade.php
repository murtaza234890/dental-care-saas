<x-layouts.app title="Patient Follow-Ups & Recalls - DentalCare">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                Patient Follow-Ups & Clinical Recall Queue
            </h2>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Track post-procedure recovery checkups, suture removals, prosthesis deliveries, and routine dental recalls.
            </p>
        </div>

        <div>
            <a href="{{ route('follow-ups.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition active:scale-[0.99]">
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Schedule Recall Task</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
        <!-- Overdue Tasks -->
        <a href="{{ route('follow-ups.index', ['filter' => 'overdue']) }}"
            class="bg-white p-5 rounded-2xl border transition group {{ $filter === 'overdue' ? 'border-rose-500 ring-2 ring-rose-500/20 shadow-md' : 'border-slate-200/80 shadow-sm hover:border-rose-300' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 group-hover:text-rose-600 transition">Overdue Tasks</span>
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold {{ $overdueCount > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $overdueCount }}</span>
                <span class="text-xs text-slate-500">require immediate call</span>
            </div>
            <div class="mt-2 text-[11px] text-rose-600 font-semibold">
                Passed scheduled due date
            </div>
        </a>

        <!-- Due Today -->
        <a href="{{ route('follow-ups.index', ['filter' => 'today']) }}"
            class="bg-white p-5 rounded-2xl border transition group {{ $filter === 'today' ? 'border-amber-500 ring-2 ring-amber-500/20 shadow-md' : 'border-slate-200/80 shadow-sm hover:border-amber-300' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 group-hover:text-amber-600 transition">Due Today</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-amber-600">{{ $dueTodayCount }}</span>
                <span class="text-xs text-slate-500">calls scheduled today</span>
            </div>
            <div class="mt-2 text-[11px] text-amber-600 font-semibold">
                {{ \Carbon\Carbon::today()->format('l, d M') }}
            </div>
        </a>

        <!-- Upcoming (Next 7 Days) -->
        <a href="{{ route('follow-ups.index', ['filter' => 'upcoming']) }}"
            class="bg-white p-5 rounded-2xl border transition group {{ $filter === 'upcoming' ? 'border-cyan-500 ring-2 ring-cyan-500/20 shadow-md' : 'border-slate-200/80 shadow-sm hover:border-cyan-300' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 group-hover:text-cyan-600 transition">Next 7 Days</span>
                <div class="w-9 h-9 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-cyan-700">{{ $upcomingCount }}</span>
                <span class="text-xs text-slate-500">upcoming recalls</span>
            </div>
            <div class="mt-2 text-[11px] text-cyan-600 font-semibold">
                Scheduled this week
            </div>
        </a>

        <!-- Booked This Month -->
        <a href="{{ route('follow-ups.index', ['filter' => 'booked']) }}"
            class="bg-white p-5 rounded-2xl border transition group {{ $filter === 'booked' ? 'border-emerald-500 ring-2 ring-emerald-500/20 shadow-md' : 'border-slate-200/80 shadow-sm hover:border-emerald-300' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition">Converted Bookings</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-emerald-700">{{ $bookedThisMonthCount }}</span>
                <span class="text-xs text-slate-500">retained patients</span>
            </div>
            <div class="mt-2 text-[11px] text-emerald-600 font-semibold">
                Booked this month
            </div>
        </a>
    </div>

    <!-- Filters & Navigation Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Filter Tabs -->
        <div class="flex flex-wrap items-center gap-1.5">
            <a href="{{ route('follow-ups.index', ['filter' => 'all', 'search' => $search]) }}"
                class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $filter === 'all' ? 'bg-cyan-600 text-white shadow-sm shadow-cyan-600/30' : 'text-slate-600 hover:bg-slate-100' }}">
                All Recalls
            </a>

            <a href="{{ route('follow-ups.index', ['filter' => 'overdue', 'search' => $search]) }}"
                class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $filter === 'overdue' ? 'bg-rose-600 text-white shadow-sm shadow-rose-600/30' : 'text-rose-600 hover:bg-rose-50' }}">
                <span>Overdue</span>
                @if($overdueCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $filter === 'overdue' ? 'bg-white text-rose-700 font-extrabold' : 'bg-rose-100 text-rose-700' }}">{{ $overdueCount }}</span>
                @endif
            </a>

            <a href="{{ route('follow-ups.index', ['filter' => 'today', 'search' => $search]) }}"
                class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $filter === 'today' ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/30' : 'text-amber-700 hover:bg-amber-50' }}">
                <span>Due Today</span>
                @if($dueTodayCount > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $filter === 'today' ? 'bg-white text-amber-800 font-extrabold' : 'bg-amber-100 text-amber-800' }}">{{ $dueTodayCount }}</span>
                @endif
            </a>

            <a href="{{ route('follow-ups.index', ['filter' => 'upcoming', 'search' => $search]) }}"
                class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $filter === 'upcoming' ? 'bg-cyan-600 text-white shadow-sm shadow-cyan-600/30' : 'text-slate-600 hover:bg-slate-100' }}">
                Next 7 Days
            </a>

            <a href="{{ route('follow-ups.index', ['filter' => 'contacted', 'search' => $search]) }}"
                class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $filter === 'contacted' ? 'bg-cyan-600 text-white shadow-sm shadow-cyan-600/30' : 'text-slate-600 hover:bg-slate-100' }}">
                Contacted
            </a>

            <a href="{{ route('follow-ups.index', ['filter' => 'booked', 'search' => $search]) }}"
                class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $filter === 'booked' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/30' : 'text-slate-600 hover:bg-slate-100' }}">
                Booked
            </a>
        </div>

        <!-- Search Form -->
        <form action="{{ route('follow-ups.index') }}" method="GET" class="flex items-center gap-2">
            <input type="hidden" name="filter" value="{{ $filter }}">

            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search patient, phone, reason..."
                    class="rounded-xl border border-slate-300 pl-8 pr-3 py-1.5 text-xs text-slate-700 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20 w-64">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            @if($search)
                <a href="{{ route('follow-ups.index', ['filter' => $filter]) }}" class="px-2.5 py-1.5 text-xs text-slate-500 hover:text-slate-800 transition">
                    Clear
                </a>
            @endif

            <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-800 text-white text-xs font-semibold hover:bg-slate-900 transition">
                Search
            </button>
        </form>
    </div>

    <!-- Follow-Ups Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Due Date</th>
                        <th class="px-6 py-4">Patient Information</th>
                        <th class="px-6 py-4">Recall Reason / Treatment</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Last Contact & Notes</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($followUps as $fu)
                        @php
                            $isOverdue = $fu->status === 'pending' && $fu->due_date->isBefore(today());
                            $isDueToday = $fu->due_date->isToday();
                            $cleanPhone = preg_replace('/[^0-9]/', '', $fu->patient->phone ?? '');
                            // If local format 0300..., convert to 92300... for WhatsApp
                            if (str_starts_with($cleanPhone, '0')) {
                                $whatsappPhone = '92' . substr($cleanPhone, 1);
                            } else {
                                $whatsappPhone = $cleanPhone;
                            }
                            $clinicName = auth()->user()->clinic->name ?? 'DentalCare Clinic';
                            $waMessage = urlencode("Dear {$fu->patient->name}, this is a gentle reminder from {$clinicName} regarding your scheduled dental follow-up for: {$fu->reason}. Please reply to this message to confirm your appointment.");
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition {{ $isOverdue ? 'bg-rose-50/20' : '' }}">
                            <!-- Due Date -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold {{ $isOverdue ? 'text-rose-600' : ($isDueToday ? 'text-amber-600' : 'text-slate-900') }}">
                                        {{ $fu->due_date->format('M d, Y') }}
                                    </span>
                                </div>
                                <div class="mt-0.5">
                                    @if($isOverdue)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 uppercase tracking-wider">
                                            Overdue by {{ max(1, (int) $fu->due_date->diffInDays(today())) }}d
                                        </span>
                                    @elseif($isDueToday)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wider">
                                            Due Today
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400">
                                            In {{ max(1, (int) today()->diffInDays($fu->due_date)) }} days
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Patient Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('patients.show', $fu->patient_id) }}" class="font-bold text-slate-900 hover:text-cyan-600 transition block">
                                    {{ $fu->patient->name }}
                                </a>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-slate-500 font-mono">{{ $fu->patient->phone }}</span>

                                    <!-- Quick WhatsApp Direct Chat Link -->
                                    @if($cleanPhone)
                                        <a href="https://wa.me/{{ $whatsappPhone }}?text={{ $waMessage }}" target="_blank" title="Send WhatsApp Reminder"
                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition">
                                            <span>WhatsApp</span>
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <!-- Reason / Treatment -->
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-800 block">
                                    {{ $fu->reason }}
                                </span>
                                @if($fu->treatmentRecord)
                                    <a href="{{ route('treatments.show', $fu->treatment_record_id) }}" class="text-[11px] text-cyan-600 hover:underline inline-flex items-center gap-1 mt-0.5">
                                        <span>Linked to Treatment #{{ $fu->treatment_record_id }}</span>
                                        @if($fu->treatmentRecord->tooth_numbers)
                                            <span class="text-slate-400 font-mono">(Tooth: {{ $fu->treatmentRecord->tooth_numbers }})</span>
                                        @endif
                                    </a>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($fu->status === 'booked')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Booked
                                    </span>
                                @elseif($fu->status === 'contacted')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Contacted
                                    </span>
                                @elseif($fu->status === 'cancelled')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Dismissed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                @endif
                            </td>

                            <!-- Staff Notes & Contact Info -->
                            <td class="px-6 py-4 text-xs text-slate-500 max-w-xs">
                                @if($fu->last_contacted_at)
                                    <span class="font-semibold text-slate-700 block mb-0.5">
                                        Called: {{ $fu->last_contacted_at->format('d M, h:i A') }}
                                    </span>
                                @endif
                                <p class="text-slate-600 truncate" title="{{ $fu->staff_notes }}">
                                    {{ $fu->staff_notes ?: 'No notes recorded.' }}
                                </p>
                            </td>

                            <!-- Quick Action Buttons -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($fu->status !== 'booked')
                                        <!-- Quick Mark Contacted Form -->
                                        @if($fu->status !== 'contacted')
                                            <form action="{{ route('follow-ups.status', $fu->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="contacted">
                                                <button type="submit" title="Mark Patient Contacted"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-xs font-semibold transition">
                                                    <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    <span>Called</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Convert to Appointment Booking -->
                                        <a href="{{ route('appointments.create', ['patient_id' => $fu->patient_id]) }}" title="Book Appointment for Patient"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-cyan-700 bg-cyan-50 hover:bg-cyan-100 border border-cyan-200 text-xs font-semibold transition">
                                            <svg class="w-3 h-3 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>Book Slot</span>
                                        </a>

                                        <!-- Mark as Booked Form -->
                                        <form action="{{ route('follow-ups.status', $fu->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="booked">
                                            <button type="submit" title="Mark as Booked"
                                                class="p-1.5 rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-xs font-semibold transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Edit Follow-Up -->
                                    <a href="{{ route('follow-ups.edit', $fu->id) }}" title="Edit Details"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-cyan-600 hover:bg-slate-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    <!-- Delete / Dismiss Form -->
                                    <form action="{{ route('follow-ups.destroy', $fu->id) }}" method="POST" class="inline" onsubmit="return confirm('Dismiss this follow-up recall task?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Dismiss"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-xs text-slate-400">
                                No follow-up or recall tasks found for the selected filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($followUps->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $followUps->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
