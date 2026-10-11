<x-layouts.app title="Financial Analytics & Reports - DentalCare">
    <!-- Header & Action Bar -->
    <div class="mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                Financial Analytics & Clinic Performance
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Executive revenue telemetry, doctor productivity, collection efficiency, and procedural yield for <strong class="text-slate-800">{{ $clinic->name }}</strong>.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('reports.print', request()->query()) }}" target="_blank"
                class="inline-flex items-center gap-2 rounded-2xl bg-white border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-700 hover:text-slate-900 hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Executive Statement
            </a>
        </div>
    </div>

    <!-- Date Range Filter Toolbar -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/90 shadow-sm mb-8" x-data="reportsFilterForm()">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col gap-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <!-- Preset Pills -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">Period:</span>
                    
                    @php
                        $presets = [
                            'today' => 'Today',
                            'this_week' => 'This Week',
                            'this_month' => 'This Month',
                            'last_month' => 'Last Month',
                            'this_year' => 'This Year',
                            'custom' => 'Custom Range',
                        ];
                    @endphp

                    @foreach($presets as $key => $label)
                        <button type="submit" name="range" value="{{ $key }}"
                            @click="selectedRange = '{{ $key }}'"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $range === $key ? 'bg-cyan-600 text-white shadow-sm shadow-cyan-600/25' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <!-- Active Date Badge -->
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 bg-slate-50 px-3.5 py-1.5 rounded-xl border border-slate-200">
                    <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ $rangeLabel }}</span>
                </div>
            </div>

            <!-- Custom Date Inputs (Displayed when custom is selected) -->
            <div x-show="selectedRange === 'custom'" x-cloak class="pt-4 border-t border-slate-100 flex flex-wrap items-center gap-4">
                <input type="hidden" name="range" value="custom">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">From Date</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                        class="px-3 py-2 text-xs font-semibold rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">To Date</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                        class="px-3 py-2 text-xs font-semibold rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500">
                </div>
                <div class="pt-5">
                    <button type="submit"
                        class="px-4 py-2 text-xs font-bold rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition shadow-sm">
                        Apply Custom Dates
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- 4 Primary Financial KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- 1. Total Invoiced -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Gross Invoiced</span>
                <div class="w-10 h-10 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-black text-slate-900">{{ $clinic->currency }} {{ number_format($totalInvoiced, 2) }}</span>
                <div class="flex items-center justify-between text-[11px] font-semibold text-slate-500 mt-1">
                    <span>{{ $invoicesCount }} invoices generated</span>
                    @if($totalDiscounts > 0)
                        <span class="text-amber-600 font-mono">-{{ number_format($totalDiscounts, 0) }} disc.</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Total Collected Revenue -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Revenue Collected</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-black text-emerald-600">{{ $clinic->currency }} {{ number_format($totalCollected, 2) }}</span>
                <div class="flex items-center justify-between text-[11px] font-semibold text-slate-500 mt-1">
                    <span>Realized counter income</span>
                    <span class="text-emerald-700 font-bold">{{ $paidInvoicesCount }} fully settled</span>
                </div>
            </div>
        </div>

        <!-- 3. Period Receivables -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Period Dues Pending</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-2xl font-black text-amber-600">{{ $clinic->currency }} {{ number_format($periodOutstanding, 2) }}</span>
                <div class="flex items-center justify-between text-[11px] font-semibold text-slate-500 mt-1">
                    <span>{{ $unpaidInvoicesCount + $partialInvoicesCount }} open balances</span>
                    <span class="text-slate-400 font-mono">All-time: {{ number_format($totalClinicReceivables, 0) }}</span>
                </div>
            </div>
        </div>

        <!-- 4. Collection Efficiency Rate -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Collection Rate</span>
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-slate-900">{{ $collectionRate }}%</span>
                    <span class="text-xs font-bold {{ $collectionRate >= 80 ? 'text-emerald-600' : ($collectionRate >= 50 ? 'text-amber-600' : 'text-rose-600') }}">
                        {{ $collectionRate >= 80 ? 'Optimal' : ($collectionRate >= 50 ? 'Moderate' : 'Under target') }}
                    </span>
                </div>
                <!-- Mini Progress Bar -->
                <div class="w-full bg-slate-100 rounded-full h-2 mt-2 overflow-hidden">
                    <div class="h-full rounded-full {{ $collectionRate >= 80 ? 'bg-emerald-500' : ($collectionRate >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}"
                        style="width: {{ min(100, max(0, $collectionRate)) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Row: Invoices Breakdown & Clinical Appointments Yield -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Invoices Lifecycle Status -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Invoicing Settlement Overview
            </h3>
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 block">Fully Paid</span>
                    <span class="text-xl font-black text-emerald-800 mt-1 block">{{ $paidInvoicesCount }}</span>
                    <span class="text-[11px] text-emerald-600 font-medium">Clear balance</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 block">Partially Paid</span>
                    <span class="text-xl font-black text-amber-800 mt-1 block">{{ $partialInvoicesCount }}</span>
                    <span class="text-[11px] text-amber-600 font-medium">Partial deposit</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-700 block">Unpaid</span>
                    <span class="text-xl font-black text-rose-800 mt-1 block">{{ $unpaidInvoicesCount }}</span>
                    <span class="text-[11px] text-rose-600 font-medium">Zero received</span>
                </div>
            </div>
            <div class="text-xs text-slate-500 flex items-center justify-between pt-2 border-t border-slate-100">
                <span>Total invoices billed this period: <strong>{{ $invoicesCount }}</strong></span>
                <a href="{{ route('invoices.index') }}" class="font-bold text-cyan-600 hover:underline">
                    View Invoices Directory →
                </a>
            </div>
        </div>

        <!-- Appointment Volume & Chair Utilization -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Appointment Volumes & Chair Attendance
            </h3>
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">Scheduled</span>
                    <span class="text-xl font-black text-slate-800 mt-1 block">{{ $totalAppointments }}</span>
                    <span class="text-[11px] text-slate-500 font-medium">Slots allocated</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 block">Completed</span>
                    <span class="text-xl font-black text-emerald-800 mt-1 block">{{ $completedAppointments }}</span>
                    <span class="text-[11px] text-emerald-600 font-medium">{{ $appointmentCompletionRate }}% attended</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-700 block">Cancelled / No-Show</span>
                    <span class="text-xl font-black text-rose-800 mt-1 block">{{ $cancelledAppointments }}</span>
                    <span class="text-[11px] text-rose-600 font-medium">Lost chair time</span>
                </div>
            </div>
            <div class="text-xs text-slate-500 flex items-center justify-between pt-2 border-t border-slate-100">
                <span>Chair completion rate: <strong class="text-slate-800">{{ $appointmentCompletionRate }}%</strong></span>
                <a href="{{ route('appointments.index') }}" class="font-bold text-cyan-600 hover:underline">
                    View Appointments Calendar →
                </a>
            </div>
        </div>
    </div>

    <!-- Doctor Clinical Performance & Financial Productivity -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">
                    Doctor Productivity & Revenue Contribution
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Individual performance matrix of consulting dental surgeons in {{ $clinic->name }}.
                </p>
            </div>
            <a href="{{ route('doctors.index') }}" class="text-xs font-bold text-cyan-600 hover:underline">
                Manage Doctors & Roster →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-4">Dental Surgeon</th>
                        <th class="px-6 py-4">Specialization</th>
                        <th class="px-6 py-4 text-center">Appointments</th>
                        <th class="px-6 py-4 text-center">Treatments</th>
                        <th class="px-6 py-4">Billed Amount</th>
                        <th class="px-6 py-4">Revenue Collected</th>
                        <th class="px-6 py-4 text-right">Avg / Treatment</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($doctorPerformance as $dp)
                        @php
                            $avgPerTreatment = $dp['treatments_count'] > 0
                                ? round($dp['collected_amount'] / $dp['treatments_count'], 2)
                                : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Doctor Name -->
                            <td class="px-6 py-4 font-extrabold text-slate-900 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-cyan-100 text-cyan-800 flex items-center justify-center font-bold text-xs">
                                        {{ substr($dp['doctor']->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <span>{{ $dp['doctor']->name }}</span>
                                        <span class="block text-[10px] font-semibold text-slate-400 uppercase">
                                            {{ str_replace('_', ' ', $dp['doctor']->role) }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Specialization -->
                            <td class="px-6 py-4 text-xs font-semibold text-slate-600 whitespace-nowrap">
                                {{ $dp['specialization'] }}
                            </td>

                            <!-- Appointments -->
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="font-bold text-slate-800">{{ $dp['completed_appointments_count'] }}</span>
                                <span class="text-xs text-slate-400">/ {{ $dp['appointments_count'] }}</span>
                            </td>

                            <!-- Treatments Rendered -->
                            <td class="px-6 py-4 text-center font-extrabold text-cyan-700 whitespace-nowrap">
                                {{ $dp['treatments_count'] }}
                            </td>

                            <!-- Billed -->
                            <td class="px-6 py-4 font-mono font-bold text-slate-700 whitespace-nowrap">
                                {{ $clinic->currency }} {{ number_format($dp['invoiced_amount'], 2) }}
                            </td>

                            <!-- Collected -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono font-extrabold text-emerald-700 text-sm">
                                    {{ $clinic->currency }} {{ number_format($dp['collected_amount'], 2) }}
                                </span>
                            </td>

                            <!-- Avg per treatment -->
                            <td class="px-6 py-4 text-right font-mono text-xs font-semibold text-slate-500 whitespace-nowrap">
                                {{ $clinic->currency }} {{ number_format($avgPerTreatment, 0) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-xs text-slate-400">
                                No doctor clinical activity logged for this time period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2-Column: Payment Methods & Specialty Categories -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Payment Methods Distribution -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 mb-1 flex items-center gap-2">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Collections by Payment Method
            </h3>
            <p class="text-xs text-slate-400 mb-4">Cash counter, debit card, bank transfer, and online receipts.</p>

            <div class="space-y-3.5">
                @forelse($paymentsByMethod as $pm)
                    @php
                        $percentage = $totalCollected > 0 ? round(($pm->total_amount / $totalCollected) * 100, 1) : 0;
                        $methodNames = [
                            'cash' => 'Cash Counter',
                            'card' => 'Debit / Credit Card POS',
                            'bank_transfer' => 'Direct Bank Transfer',
                            'online' => 'Online Payment Gateway',
                            'cheque' => 'Bank Cheque',
                        ];
                        $label = $methodNames[$pm->payment_method] ?? ucfirst(str_replace('_', ' ', $pm->payment_method));
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span class="text-slate-800 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-cyan-600"></span>
                                {{ $label }} ({{ $pm->count }} txns)
                            </span>
                            <span class="text-slate-900 font-mono">
                                {{ $clinic->currency }} {{ number_format($pm->total_amount, 2) }}
                                <span class="text-slate-400 font-normal">({{ $percentage }}%)</span>
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="h-full rounded-full bg-cyan-600" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">
                        No payments received during this period.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Revenue by Dental Specialty Category -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 mb-1 flex items-center gap-2">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Revenue by Specialty Category
            </h3>
            <p class="text-xs text-slate-400 mb-4">Procedural turnover segmented across clinical specialties.</p>

            <div class="space-y-3.5">
                @forelse($categoryBreakdown as $cb)
                    @php
                        $percentage = $totalInvoiced > 0 ? round(($cb->total_revenue / $totalInvoiced) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span class="text-slate-800 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                                {{ $cb->category_name }} ({{ $cb->total_count }} procedures)
                            </span>
                            <span class="text-slate-900 font-mono">
                                {{ $clinic->currency }} {{ number_format($cb->total_revenue, 2) }}
                                <span class="text-slate-400 font-normal">({{ $percentage }}%)</span>
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="h-full rounded-full bg-teal-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">
                        No procedure billing recorded for this period.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top Billed Dental Procedures -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="text-base font-extrabold text-slate-900">
                Top Billed Dental Procedures
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Highest turnover procedures rendered during the selected reporting timeframe.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-4">Procedure Description</th>
                        <th class="px-6 py-4">Specialty Category</th>
                        <th class="px-6 py-4 text-center">Times Billed</th>
                        <th class="px-6 py-4 text-right">Total Billed Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($topProcedures as $tp)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $tp->procedure_name }}
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-cyan-700">
                                <span class="px-2.5 py-0.5 rounded-full bg-cyan-50 border border-cyan-200 font-bold">
                                    {{ $tp->category_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-700">
                                {{ $tp->total_count }}
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-extrabold text-slate-900">
                                {{ $clinic->currency }} {{ number_format($tp->total_revenue, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-xs text-slate-400">
                                No procedure items recorded in this timeframe.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Payment Transactions Ledger in Period -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">
                    Payment Transactions Audit Ledger
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Latest verified collections and receipts during {{ $rangeLabel }}.
                </p>
            </div>
            <a href="{{ route('invoices.index') }}" class="text-xs font-bold text-cyan-600 hover:underline">
                View All Invoices →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-4">Date & Time</th>
                        <th class="px-6 py-4">Patient Name</th>
                        <th class="px-6 py-4">Invoice #</th>
                        <th class="px-6 py-4">Method</th>
                        <th class="px-6 py-4">Received By</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4 text-right">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($recentTransactions as $txn)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-slate-600">
                                {{ $txn->payment_date ? $txn->payment_date->format('d M Y, h:i A') : $txn->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900 whitespace-nowrap">
                                {{ $txn->patient->name ?? 'Walk-in Patient' }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs font-bold text-cyan-700 whitespace-nowrap">
                                {{ $txn->invoice->invoice_number ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ str_replace('_', ' ', $txn->payment_method) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-600 whitespace-nowrap">
                                {{ $txn->receivedBy->name ?? 'Clinic Staff' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-extrabold text-emerald-700 whitespace-nowrap">
                                {{ $clinic->currency }} {{ number_format($txn->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('payments.receipt', $txn) }}" target="_blank"
                                    class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold text-cyan-700 bg-cyan-50 hover:bg-cyan-100 rounded-xl transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-xs text-slate-400">
                                No payment transactions recorded for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alpine Form Script -->
    <script>
        function reportsFilterForm() {
            return {
                selectedRange: @js($range),
            };
        }
    </script>
</x-layouts.app>
