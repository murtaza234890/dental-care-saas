<x-layouts.public-clinic :clinic="$clinic" :title="$clinic->name">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-b from-cyan-50/60 via-white to-slate-50 pt-16 pb-24 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 relative z-10">
            @if($clinic->emergency_notice)
                <div class="mb-8 rounded-2xl bg-amber-50 border border-amber-200 p-4 text-sm font-semibold text-amber-900 flex items-start gap-3 shadow-sm">
                    <span class="text-lg">📢</span>
                    <div>
                        <span class="font-extrabold block text-xs uppercase tracking-wider text-amber-800">Clinic Announcement</span>
                        <span class="text-xs sm:text-sm text-amber-900">{{ $clinic->emergency_notice }}</span>
                    </div>
                </div>
            @endif
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-800 border border-cyan-200">
                        <svg class="w-4 h-4 text-cyan-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Official Dental Care Portal &bull; {{ $clinic->name }}
                    </span>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                        Precision Dentistry. <br>
                        <span class="bg-gradient-to-r from-cyan-600 to-teal-600 bg-clip-text text-transparent">Confident, Pain-Free Smiles.</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-600 max-w-2xl leading-relaxed">
                        Welcome to {{ $clinic->name }}. Experience hospital-grade sterilization, computerized diagnostics, and compassionate dental care from certified dental surgeons.
                    </p>

                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="#booking-section"
                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-6 py-3.5 text-sm font-bold uppercase tracking-wider text-white shadow-lg shadow-cyan-600/30 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Book Appointment Now
                        </a>

                        @if($clinic->phone)
                            <a href="tel:{{ $clinic->phone }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-300 px-6 py-3.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                Call Helpline
                            </a>
                        @endif
                    </div>

                    <!-- Trust Stats -->
                    <div class="grid grid-cols-3 gap-6 pt-6 border-t border-slate-200/80">
                        <div>
                            <span class="block text-2xl font-extrabold text-slate-900">100%</span>
                            <span class="block text-xs text-slate-500 font-medium">Autoclaved Instruments</span>
                        </div>
                        <div>
                            <span class="block text-2xl font-extrabold text-slate-900">{{ $doctors->count() }}+</span>
                            <span class="block text-xs text-slate-500 font-medium">Certified Specialists</span>
                        </div>
                        <div>
                            <span class="block text-2xl font-extrabold text-slate-900">30 Min</span>
                            <span class="block text-xs text-slate-500 font-medium">Standard Slot Time</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Booking Card Preview -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-3xl p-8 shadow-2xl shadow-cyan-900/10 border border-slate-200/80 relative">
                        <div class="flex items-center justify-between pb-6 border-b border-slate-100">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-cyan-600">Fast Appointment</span>
                                <h3 class="text-xl font-bold text-slate-900">Online Slot Booking</h3>
                            </div>
                            <span class="w-10 h-10 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold">
                                🦷
                            </span>
                        </div>

                        <p class="text-xs text-slate-500 my-4 leading-relaxed">
                            Select your dentist and desired time below. Your appointment will be recorded instantly in the clinic schedule.
                        </p>

                        <a href="#booking-section"
                            class="block w-full text-center py-3.5 px-4 rounded-xl bg-slate-900 text-white text-xs font-bold uppercase tracking-wider hover:bg-slate-800 transition">
                            Jump to Booking Form &darr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Catalog Section -->
    <section id="services" class="py-20 max-w-7xl mx-auto px-4 sm:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-bold uppercase tracking-wider text-cyan-600">Treatments Catalog</span>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Our Dental Treatments & Fees</h2>
            <p class="text-sm text-slate-500 mt-2">Clear, transparent healthcare fees without hidden charges.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($services as $service)
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-cyan-200 transition group flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-4 mb-3">
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-cyan-700 transition">
                                {{ $service->name }}
                            </h3>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-cyan-50 text-cyan-700 shrink-0">
                                {{ $clinic->currency }} {{ number_format($service->standard_fee, 0) }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            {{ $service->description ?? 'Professional clinic treatment delivered by dental surgeons.' }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-400 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            ~{{ $service->estimated_duration_minutes }} Mins
                        </span>
                        <a href="#booking-section" onclick="selectService('{{ $service->id }}')" class="font-bold text-cyan-600 hover:text-cyan-700 hover:underline">
                            Book This &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 text-slate-400 text-sm">
                    No services catalog published yet for this clinic.
                </div>
            @endforelse
        </div>
    </section>

    <!-- Doctors & Specialists Section -->
    <section id="doctors" class="py-20 bg-slate-100/70 border-y border-slate-200/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-cyan-600">Qualified Team</span>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Meet Our Dental Specialists</h2>
                <p class="text-sm text-slate-500 mt-2">Board certified dentists dedicated to gentle and aesthetic oral healthcare.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                @foreach($doctors as $doc)
                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-600 to-teal-500 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-cyan-600/20 shrink-0">
                                    {{ substr($doc->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900">{{ $doc->name }}</h3>
                                    <span class="text-xs font-semibold text-cyan-600 block">
                                        {{ $doc->doctorProfile->specialization ?? 'General Dentist' }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-mono">
                                        Fee: {{ $clinic->currency }} {{ number_format($doc->doctorProfile->consultation_fee ?? 1500, 0) }}
                                    </span>
                                </div>
                            </div>

                            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                {{ $doc->doctorProfile->bio ?? 'Experienced dental surgeon offering preventive and restorative dental care.' }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-500">
                                <strong>Hours:</strong> {{ \Carbon\Carbon::parse($doc->doctorProfile->start_time ?? '10:00')->format('h:i A') }} &ndash; {{ \Carbon\Carbon::parse($doc->doctorProfile->end_time ?? '20:00')->format('h:i A') }}
                            </span>
                            <a href="#booking-section" onclick="selectDoctor('{{ $doc->id }}')" class="font-bold text-cyan-600 hover:text-cyan-700 hover:underline">
                                Select Doctor &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Interactive Online Booking Engine Section -->
    <section id="booking-section" class="py-20 max-w-4xl mx-auto px-4 sm:px-8">
        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200/90 shadow-xl shadow-slate-200/50">
            <div class="text-center max-w-xl mx-auto mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-cyan-600">Real-Time Scheduling</span>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Book Your Dental Visit</h2>
                <p class="text-sm text-slate-500 mt-2">Direct booking into {{ $clinic->name }}'s live doctor calendar.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('clinic.book.store', $clinic->slug) }}" method="POST" class="space-y-6" id="bookingForm">
                @csrf

                <!-- Patient Demographics -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="patient_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Patient Full Name *</label>
                        <input id="patient_name" name="patient_name" type="text" required value="{{ old('patient_name') }}"
                            placeholder="e.g. Muhammad Ali"
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                    </div>

                    <div>
                        <label for="patient_phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">WhatsApp / Phone Number *</label>
                        <input id="patient_phone" name="patient_phone" type="text" required value="{{ old('patient_phone') }}"
                            placeholder="0300-1234567"
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="patient_email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Email (Optional)</label>
                        <input id="patient_email" name="patient_email" type="email" value="{{ old('patient_email') }}"
                            placeholder="patient@example.com"
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                    </div>

                    <div>
                        <label for="service_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Select Treatment</label>
                        <select id="service_id" name="service_id"
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                            <option value="">General Consultation / Checkup</option>
                            @foreach($services as $srv)
                                <option value="{{ $srv->id }}" {{ old('service_id') == $srv->id ? 'selected' : '' }}>
                                    {{ $srv->name }} ({{ $clinic->currency }} {{ number_format($srv->standard_fee, 0) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Doctor & Date Selection -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="doctor_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Choose Dentist *</label>
                        <select id="doctor_id" name="doctor_id" required onchange="loadSlots()"
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                            <option value="">Select a Doctor</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}" {{ old('doctor_id') == $doc->id ? 'selected' : '' }}>
                                    {{ $doc->name }} ({{ $doc->doctorProfile->specialization ?? 'Dentist' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="appointment_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Appointment Date *</label>
                        <input id="appointment_date" name="appointment_date" type="date" required
                            min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                            value="{{ old('appointment_date', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                            onchange="loadSlots()"
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                    </div>
                </div>

                <!-- Dynamic Available Time Slots Grid -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Available Time Slots *
                    </label>

                    <input type="hidden" name="starts_at" id="starts_at_input" required value="{{ old('starts_at') }}">

                    <div id="slots_container" class="p-4 rounded-2xl bg-slate-50 border border-slate-200 min-h-[90px] flex items-center justify-center text-xs text-slate-500">
                        Please select a doctor to view real-time available slots.
                    </div>
                </div>

                <!-- Notes / Reason -->
                <div>
                    <label for="patient_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Reason for Visit / Symptoms (Optional)</label>
                    <textarea id="patient_notes" name="patient_notes" rows="2"
                        placeholder="e.g. Toothache on lower left jaw, sensitivity to cold fluids, or regular cleaning"
                        class="mt-1.5 block w-full rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">{{ old('patient_notes') }}</textarea>
                </div>

                <button type="submit"
                    class="w-full py-4 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-cyan-600/30 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                    Confirm & Schedule Appointment
                </button>
            </form>
        </div>
    </section>

    <!-- Client-side Interactive Slot Fetching Script -->
    <script>
        function selectDoctor(docId) {
            document.getElementById('doctor_id').value = docId;
            loadSlots();
        }

        function selectService(srvId) {
            document.getElementById('service_id').value = srvId;
        }

        async function loadSlots() {
            const doctorId = document.getElementById('doctor_id').value;
            const date = document.getElementById('appointment_date').value;
            const container = document.getElementById('slots_container');
            const startsAtInput = document.getElementById('starts_at_input');

            if (!doctorId || !date) {
                container.innerHTML = `<span class="text-slate-400">Please choose a doctor and date to check available slots.</span>`;
                return;
            }

            container.innerHTML = `<span class="text-cyan-700 flex items-center gap-2">Checking doctor schedule...</span>`;

            try {
                const response = await fetch(`{{ route('clinic.slots', $clinic->slug) }}?doctor_id=${doctorId}&date=${date}`);
                const data = await response.json();

                if (!data.available) {
                    container.innerHTML = `<span class="text-amber-700 font-semibold">${data.message || 'No slots available on this date.'}</span>`;
                    startsAtInput.value = '';
                    return;
                }

                if (!data.slots || data.slots.length === 0) {
                    container.innerHTML = `<span class="text-rose-600 font-semibold">All slots are booked for this date. Please select another date.</span>`;
                    startsAtInput.value = '';
                    return;
                }

                // Render slots grid
                let html = `<div class="grid grid-cols-3 sm:grid-cols-6 gap-2 w-full">`;
                data.slots.forEach(slot => {
                    const isSelected = startsAtInput.value === slot.time;
                    html += `
                        <button type="button" onclick="selectSlot('${slot.time}', this)"
                            class="slot-btn py-2 px-2 rounded-xl text-center font-mono text-xs font-bold border transition ${isSelected ? 'bg-cyan-600 text-white border-cyan-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:border-cyan-400 hover:bg-cyan-50'}">
                            ${slot.label}
                        </button>
                    `;
                });
                html += `</div>`;
                container.innerHTML = html;
            } catch (err) {
                container.innerHTML = `<span class="text-rose-600">Failed to load time slots. Please try again.</span>`;
            }
        }

        function selectSlot(time, btn) {
            document.getElementById('starts_at_input').value = time;
            document.querySelectorAll('.slot-btn').forEach(b => {
                b.classList.remove('bg-cyan-600', 'text-white', 'border-cyan-600', 'shadow-sm');
                b.classList.add('bg-white', 'text-slate-700', 'border-slate-200');
            });
            btn.classList.add('bg-cyan-600', 'text-white', 'border-cyan-600', 'shadow-sm');
            btn.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
        }

        // Trigger on load if doctor is already selected
        document.addEventListener('DOMContentLoaded', () => {
            if (document.getElementById('doctor_id').value) {
                loadSlots();
            }
        });
    </script>
</x-layouts.public-clinic>
