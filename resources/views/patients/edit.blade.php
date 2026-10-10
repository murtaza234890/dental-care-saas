<x-layouts.app title="Edit Patient - {{ $patient->name }}">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb & Header -->
        <div class="mb-6">
            <a href="{{ route('patients.show', $patient->id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-600 hover:text-cyan-700 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Patient Dossier
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        Edit Patient Profile
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Updating demographic and medical alert details for <span class="font-bold text-slate-800">{{ $patient->name }}</span> (ID #{{ $patient->id }}).
                    </p>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 text-xs font-semibold self-start sm:self-auto">
                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                    Tenant Isolated
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800">
                <div class="font-bold mb-1 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Please correct the following errors:
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm">
            <form action="{{ route('patients.update', $patient->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Section: Personal Demographics -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-cyan-700 flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Personal Demographics
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                                Full Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $patient->name) }}" required
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1">
                                Phone Number <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $patient->phone) }}" required
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20 font-mono">
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                                Email Address <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email', $patient->email) }}"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-xs font-semibold text-slate-700 mb-1">
                                Date of Birth <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <input type="date" id="date_of_birth" name="date_of_birth"
                                value="{{ old('date_of_birth', $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : '') }}"
                                max="{{ date('Y-m-d') }}"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>

                        <div>
                            <label for="gender" class="block text-xs font-semibold text-slate-700 mb-1">
                                Gender <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <select id="gender" name="gender"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                                <option value="">-- Select Gender --</option>
                                <option value="male" {{ old('gender', $patient->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $patient->gender) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div>
                            <label for="blood_group" class="block text-xs font-semibold text-slate-700 mb-1">
                                Blood Group <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <select id="blood_group" name="blood_group"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                                <option value="">-- Select Blood Group --</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group', $patient->blood_group) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address" class="block text-xs font-semibold text-slate-700 mb-1">
                                Residential Address <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <textarea id="address" name="address" rows="2"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">{{ old('address', $patient->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section: Clinical Alert & Medical History -->
                <div class="pt-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-rose-700 flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Medical History & Drug Allergies
                    </h3>

                    <div>
                        <label for="medical_history" class="block text-xs font-semibold text-slate-700 mb-1">
                            Medical Alerts, Chronic Conditions & Drug Allergies
                        </label>
                        <textarea id="medical_history" name="medical_history" rows="3"
                            placeholder="e.g. Allergic to Penicillin; Diagnosed with Type-2 Diabetes..."
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500/20">{{ old('medical_history', $patient->medical_history) }}</textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <!-- Delete / Archive Patient Form -->
                        <button type="button" onclick="if(confirm('Are you sure you want to archive this patient file? All associated records will remain protected in clinic archive.')) { document.getElementById('delete-patient-form').submit(); }"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-rose-200 text-xs font-bold text-rose-600 hover:bg-rose-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Archive Patient File
                        </button>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('patients.show', $patient->id) }}"
                            class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                            Cancel
                        </a>

                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-6 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>

            <form id="delete-patient-form" action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</x-layouts.app>
