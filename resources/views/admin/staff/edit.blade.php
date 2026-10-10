<x-layouts.app title="Edit Staff Member - DentalCare">
    @php
        $isSelf = $staff->id === auth()->id();
        $prof = $staff->doctorProfile;
    @endphp

    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-cyan-600 mb-1">
                <a href="{{ route('admin.staff.index') }}" class="hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Staff Management
                </a>
                <span>/</span>
                <span class="text-slate-400">Edit Member</span>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Edit Staff Member: <span class="text-cyan-700">{{ $staff->name }}</span>
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Update credentials, reassign operational roles, or adjust doctor consultation fees.
            </p>
        </div>

        <a href="{{ route('admin.staff.index') }}"
            class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 rounded-2xl hover:bg-slate-50 transition">
            Back to Roster
        </a>
    </div>

    <!-- Self-account Notice -->
    @if($isSelf)
        <div class="mb-6 rounded-2xl bg-cyan-50 border border-cyan-200 p-4 text-xs font-semibold text-cyan-900 flex items-center gap-2.5">
            <svg class="w-5 h-5 text-cyan-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>You are currently managing your own active administrator profile. Administrator status and active state cannot be removed from yourself.</span>
        </div>
    @endif

    <!-- Protected Record Notice -->
    @if($isReferenced)
        <div class="mb-6 rounded-2xl bg-amber-50 border border-amber-200 p-4 text-xs font-semibold text-amber-900 flex items-center gap-2.5">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>This staff member has historical appointment, clinical treatment, or payment records. If removed, the account will be safely deactivated to protect audit integrity.</span>
        </div>
    @endif

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
    <div x-data="staffEditForm()" class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-8 max-w-4xl">
        <form action="{{ route('admin.staff.update', $staff) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Full Name -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $staff->name) }}" required
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
                    <input type="email" name="email" value="{{ old('email', $staff->email) }}" required
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
                    <input type="text" name="phone" value="{{ old('phone', $staff->phone) }}"
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
                    @if($isSelf)
                        <input type="hidden" name="role" value="clinic_admin">
                        <input type="text" disabled value="Clinic Administrator (Self Profile)"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-100 border border-slate-200 text-sm font-bold text-slate-600 cursor-not-allowed">
                    @else
                        <select name="role" x-model="role" required
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
                            <option value="dentist">Dentist / Dental Surgeon</option>
                            <option value="receptionist">Frontdesk Receptionist</option>
                            <option value="clinic_admin">Clinic Administrator (Full Access)</option>
                        </select>
                    @endif
                    @error('role')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="flex items-center pt-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $staff->is_active) ? 'checked' : '' }}
                            {{ $isSelf ? 'disabled' : '' }}
                            class="w-5 h-5 rounded-lg text-cyan-600 border-slate-300 focus:ring-cyan-500 {{ $isSelf ? 'opacity-50 cursor-not-allowed' : '' }}">
                        @if($isSelf)
                            <input type="hidden" name="is_active" value="1">
                        @endif
                        <div>
                            <span class="text-sm font-bold text-slate-900 block">Account Active</span>
                            <span class="text-xs text-slate-500 block">User can authenticate and perform clinic duties</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Password Reset Section (Optional) -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 mb-8">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 mb-2">
                    Reset Password (Optional)
                </h3>
                <p class="text-xs text-slate-500 mb-4">Leave both fields empty if you do not want to alter the current password.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">New Password</label>
                        <input type="password" name="password" minlength="8"
                            placeholder="Leave blank to keep unchanged"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Confirm New Password</label>
                        <input type="password" name="password_confirmation" minlength="8"
                            placeholder="Re-type new password"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500">
                    </div>
                </div>
            </div>

            <!-- Doctor Specific Profile Panel (Conditional when role is dentist) -->
            <div x-show="role === 'dentist'" x-cloak class="p-6 rounded-2xl bg-cyan-50/60 border border-cyan-100 mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-base">🩺</span>
                    <h3 class="text-xs font-black uppercase tracking-wider text-cyan-900">
                        Doctor Profile & Slot Configuration
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Specialization</label>
                        <input type="text" name="specialization"
                            value="{{ old('specialization', $prof->specialization ?? 'General Dental Practitioner') }}"
                            placeholder="e.g. Endodontist, Orthodontist"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Consultation Fee ({{ auth()->user()->clinic->currency ?? 'PKR' }})</label>
                        <input type="number" step="0.01" min="0" name="consultation_fee"
                            value="{{ old('consultation_fee', $prof->consultation_fee ?? '1500.00') }}"
                            placeholder="1500"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Default Slot (Minutes)</label>
                        <input type="number" step="5" min="10" max="180" name="slot_duration_minutes"
                            value="{{ old('slot_duration_minutes', $prof->slot_duration_minutes ?? '30') }}"
                            placeholder="30"
                            class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 font-mono">
                    </div>
                </div>
            </div>

            <!-- Submit Toolbar -->
            <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                @if(!$isSelf)
                    <button type="button"
                        onclick="if(confirm('Are you sure you want to remove this staff member?')) { document.getElementById('delete-staff-form').submit(); }"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition border border-rose-200">
                        Delete Staff Member
                    </button>
                @else
                    <div></div>
                @endif

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.staff.index') }}"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider text-white bg-gradient-to-r from-cyan-600 to-teal-600 hover:from-cyan-700 hover:to-teal-700 transition shadow-md shadow-cyan-600/25 active:scale-[0.99]">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>

        @if(!$isSelf)
            <form id="delete-staff-form" action="{{ route('admin.staff.destroy', $staff) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

    <script>
        function staffEditForm() {
            return {
                role: @js(old('role', $staff->role)),
            };
        }
    </script>
</x-layouts.app>
