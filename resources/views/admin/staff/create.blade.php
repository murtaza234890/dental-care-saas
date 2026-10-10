<x-layouts.app title="Onboard Staff Member - DentalCare">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-cyan-600 mb-1">
                <a href="{{ route('admin.staff.index') }}" class="hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Staff Management
                </a>
                <span>/</span>
                <span class="text-slate-400">Onboard New Team Member</span>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Onboard Clinic Staff Member 👤
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Grant role-based operational permissions for dentists, receptionists, or co-administrators.
            </p>
        </div>

        <a href="{{ route('admin.staff.index') }}"
            class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 rounded-2xl hover:bg-slate-50 transition">
            Back to Roster
        </a>
    </div>

    <!-- Error Summary -->
    @if($errors->any())
        <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs font-semibold text-rose-800">
            <div class="font-bold mb-1">Please correct the following errors:</div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Container -->
    <div x-data="staffCreateForm()" class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-8 max-w-4xl">
        <form action="{{ route('admin.staff.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Full Name -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="e.g. Dr. Usman Tariq or Ayesha Khan"
                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        Email Address (Login Username) <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="staff@citydental.pk"
                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition font-mono">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        Phone / Mobile Number (Optional)
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        placeholder="0300-1234567"
                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
                    @error('phone')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role Selection -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        System Access Role <span class="text-rose-500">*</span>
                    </label>
                    <select name="role" x-model="role" required
                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
                        <option value="dentist">Dentist / Dental Surgeon</option>
                        <option value="receptionist">Frontdesk Receptionist</option>
                        <option value="clinic_admin">Clinic Administrator (Full Access)</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        Initial Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password" required minlength="8"
                        placeholder="Minimum 8 characters"
                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
                    @error('password')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        Confirm Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" required minlength="8"
                        placeholder="Re-type password"
                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
                </div>
            </div>

            <!-- Doctor Specific Profile Panel (Conditional when role is dentist) -->
            <div x-show="role === 'dentist'" x-cloak class="p-6 rounded-2xl bg-cyan-50/60 border border-cyan-100 mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-base">🩺</span>
                    <h3 class="text-xs font-black uppercase tracking-wider text-cyan-900">
                        Doctor Profile & Appointment Slot Configuration
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Specialization</label>
                        <input type="text" name="specialization" value="{{ old('specialization', 'General Dental Practitioner') }}"
                            placeholder="e.g. Endodontist, Orthodontist"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Consultation Fee ({{ auth()->user()->clinic->currency ?? 'PKR' }})</label>
                        <input type="number" step="0.01" min="0" name="consultation_fee" value="{{ old('consultation_fee', '1500.00') }}"
                            placeholder="1500"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Default Slot (Minutes)</label>
                        <input type="number" step="5" min="10" max="180" name="slot_duration_minutes" value="{{ old('slot_duration_minutes', '30') }}"
                            placeholder="30"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 font-mono">
                    </div>
                </div>
            </div>

            <!-- Submit Toolbar -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.staff.index') }}"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider text-white bg-gradient-to-r from-cyan-600 to-teal-600 hover:from-cyan-700 hover:to-teal-700 transition shadow-md shadow-cyan-600/25 active:scale-[0.99]">
                    Create Staff Account
                </button>
            </div>
        </form>
    </div>

    <script>
        function staffCreateForm() {
            return {
                role: @js(old('role', 'dentist')),
            };
        }
    </script>
</x-layouts.app>
