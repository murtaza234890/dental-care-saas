<x-layouts.app title="Clinical Treatments - DentalCare">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Clinical Treatments and Odontogram 🦷
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Manage clinical records, dental charting, diagnoses, and medical prescriptions.
            </p>
        </div>

        @if(!auth()->user()->isReceptionist())
            <div>
                <a href="{{ route('treatments.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Record New Procedure
                </a>
            </div>
        @endif
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Total Treatments</span>
                <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $totalTreatments }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-cyan-100 text-cyan-800 flex items-center justify-center font-bold">
                🦷
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-teal-600 block">Unique Patients Treated</span>
                <span class="text-2xl font-extrabold text-teal-700 mt-1 block">{{ $uniquePatientsTreated }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-teal-100 text-teal-800 flex items-center justify-center font-bold">
                👥
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-500 block">Procedures This Month</span>
                <span class="text-2xl font-extrabold text-indigo-700 mt-1 block">{{ $treatmentsThisMonth }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-800 flex items-center justify-center font-bold">
                📅
            </div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm mb-6">
        <form action="{{ route('treatments.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <!-- Search Bar -->
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by patient, phone, diagnosis..."
                    class="w-full rounded-xl border border-slate-300 pl-9 pr-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Doctor Filter -->
            <select name="doctor_id" onchange="this.form.submit()"
                class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                <option value="">All Dentists</option>
                @foreach($doctors as $doc)
                    <option value="{{ $doc->id }}" {{ $doctorId == $doc->id ? 'selected' : '' }}>
                        {{ $doc->name }}
                    </option>
                @endforeach
            </select>

            <!-- Service Filter -->
            <select name="service_id" onchange="this.form.submit()"
                class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                <option value="">All Services</option>
                @foreach($services as $srv)
                    <option value="{{ $srv->id }}" {{ $serviceId == $srv->id ? 'selected' : '' }}>
                        {{ $srv->name }}
                    </option>
                @endforeach
            </select>

            <!-- Tooth Number Search -->
            <div class="w-28">
                <input type="text" name="tooth" value="{{ $tooth }}" placeholder="Tooth #"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 placeholder-slate-400 font-mono focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
            </div>

            <button type="submit"
                class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition">
                Filter
            </button>

            @if($search || $doctorId || $serviceId || $tooth)
                <a href="{{ route('treatments.index') }}" class="text-xs font-semibold text-rose-600 hover:underline px-2">
                    Clear Filters
                </a>
            @endif
        </form>
    </div>

    <!-- Treatment Records Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Date & Time</th>
                        <th class="px-6 py-4">Patient Demographics</th>
                        <th class="px-6 py-4">Attending Dentist</th>
                        <th class="px-6 py-4">Procedure & Tooth #</th>
                        <th class="px-6 py-4">Diagnosis & Rendered Care</th>
                        <th class="px-6 py-4">Prescription</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($treatments as $treatment)
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Date & Time -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <div class="font-bold text-slate-900">{{ $treatment->created_at->format('M d, Y') }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $treatment->created_at->format('h:i A') }}</div>
                            </td>

                            <!-- Patient -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <a href="{{ route('patients.show', $treatment->patient_id) }}" class="font-bold text-slate-900 hover:text-cyan-700 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold text-[10px] shrink-0">
                                        {{ substr($treatment->patient->name, 0, 1) }}
                                    </span>
                                    <span>{{ $treatment->patient->name }}</span>
                                </a>
                                <span class="text-[11px] text-slate-400 block font-mono pl-8">{{ $treatment->patient->phone }}</span>
                            </td>

                            <!-- Dentist -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <span class="font-bold text-slate-800">{{ $treatment->doctor->name }}</span>
                                <span class="block text-[11px] text-slate-400">{{ $treatment->doctor->doctorProfile->specialization ?? 'General Dentist' }}</span>
                            </td>

                            <!-- Procedure & Tooth -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <span class="font-semibold text-slate-800 block">{{ $treatment->service->name ?? 'Clinical Procedure' }}</span>
                                @if($treatment->tooth_numbers)
                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-md bg-cyan-50 border border-cyan-200 text-cyan-800 font-mono text-[11px] font-bold">
                                        <span>Tooth:</span>
                                        <span>#{{ $treatment->tooth_numbers }}</span>
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400">Full Arch / General</span>
                                @endif
                            </td>

                            <!-- Diagnosis & Treatment -->
                            <td class="px-6 py-4 text-xs max-w-[240px]">
                                <div class="font-semibold text-slate-800 truncate" title="{{ $treatment->diagnosis }}">
                                    {{ $treatment->diagnosis }}
                                </div>
                                <div class="text-[11px] text-slate-400 truncate mt-0.5" title="{{ $treatment->treatment_rendered }}">
                                    {{ $treatment->treatment_rendered }}
                                </div>
                            </td>

                            <!-- Prescription Rx -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                @if($treatment->prescription)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Rx Issued
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400">None</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('treatments.show', $treatment->id) }}"
                                        class="px-2.5 py-1 rounded-lg bg-cyan-50 text-cyan-700 border border-cyan-200 hover:bg-cyan-100 font-bold transition">
                                        View Dossier
                                    </a>

                                    @if(!auth()->user()->isReceptionist())
                                        <a href="{{ route('treatments.edit', $treatment->id) }}"
                                            class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Edit Treatment Record">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-xs text-slate-400">
                                No clinical treatment records found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($treatments->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $treatments->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
