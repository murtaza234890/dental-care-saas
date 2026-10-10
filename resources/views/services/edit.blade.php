<x-layouts.app title="Edit Dental Procedure - DentalCare">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-cyan-600 mb-1">
                <a href="{{ route('services.index') }}" class="hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Services Catalog
                </a>
                <span>/</span>
                <span class="text-slate-400">Edit Procedure</span>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Edit Dental Procedure: <span class="text-cyan-700">{{ $service->name }}</span>
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Update tariffs, category classification, chair time duration, or catalog visibility.
            </p>
        </div>

        <a href="{{ route('services.index') }}"
            class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 rounded-2xl hover:bg-slate-50 transition">
            Back to Catalog
        </a>
    </div>

    <!-- Historical Reference Notice -->
    @if($isReferenced)
        <div class="mb-6 rounded-2xl bg-amber-50 border border-amber-200 p-4 text-xs font-semibold text-amber-900 flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <span class="font-bold block text-sm">Protected Clinical & Billing Record</span>
                <span class="text-amber-800">
                    This procedure is referenced by historical appointments, treatment records, or invoices. If removed, the system will automatically deactivate it rather than hard delete it to safeguard financial audit integrity.
                </span>
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

    <!-- Edit Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-8 max-w-4xl">
        <form action="{{ route('services.update', $service) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Procedure Name -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        Procedure Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $service->name) }}" required
                        placeholder="e.g. Scaling & Polishing (Teeth Cleaning)"
                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category Specialty -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        Clinical Specialty Category <span class="text-rose-500">*</span>
                    </label>
                    <select name="category" required
                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category', $service->category) === $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Standard Fee -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        Standard Fee ({{ auth()->user()->clinic->currency ?? 'PKR' }}) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-bold text-slate-400">
                            {{ auth()->user()->clinic->currency ?? 'PKR' }}
                        </span>
                        <input type="number" step="0.01" min="0" name="standard_fee" value="{{ old('standard_fee', $service->standard_fee) }}" required
                            placeholder="0.00"
                            class="w-full pl-14 pr-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition font-mono">
                    </div>
                    @error('standard_fee')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estimated Duration (Minutes) -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        Estimated Duration (Minutes) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" step="5" min="5" max="480" name="estimated_duration_minutes" value="{{ old('estimated_duration_minutes', $service->estimated_duration_minutes) }}" required
                            placeholder="30"
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition font-mono">
                        <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-xs text-slate-400 font-semibold">
                            minutes
                        </span>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-400">Slot allocation interval on appointment calendars.</p>
                    @error('estimated_duration_minutes')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Availability Checkbox -->
                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                            class="w-5 h-5 rounded-lg text-cyan-600 border-slate-300 focus:ring-cyan-500">
                        <div>
                            <span class="text-sm font-bold text-slate-900 block">Available for Booking</span>
                            <span class="text-xs text-slate-500 block">Procedure appears in appointment dropdowns & public clinic site</span>
                        </div>
                    </label>
                </div>

                <!-- Detailed Description -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        Clinical Description / Patient Instructions (Optional)
                    </label>
                    <textarea name="description" rows="3"
                        placeholder="Provide summary clinical scope, what the procedure includes, or post-operative advice..."
                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">{{ old('description', $service->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Toolbar -->
            <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                <!-- Delete Button -->
                <button type="button"
                    onclick="if(confirm('Are you sure you want to delete or deactivate this dental procedure?')) { document.getElementById('delete-service-form').submit(); }"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 transition border border-rose-200">
                    Delete Procedure
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('services.index') }}"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider text-white bg-gradient-to-r from-cyan-600 to-teal-600 hover:from-cyan-700 hover:to-teal-700 transition shadow-md shadow-cyan-600/25 active:scale-[0.99]">
                        Update Dental Procedure
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-service-form" action="{{ route('services.destroy', $service) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</x-layouts.app>
