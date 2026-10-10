<x-layouts.app title="Tenant Inspection: {{ $clinic->name }} - Platform Super Owner">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-cyan-600 mb-1">
                <a href="{{ route('platform.clinics.index') }}" class="hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    All Clinics
                </a>
                <span>/</span>
                <span class="text-slate-400">Tenant Dossier</span>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 flex items-center gap-2">
                {{ $clinic->name }}
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Tenant ID: <span class="font-mono font-bold text-slate-700">#{{ $clinic->id }}</span> &bull; Slug: <span class="font-mono text-cyan-600">/clinic/{{ $clinic->slug }}</span> &bull; Registered {{ $clinic->created_at->format('d M Y') }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('clinic.show', $clinic->slug) }}" target="_blank"
                class="inline-flex items-center gap-2 rounded-2xl bg-white border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-700 hover:text-slate-900 hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                View Public Site
            </a>

            <!-- Status Control Form -->
            <form action="{{ route('platform.clinics.status', $clinic) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-2xl border border-slate-200 shadow-sm">
                    <span class="text-xs font-bold text-slate-500">Status:</span>
                    <select name="status" onchange="this.form.submit()"
                        class="text-xs font-extrabold px-2.5 py-1 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 focus:outline-none">
                        <option value="active" {{ $clinic->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ $clinic->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="inactive" {{ $clinic->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </form>
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

    <!-- Tenant Activity KPI Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Team Staff</span>
            <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $clinic->users_count }}</span>
            <span class="text-[10px] text-slate-500">Users provisioned</span>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Patients</span>
            <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $clinic->patients_count }}</span>
            <span class="text-[10px] text-slate-500">Registered records</span>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Appointments</span>
            <span class="text-2xl font-black text-cyan-700 mt-1 block">{{ $clinic->appointments_count }}</span>
            <span class="text-[10px] text-slate-500">Total bookings</span>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Treatments</span>
            <span class="text-2xl font-black text-teal-700 mt-1 block">{{ $clinic->treatment_records_count }}</span>
            <span class="text-[10px] text-slate-500">Clinical entries</span>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Gross Billed</span>
            <span class="text-xl font-black text-slate-900 mt-1 block font-mono">{{ $clinic->currency }} {{ number_format($invoicedTotal, 0) }}</span>
            <span class="text-[10px] text-slate-500">{{ $clinic->invoices_count }} invoices</span>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-sm">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Collections</span>
            <span class="text-xl font-black text-emerald-700 mt-1 block font-mono">{{ $clinic->currency }} {{ number_format($collectedTotal, 0) }}</span>
            <span class="text-[10px] text-amber-600 font-semibold font-mono">Due: {{ number_format($outstandingTotal, 0) }}</span>
        </div>
    </div>

    <!-- 2-Column: Tenant Details & Staff Roster -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Clinic Profile & Configuration Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Clinic Tenant Details
            </h3>

            <dl class="space-y-3.5 text-xs">
                <div>
                    <dt class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Clinic Name</dt>
                    <dd class="font-extrabold text-slate-900 text-sm mt-0.5">{{ $clinic->name }}</dd>
                </div>
                <div>
                    <dt class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Public URL Slug</dt>
                    <dd class="font-mono text-cyan-700 mt-0.5">/clinic/{{ $clinic->slug }}</dd>
                </div>
                <div>
                    <dt class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Contact Helpline</dt>
                    <dd class="font-semibold text-slate-700 mt-0.5">{{ $clinic->phone ?? 'Not specified' }}</dd>
                </div>
                <div>
                    <dt class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">WhatsApp Support</dt>
                    <dd class="font-semibold text-slate-700 mt-0.5">{{ $clinic->whatsapp_number ?? 'Not configured' }}</dd>
                </div>
                <div>
                    <dt class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Official Email</dt>
                    <dd class="font-mono text-slate-700 mt-0.5">{{ $clinic->email ?? 'Not specified' }}</dd>
                </div>
                <div>
                    <dt class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Physical Practice Address</dt>
                    <dd class="text-slate-600 leading-relaxed mt-0.5">{{ $clinic->address ?? 'Not specified' }}</dd>
                </div>
                @if($clinic->emergency_notice)
                    <div>
                        <dt class="font-bold text-amber-700 uppercase tracking-wider text-[10px]">Active Emergency Notice</dt>
                        <dd class="text-amber-900 bg-amber-50 p-2.5 rounded-xl border border-amber-200 mt-0.5 text-[11px] leading-relaxed">
                            {{ $clinic->emergency_notice }}
                        </dd>
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                    <div>
                        <dt class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Currency</dt>
                        <dd class="font-bold text-slate-800 mt-0.5">{{ $clinic->currency }}</dd>
                    </div>
                    <div>
                        <dt class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Timezone</dt>
                        <dd class="font-bold text-slate-800 mt-0.5 truncate">{{ $clinic->timezone }}</dd>
                    </div>
                </div>
            </dl>
        </div>

        <!-- Tenant Staff Directory Table -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-6 py-4.5 border-b border-slate-100">
                    <h3 class="text-sm font-extrabold text-slate-900">
                        Authorized Clinic Staff Accounts ({{ $staffMembers->count() }})
                    </h3>
                    <p class="text-xs text-slate-400">Team members registered under this tenant workspace.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3">Member</th>
                                <th class="px-6 py-3">Role</th>
                                <th class="px-6 py-3">Specialization / Fee</th>
                                <th class="px-6 py-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($staffMembers as $staff)
                                <tr>
                                    <td class="px-6 py-3.5 font-bold text-slate-900">
                                        <div>{{ $staff->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $staff->email }}</div>
                                    </td>
                                    <td class="px-6 py-3.5 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded-md font-bold uppercase text-[10px] bg-slate-100 text-slate-700">
                                            {{ str_replace('_', ' ', $staff->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-600">
                                        @if($staff->role === 'dentist' && $staff->doctorProfile)
                                            <span class="font-semibold text-slate-800">{{ $staff->doctorProfile->specialization }}</span>
                                            <span class="block text-[10px] text-slate-400">
                                                Fee: {{ $clinic->currency }} {{ number_format($staff->doctorProfile->consultation_fee, 0) }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $staff->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $staff->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">No staff members registered.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
