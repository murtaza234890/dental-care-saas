<x-layouts.public-clinic :clinic="$clinic" title="Appointment Confirmed - {{ $clinic->name }}">
    <section class="py-16 max-w-2xl mx-auto px-4 sm:px-8">
        <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl shadow-slate-200/40 text-center">
            <!-- Success Icon -->
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-6 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 block mb-1">Appointment Confirmed</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">We look forward to seeing you!</h1>
            <p class="text-xs text-slate-500 mt-2 max-w-md mx-auto">
                Your appointment has been successfully recorded in the schedule for <strong>{{ $clinic->name }}</strong>.
            </p>

            <!-- Appointment Details Ticket Card -->
            <div class="mt-8 rounded-2xl bg-slate-50 border border-slate-200 p-6 text-left space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                    <span class="text-xs text-slate-500 font-medium">Reference Code</span>
                    <span class="font-mono text-xs font-bold text-slate-900 bg-white px-2.5 py-1 rounded-md border border-slate-200">
                        #APT-{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-0.5">Patient Name</span>
                        <strong class="text-slate-800 text-sm block">{{ $appointment->patient->name }}</strong>
                        <span class="text-slate-500 font-mono text-[11px]">{{ $appointment->patient->phone }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-0.5">Attending Dentist</span>
                        <strong class="text-slate-800 text-sm block">{{ $appointment->doctor->name }}</strong>
                        <span class="text-cyan-700 text-[11px] block">{{ $appointment->doctor->doctorProfile->specialization ?? 'Dental Surgeon' }}</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-200/70 grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-0.5">Date & Time</span>
                        <strong class="text-slate-900 block text-xs">
                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, d M Y') }}
                        </strong>
                        <span class="text-cyan-700 font-mono font-bold block text-sm">
                            {{ \Carbon\Carbon::parse($appointment->starts_at)->format('h:i A') }}
                        </span>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-0.5">Treatment Selected</span>
                        <strong class="text-slate-800 block text-xs">
                            {{ $appointment->service->name ?? 'General Consultation' }}
                        </strong>
                        <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                            {{ $appointment->status }}
                        </span>
                    </div>
                </div>

                @if($clinic->address)
                    <div class="pt-2 border-t border-slate-200/70 text-xs">
                        <span class="text-slate-400 block mb-0.5">Clinic Address</span>
                        <span class="text-slate-700 font-medium">{{ $clinic->address }}</span>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('clinic.show', $clinic->slug) }}"
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-900 text-white text-xs font-bold uppercase tracking-wider hover:bg-slate-800 transition">
                    &larr; Back to Clinic Home
                </a>

                @if($clinic->phone)
                    <a href="tel:{{ $clinic->phone }}"
                        class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider hover:bg-slate-50 transition">
                        Call {{ $clinic->phone }}
                    </a>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public-clinic>
