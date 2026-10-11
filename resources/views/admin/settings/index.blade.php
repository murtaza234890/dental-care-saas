<x-layouts.app title="Clinic Settings - DentalCare">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 flex items-center gap-2">
                Clinic Workspace Settings
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Configure clinic identity, public online booking portal, WhatsApp recall channel, and billing currency.
            </p>
        </div>

        <div>
            <a href="{{ route('clinic.show', $clinic->slug) }}" target="_blank"
                class="inline-flex items-center gap-2 rounded-2xl bg-white border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-700 hover:text-slate-900 hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                View Public Clinic Site
            </a>
        </div>
    </div>

    <!-- Alert / Status Notifications -->
    @if(session('status'))
        <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm font-semibold text-emerald-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('status') }}</span>
            </div>
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

    <!-- Settings Form Container -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-8 max-w-4xl">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Section 1: Clinic Identity & Public Portal -->
            <div class="mb-8">
                <div class="flex items-center gap-2.5 mb-4 pb-2 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </span>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">
                        Clinic Identity & Public Web Presence
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Clinic Name -->
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                            Clinic Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $clinic->name) }}" required
                            placeholder="e.g. City Dental & Implant Centre"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
                        @error('name')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Clinic Slug -->
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                            Public URL Slug <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs text-slate-400 font-mono">
                                /clinic/
                            </span>
                            <input type="text" name="slug" value="{{ old('slug', $clinic->slug) }}" required
                                placeholder="city-dental"
                                class="w-full pl-20 pr-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition font-mono">
                        </div>
                        <p class="mt-1 text-[11px] text-slate-400">Used for patient online booking portal address.</p>
                        @error('slug')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Operational Status -->
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                            Clinic Workspace Status <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" required
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
                            <option value="active" {{ old('status', $clinic->status) === 'active' ? 'selected' : '' }}>Active & Accepting Bookings</option>
                            <option value="inactive" {{ old('status', $clinic->status) === 'inactive' ? 'selected' : '' }}>Temporarily Inactive / Paused</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Contact & Patient Communication Channels -->
            <div class="mb-8">
                <div class="flex items-center gap-2.5 mb-4 pb-2 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </span>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">
                        Contact Details & Patient Channels
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Phone -->
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                            Clinic Contact Phone
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', $clinic->phone) }}"
                            placeholder="0300-1234567 or 051-2223344"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
                        @error('phone')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- WhatsApp Number -->
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                            WhatsApp Recall Number (Optional)
                        </label>
                        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $clinic->whatsapp_number) }}"
                            placeholder="e.g. 923001234567"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition font-mono">
                        <p class="mt-1 text-[11px] text-slate-400">Used for 1-click patient WhatsApp reminders.</p>
                        @error('whatsapp_number')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                            Official Inquiries Email
                        </label>
                        <input type="email" name="email" value="{{ old('email', $clinic->email) }}"
                            placeholder="info@citydental.pk"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition font-mono">
                        @error('email')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Physical Address -->
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                            Practice Physical Address
                        </label>
                        <textarea name="address" rows="2"
                            placeholder="e.g. Plaza 14, Sector F-7 Markaz, Islamabad"
                            class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">{{ old('address', $clinic->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Emergency Announcements Banner -->
            <div class="mb-8">
                <div class="flex items-center gap-2.5 mb-4 pb-2 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    </span>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">
                        Public Portal Emergency Notice & Announcements
                    </h3>
                </div>

                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        Notice Banner Message (Displayed at the top of your public booking page)
                    </label>
                    <textarea name="emergency_notice" rows="2"
                        placeholder="e.g. Walk-in consultations available every Friday after 4:00 PM. Please arrive 10 minutes prior to your allocated slot."
                        class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">{{ old('emergency_notice', $clinic->emergency_notice) }}</textarea>
                    <p class="mt-1 text-[11px] text-slate-400">Leave blank if no announcement is currently active.</p>
                    @error('emergency_notice')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Section 4: Currency & Regional Localization -->
            <div class="mb-8">
                <div class="flex items-center gap-2.5 mb-4 pb-2 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    </span>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">
                        Financial & Regional Localization
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Currency -->
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                            Billing Currency <span class="text-rose-500">*</span>
                        </label>
                        <select name="currency" required
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
                            @foreach($currencies as $code => $label)
                                <option value="{{ $code }}" {{ old('currency', $clinic->currency) === $code ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-[11px] text-slate-400">Applied across invoices, counter receipts, and procedure catalog.</p>
                        @error('currency')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Timezone -->
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                            Operating Timezone <span class="text-rose-500">*</span>
                        </label>
                        <select name="timezone" required
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
                            @foreach($timezones as $tz => $tzLabel)
                                <option value="{{ $tz }}" {{ old('timezone', $clinic->timezone) === $tz ? 'selected' : '' }}>
                                    {{ $tzLabel }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-[11px] text-slate-400">Ensures accurate doctor shift calculations & slot calendars.</p>
                        @error('timezone')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Toolbar -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider text-white bg-gradient-to-r from-cyan-600 to-teal-600 hover:from-cyan-700 hover:to-teal-700 transition shadow-md shadow-cyan-600/25 active:scale-[0.99]">
                    Save Workspace Settings
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
