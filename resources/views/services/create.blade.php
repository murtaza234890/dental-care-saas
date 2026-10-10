<x-layouts.app title="Add Dental Procedure - DentalCare">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-cyan-600 mb-1">
                <a href="{{ route('services.index') }}" class="hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Services Catalog
                </a>
                <span>/</span>
                <span class="text-slate-400">New Procedure</span>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Add Dental Procedure 📋
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Register a clinical procedure into your clinic's catalog, define appointment duration and standard tariff.
            </p>
        </div>

        <a href="{{ route('services.index') }}"
            class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 rounded-2xl hover:bg-slate-50 transition">
            Back to Catalog
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

    <div x-data="serviceCreateForm()">
        <!-- 1-Click Clinical Presets Bar -->
        <div class="bg-gradient-to-r from-cyan-900 via-slate-900 to-slate-900 text-white rounded-3xl p-6 mb-8 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="text-base">⚡</span>
                    <h3 class="text-sm font-black tracking-wide uppercase text-cyan-400">1-Click Clinical Procedure Presets</h3>
                </div>
                <span class="text-xs text-slate-400">Click any procedure below to auto-populate the form</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($presets as $idx => $preset)
                    <button type="button"
                        @click="applyPreset(@js($preset))"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold bg-white/10 hover:bg-cyan-500 hover:text-white border border-white/10 transition flex items-center gap-2 active:scale-95">
                        <span>{{ $preset['name'] }}</span>
                        <span class="px-1.5 py-0.5 rounded-md text-[10px] bg-white/20 text-cyan-200 font-mono">
                            {{ $preset['category'] }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-8 max-w-4xl">
            <form action="{{ route('services.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Procedure Name -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                            Procedure Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" x-model="name" required
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
                        <select name="category" x-model="category" required
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
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
                            <input type="number" step="0.01" min="0" name="standard_fee" x-model="fee" required
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
                            <input type="number" step="5" min="5" max="480" name="estimated_duration_minutes" x-model="duration" required
                                placeholder="30"
                                class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition font-mono">
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-xs text-slate-400 font-semibold">
                                minutes
                            </span>
                        </div>
                        <p class="mt-1 text-[11px] text-slate-400">Used for slot schedule allocation & calendar booking.</p>
                        @error('estimated_duration_minutes')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Availability Checkbox -->
                    <div class="flex items-center pt-6">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="isActive"
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
                        <textarea name="description" x-model="description" rows="3"
                            placeholder="Provide summary clinical scope, what the procedure includes, or post-operative advice..."
                            class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition"></textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Toolbar -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('services.index') }}"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider text-white bg-gradient-to-r from-cyan-600 to-teal-600 hover:from-cyan-700 hover:to-teal-700 transition shadow-md shadow-cyan-600/25 active:scale-[0.99]">
                        Save Dental Procedure
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function serviceCreateForm() {
            return {
                name: @js(old('name', '')),
                category: @js(old('category', 'Preventive')),
                fee: @js(old('standard_fee', '')),
                duration: @js(old('estimated_duration_minutes', '30')),
                description: @js(old('description', '')),
                isActive: @js(old('is_active', '1') == '1'),
                applyPreset(preset) {
                    this.name = preset.name;
                    this.category = preset.category;
                    this.fee = preset.fee;
                    this.duration = preset.duration;
                    this.description = preset.description;
                }
            };
        }
    </script>
</x-layouts.app>
