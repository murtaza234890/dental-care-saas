<x-layouts.app title="Edit Recall Task - DentalCare">
    <div class="max-w-3xl mx-auto">
        <!-- Breadcrumb & Header -->
        <div class="mb-6">
            <a href="{{ route('follow-ups.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-600 hover:text-cyan-700 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Recall Queue
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        Edit Follow-Up Recall Task #{{ $followUp->id }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Update scheduled due date, communication outcome, or staff reminder notes.
                    </p>
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
            <!-- Patient Info Banner -->
            <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Patient</span>
                    <span class="text-sm font-bold text-slate-900">{{ $followUp->patient->name }}</span>
                    <span class="text-xs text-slate-500 font-mono block">{{ $followUp->patient->phone }}</span>
                </div>
                <a href="{{ route('patients.show', $followUp->patient_id) }}" class="text-xs font-semibold text-cyan-600 hover:underline">
                    View Full Dossier &rarr;
                </a>
            </div>

            <form action="{{ route('follow-ups.update', $followUp->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Section 1: Recall Reason -->
                <div>
                    <label for="reason" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Recall Reason / Procedure <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="reason" name="reason" required
                        value="{{ old('reason', $followUp->reason) }}"
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                </div>

                <!-- Section 2: Due Date & Status -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="due_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Scheduled Due Date <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" id="due_date" name="due_date" required
                            value="{{ old('due_date', $followUp->due_date->format('Y-m-d')) }}"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Current Status <span class="text-rose-500">*</span>
                        </label>
                        <select id="status" name="status" required
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                            <option value="pending" {{ old('status', $followUp->status) === 'pending' ? 'selected' : '' }}>Pending Recall</option>
                            <option value="contacted" {{ old('status', $followUp->status) === 'contacted' ? 'selected' : '' }}>Contacted / Awaiting Confirmation</option>
                            <option value="booked" {{ old('status', $followUp->status) === 'booked' ? 'selected' : '' }}>Booked as Appointment</option>
                            <option value="cancelled" {{ old('status', $followUp->status) === 'cancelled' ? 'selected' : '' }}>Dismissed / Cancelled</option>
                        </select>
                    </div>
                </div>

                <!-- Section 3: Staff Notes -->
                <div>
                    <label for="staff_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Staff Communication Notes & History
                    </label>
                    <textarea id="staff_notes" name="staff_notes" rows="4"
                        placeholder="Log contact notes, patient response, preferred call times..."
                        class="w-full rounded-xl border border-slate-300 p-3 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">{{ old('staff_notes', $followUp->staff_notes) }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('follow-ups.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Cancel
                    </a>

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-6 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Update Recall Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
