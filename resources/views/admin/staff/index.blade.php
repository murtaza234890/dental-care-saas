<x-layouts.app title="Clinic Staff Management - DentalCare">
    <!-- Header & Action Bar -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 flex items-center gap-2">
                Clinic Staff & Team Access 👥
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Manage practitioner credentials, configure team roles, and maintain secure operational access for <strong class="text-slate-800">{{ auth()->user()->clinic->name }}</strong>.
            </p>
        </div>

        <div>
            <a href="{{ route('admin.staff.create') }}"
                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-600 to-teal-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Onboard Staff Member
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

    @if(session('error'))
        <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-sm font-semibold text-rose-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Staffing KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- 1. Total Staff -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Team</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $metrics['total'] }}</span>
                <span class="text-[11px] font-semibold text-slate-500 mt-0.5 block">Registered users</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <!-- 2. Active Accounts -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Active Accounts</span>
                <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $metrics['active'] }}</span>
                <span class="text-[11px] font-semibold text-emerald-700 mt-0.5 block">Can sign in right now</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- 3. Dentists -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Dental Surgeons</span>
                <span class="text-2xl font-black text-cyan-700 mt-1 block">{{ $metrics['dentists'] }}</span>
                <span class="text-[11px] font-semibold text-slate-500 mt-0.5 block">Clinical practitioners</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>

        <!-- 4. Front Desk Staff -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Reception Staff</span>
                <span class="text-2xl font-black text-amber-600 mt-1 block">{{ $metrics['receptionists'] }}</span>
                <span class="text-[11px] font-semibold text-slate-500 mt-0.5 block">Front desk & scheduling</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.staff.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3 justify-between">
            <!-- Search Input -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search staff member by name, email, or phone..."
                    class="w-full pl-10 pr-4 py-2.5 text-xs font-semibold rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
            </div>

            <!-- Role & Status Dropdowns -->
            <div class="flex items-center gap-2">
                <select name="role" onchange="this.form.submit()"
                    class="text-xs font-semibold px-3 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition text-slate-700">
                    <option value="all" {{ $roleFilter === 'all' ? 'selected' : '' }}>All Roles</option>
                    <option value="clinic_admin" {{ $roleFilter === 'clinic_admin' ? 'selected' : '' }}>Clinic Administrators</option>
                    <option value="dentist" {{ $roleFilter === 'dentist' ? 'selected' : '' }}>Dentists / Surgeons</option>
                    <option value="receptionist" {{ $roleFilter === 'receptionist' ? 'selected' : '' }}>Receptionists</option>
                </select>

                <select name="status" onchange="this.form.submit()"
                    class="text-xs font-semibold px-3 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition text-slate-700">
                    <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>

                <button type="submit"
                    class="px-4 py-2.5 text-xs font-bold rounded-2xl bg-slate-900 text-white hover:bg-slate-800 transition shadow-sm">
                    Filter
                </button>

                @if($roleFilter !== 'all' || $statusFilter !== 'all' || !empty($search))
                    <a href="{{ route('admin.staff.index') }}"
                        class="px-3 py-2.5 text-xs font-semibold rounded-2xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Staff Roster Table -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-4">Staff Member & Contact</th>
                        <th class="px-6 py-4">Role & Permissions</th>
                        <th class="px-6 py-4">Specialization / Notes</th>
                        <th class="px-6 py-4">Account Status</th>
                        <th class="px-6 py-4">Joined Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($staffMembers as $member)
                        @php
                            $isSelf = $member->id === $user->id;
                            $roleBadges = [
                                'clinic_admin' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'dentist' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                'receptionist' => 'bg-amber-50 text-amber-700 border-amber-200',
                            ];
                            $roleNames = [
                                'clinic_admin' => 'Clinic Administrator',
                                'dentist' => 'Dental Surgeon',
                                'receptionist' => 'Frontdesk Receptionist',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition group">
                            <!-- Staff Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-cyan-600 to-teal-500 text-white font-bold text-sm flex items-center justify-center shrink-0 shadow-sm shadow-cyan-600/20">
                                        {{ substr($member->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-slate-900 flex items-center gap-1.5">
                                            <span>{{ $member->name }}</span>
                                            @if($isSelf)
                                                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-cyan-100 text-cyan-800">You</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-500 font-mono">{{ $member->email }}</div>
                                        @if($member->phone)
                                            <div class="text-[11px] text-slate-400 font-medium">{{ $member->phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Role Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-bold border {{ $roleBadges[$member->role] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                    {{ $roleNames[$member->role] ?? ucfirst($member->role) }}
                                </span>
                            </td>

                            <!-- Specialization / Details -->
                            <td class="px-6 py-4 text-xs font-medium text-slate-600 max-w-xs truncate">
                                @if($member->role === 'dentist' && $member->doctorProfile)
                                    <span class="font-bold text-slate-800">{{ $member->doctorProfile->specialization ?? 'General Dentistry' }}</span>
                                    <span class="block text-[11px] text-slate-400 mt-0.5">
                                        Fee: {{ auth()->user()->clinic->currency ?? 'PKR' }} {{ number_format($member->doctorProfile->consultation_fee, 0) }} • {{ $member->doctorProfile->slot_duration_minutes }}m slot
                                    </span>
                                @elseif($member->role === 'clinic_admin')
                                    <span class="text-slate-500">Full System & Billing Access</span>
                                @elseif($member->role === 'receptionist')
                                    <span class="text-slate-500">Front Desk & Billing Records</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <!-- Account Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($member->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                        Inactive / Suspended
                                    </span>
                                @endif
                            </td>

                            <!-- Joined Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500 font-medium">
                                {{ $member->created_at->format('d M Y') }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(!$isSelf)
                                        <!-- Quick Toggle Switch -->
                                        <form method="POST" action="{{ route('admin.staff.toggle', $member) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                title="{{ $member->is_active ? 'Deactivate account' : 'Activate account' }}"
                                                class="p-2 rounded-xl text-xs font-semibold transition {{ $member->is_active ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }}">
                                                @if($member->is_active)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Edit Profile -->
                                    <a href="{{ route('admin.staff.edit', $member) }}"
                                        class="p-2 rounded-xl text-cyan-600 hover:bg-cyan-50 transition"
                                        title="Edit credentials & roles">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    @if(!$isSelf)
                                        <!-- Delete / Archive Form -->
                                        <form method="POST" action="{{ route('admin.staff.destroy', $member) }}" class="inline"
                                            onsubmit="return confirm('Are you sure you want to remove this staff member? If linked to appointments or invoices, the account will be safely deactivated.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 transition"
                                                title="Delete or safely archive staff member">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                No staff members matched your current filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($staffMembers->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $staffMembers->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
