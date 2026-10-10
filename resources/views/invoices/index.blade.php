<x-layouts.app title="Billing & Invoices - DentalCare">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Billing and Financial Ledger 💳
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Manage dental invoices, payment receipts, discounts, and real-time patient balances.
            </p>
        </div>

        <div>
            <a href="{{ route('invoices.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Issue New Invoice
            </a>
        </div>
    </div>

    <!-- Financial KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <!-- Total Billed -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Total Invoiced</span>
                <span class="text-2xl font-black font-mono text-slate-900 mt-1 block">
                    Rs. {{ number_format($totalInvoiced, 0) }}
                </span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-cyan-100 text-cyan-800 flex items-center justify-center font-bold">
                🧾
            </div>
        </div>

        <!-- Total Collected -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 block">Total Collected</span>
                <span class="text-2xl font-black font-mono text-emerald-700 mt-1 block">
                    Rs. {{ number_format($totalPaid, 0) }}
                </span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                💵
            </div>
        </div>

        <!-- Outstanding Balance -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-rose-500 block">Outstanding Balance</span>
                <span class="text-2xl font-black font-mono {{ $totalOutstanding > 0 ? 'text-rose-600' : 'text-slate-900' }} mt-1 block">
                    Rs. {{ number_format($totalOutstanding, 0) }}
                </span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-800 flex items-center justify-center font-bold">
                ⏳
            </div>
        </div>

        <!-- Pending Invoices Count -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600 block">Pending Invoices</span>
                <span class="text-2xl font-black font-mono text-amber-700 mt-1 block">
                    {{ $pendingCount }}
                </span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold">
                ⚠️
            </div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm mb-6">
        <form action="{{ route('invoices.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <!-- Search Bar -->
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by Invoice #, patient name, phone..."
                    class="w-full rounded-xl border border-slate-300 pl-9 pr-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Status Filter -->
            <select name="status" onchange="this.form.submit()"
                class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                <option value="">All Payment Statuses</option>
                <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="partially_paid" {{ $status === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <!-- Date Range Filters -->
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <span>From:</span>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                    class="rounded-xl border border-slate-300 px-2.5 py-1.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none">
                <span>To:</span>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                    class="rounded-xl border border-slate-300 px-2.5 py-1.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none">
            </div>

            <button type="submit"
                class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition">
                Filter
            </button>

            @if($search || $status || $dateFrom || $dateTo)
                <a href="{{ route('invoices.index') }}" class="text-xs font-semibold text-rose-600 hover:underline px-2">
                    Clear Filters
                </a>
            @endif
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Invoice # & Date</th>
                        <th class="px-6 py-4">Patient Profile</th>
                        <th class="px-6 py-4">Total Amount</th>
                        <th class="px-6 py-4">Paid Amount</th>
                        <th class="px-6 py-4">Balance Due</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Invoice # & Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <a href="{{ route('invoices.show', $inv->id) }}" class="font-mono font-bold text-slate-900 hover:text-cyan-700 block">
                                    {{ $inv->invoice_number }}
                                </a>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    Issued: {{ $inv->created_at->format('M d, Y') }}
                                </div>
                            </td>

                            <!-- Patient -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <a href="{{ route('patients.show', $inv->patient_id) }}" class="font-bold text-slate-900 hover:text-cyan-700 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold text-[10px] shrink-0">
                                        {{ substr($inv->patient->name, 0, 1) }}
                                    </span>
                                    <span>{{ $inv->patient->name }}</span>
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono block pl-8">{{ $inv->patient->phone }}</span>
                            </td>

                            <!-- Total -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-mono font-bold text-slate-900">
                                Rs. {{ number_format($inv->total_amount, 0) }}
                                @if($inv->discount_amount > 0)
                                    <span class="block text-[10px] font-sans text-emerald-600 font-semibold">
                                        (Disc: Rs. {{ number_format($inv->discount_amount, 0) }})
                                    </span>
                                @endif
                            </td>

                            <!-- Paid -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-mono font-semibold text-emerald-700">
                                Rs. {{ number_format($inv->paid_amount, 0) }}
                            </td>

                            <!-- Balance Due -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-mono font-bold {{ $inv->balance_amount > 0 ? 'text-rose-600' : 'text-slate-600' }}">
                                Rs. {{ number_format($inv->balance_amount, 0) }}
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($inv->status === 'paid')
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Paid
                                    </span>
                                @elseif($inv->status === 'partially_paid')
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                        Partial
                                    </span>
                                @elseif($inv->status === 'cancelled')
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500 border border-slate-200">
                                        Cancelled
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                        Unpaid
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('invoices.show', $inv->id) }}"
                                        class="px-2.5 py-1 rounded-lg bg-cyan-50 text-cyan-700 border border-cyan-200 hover:bg-cyan-100 font-bold transition">
                                        View & Ledger
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-xs text-slate-400">
                                No billing invoices found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
