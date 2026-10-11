<x-layouts.app title="Services Catalog & Pricing Engine - DentalCare">
    <!-- Header & Action Bar -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                Services Catalog & Pricing Engine
            </h2>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Standardize dental procedures, categorize clinical specialties, and configure tariffs for booking & invoicing.
            </p>
        </div>

        @if($user->isClinicAdmin())
            <div>
                <a href="{{ route('services.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 hover:bg-slate-800 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition active:scale-[0.99]">
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add New Procedure</span>
                </a>
            </div>
        @endif
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

    <!-- KPI Analytics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- 1. Total Services -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Procedures</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $metrics['total'] }}</span>
                <span class="text-[11px] font-semibold text-slate-500 mt-0.5 block">Across all specialties</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
        </div>

        <!-- 2. Active in Booking -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Active Available</span>
                <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $metrics['active'] }}</span>
                <span class="text-[11px] font-semibold text-emerald-700 mt-0.5 block">Live in patient booking</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- 3. Inactive / Archived -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Archived / Hidden</span>
                <span class="text-2xl font-black text-slate-500 mt-1 block">{{ $metrics['inactive'] }}</span>
                <span class="text-[11px] font-semibold text-slate-400 mt-0.5 block">Hidden from public forms</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
        </div>

        <!-- 4. Average Fee -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Average Tariff</span>
                <span class="text-2xl font-black text-cyan-700 mt-1 block">{{ auth()->user()->clinic->currency ?? 'PKR' }} {{ number_format($metrics['average_fee'], 0) }}</span>
                <span class="text-[11px] font-semibold text-slate-500 mt-0.5 block">Across active procedures</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm mb-6">
        <form method="GET" action="{{ route('services.index') }}" class="flex flex-col gap-4">
            <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 justify-between">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Search procedure name or description..."
                        class="w-full pl-10 pr-4 py-2.5 text-xs font-semibold rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition">
                </div>

                <!-- Status Select -->
                <div class="flex items-center gap-2">
                    <select name="status" onchange="this.form.submit()"
                        class="text-xs font-semibold px-3 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition text-slate-700">
                        <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="inactive" {{ $selectedStatus === 'inactive' ? 'selected' : '' }}>Inactive / Archived Only</option>
                    </select>

                    <button type="submit"
                        class="px-4 py-2.5 text-xs font-bold rounded-2xl bg-slate-900 text-white hover:bg-slate-800 transition shadow-sm">
                        Apply Filter
                    </button>

                    @if($selectedCategory !== 'all' || $selectedStatus !== 'all' || !empty($searchQuery))
                        <a href="{{ route('services.index') }}"
                            class="px-3 py-2.5 text-xs font-semibold rounded-2xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            <!-- Category Pills Bar -->
            <div class="flex flex-wrap items-center gap-1.5 pt-3 border-t border-slate-100">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-2">Specialty:</span>
                
                <!-- All category pill -->
                <a href="{{ route('services.index', array_merge(request()->query(), ['category' => 'all'])) }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $selectedCategory === 'all' ? 'bg-cyan-600 text-white shadow-sm shadow-cyan-600/25' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span>All</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $selectedCategory === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600' }}">
                        {{ $metrics['total'] }}
                    </span>
                </a>

                @foreach($categories as $cat)
                    @php
                        $count = $metrics['breakdown'][$cat] ?? 0;
                        $isSelected = $selectedCategory === $cat;
                    @endphp
                    <a href="{{ route('services.index', array_merge(request()->query(), ['category' => $cat])) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $isSelected ? 'bg-cyan-600 text-white shadow-sm shadow-cyan-600/25' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        <span>{{ $cat }}</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $isSelected ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600' }}">
                            {{ $count }}
                        </span>
                    </a>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Procedures Table / Catalog -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-4">Procedure & Description</th>
                        <th class="px-6 py-4">Category Specialty</th>
                        <th class="px-6 py-4">Est. Duration</th>
                        <th class="px-6 py-4">Standard Fee</th>
                        <th class="px-6 py-4">Availability</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($services as $svc)
                        @php
                            $catColors = [
                                'Preventive' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'Restorative' => 'bg-sky-50 text-sky-700 border-sky-200',
                                'Endodontics' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'Periodontics' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'Prosthodontics' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'Oral Surgery' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'Orthodontics' => 'bg-teal-50 text-teal-700 border-teal-200',
                                'Cosmetic' => 'bg-pink-50 text-pink-700 border-pink-200',
                                'General' => 'bg-slate-100 text-slate-700 border-slate-200',
                            ];
                            $badgeClass = $catColors[$svc->category] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition group">
                            <!-- Procedure Name & Info -->
                            <td class="px-6 py-4.5 max-w-md">
                                <div class="font-extrabold text-slate-900 text-sm leading-snug group-hover:text-cyan-700 transition">
                                    {{ $svc->name }}
                                </div>
                                @if($svc->description)
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                                        {{ $svc->description }}
                                    </p>
                                @endif
                            </td>

                            <!-- Category Badge -->
                            <td class="px-6 py-4.5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-bold border {{ $badgeClass }}">
                                    {{ $svc->category }}
                                </span>
                            </td>

                            <!-- Duration -->
                            <td class="px-6 py-4.5 whitespace-nowrap text-slate-600 font-semibold text-xs">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>{{ $svc->estimated_duration_minutes }} mins</span>
                                </div>
                            </td>

                            <!-- Standard Fee -->
                            <td class="px-6 py-4.5 whitespace-nowrap">
                                <div class="font-black text-slate-900 text-sm">
                                    {{ auth()->user()->clinic->currency ?? 'PKR' }} {{ number_format($svc->standard_fee, 2) }}
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium block">Standard tariff</span>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4.5 whitespace-nowrap">
                                @if($svc->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4.5 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($user->isClinicAdmin())
                                        <!-- Quick Status Toggle -->
                                        <form method="POST" action="{{ route('services.toggle', $svc) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                title="{{ $svc->is_active ? 'Deactivate procedure' : 'Activate procedure' }}"
                                                class="p-2 rounded-xl text-xs font-semibold transition {{ $svc->is_active ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }}">
                                                @if($svc->is_active)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            </button>
                                        </form>

                                        <!-- Edit Button -->
                                        <a href="{{ route('services.edit', $svc) }}"
                                            class="p-2 rounded-xl text-cyan-600 hover:bg-cyan-50 transition"
                                            title="Edit procedure">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        <!-- Delete / Archive Form -->
                                        <form method="POST" action="{{ route('services.destroy', $svc) }}" class="inline"
                                            onsubmit="return confirm('Are you sure you want to remove or deactivate this dental procedure? If linked to historical records, it will be safely deactivated.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 transition"
                                                title="Delete or safely archive procedure">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400 italic">View only</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="max-w-sm mx-auto">
                                    <div class="w-14 h-14 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    </div>
                                    <h4 class="font-bold text-slate-800 text-base">No procedures found</h4>
                                    <p class="text-xs text-slate-500 mt-1 mb-4">
                                        No dental services matched your current category or search filters.
                                    </p>
                                    @if($user->isClinicAdmin())
                                        <a href="{{ route('services.create') }}"
                                            class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 px-4 py-2 text-xs font-bold text-white hover:bg-cyan-700 transition">
                                            + Add Procedure Now
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($services->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
