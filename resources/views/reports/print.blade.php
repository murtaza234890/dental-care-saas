<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Performance Statement - {{ $clinic->name }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-shadow-none { box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased p-4 md:p-8">
    <!-- Non-print Action Toolbar -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('reports.index', request()->query()) }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm">
            ← Back to Analytics
        </a>
        <button onclick="window.print()"
            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold uppercase tracking-wider transition shadow-md shadow-cyan-600/25">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Statement
        </button>
    </div>

    <!-- Printable Statement Document -->
    <div class="max-w-4xl mx-auto bg-white rounded-3xl p-8 sm:p-12 border border-slate-200/90 shadow-sm print-shadow-none">
        <!-- Clinic Letterhead Header -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between pb-8 border-b-2 border-slate-900 gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $clinic->name }}</h1>
                <p class="text-xs text-slate-500 mt-1 max-w-sm leading-relaxed">
                    {{ $clinic->address ?? 'Main Practice Center' }}
                </p>
                <div class="mt-2 text-xs text-slate-600 flex flex-wrap items-center gap-4">
                    @if($clinic->phone)
                        <span><strong>Tel:</strong> {{ $clinic->phone }}</span>
                    @endif
                    @if($clinic->email)
                        <span><strong>Email:</strong> {{ $clinic->email }}</span>
                    @endif
                    <span><strong>Currency:</strong> {{ $clinic->currency }}</span>
                </div>
            </div>

            <div class="sm:text-right">
                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-900 text-white mb-2">
                    Official Executive Statement
                </span>
                <h2 class="text-lg font-extrabold text-slate-800">Financial Performance Audit</h2>
                <p class="text-xs text-slate-500 font-mono mt-0.5">Period: {{ $rangeLabel }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Generated: {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}</p>
            </div>
        </div>

        <!-- Executive Summary Financial KPIs -->
        <div class="my-8">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-4">
                1. Executive Financial & Productivity Summary
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-200">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Gross Invoiced</span>
                    <span class="text-lg font-black text-slate-900 mt-0.5 block font-mono">
                        {{ $clinic->currency }} {{ number_format($totalInvoiced, 2) }}
                    </span>
                    <span class="text-[10px] text-slate-500">{{ $invoicesCount }} invoices generated</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Realized Collections</span>
                    <span class="text-lg font-black text-emerald-700 mt-0.5 block font-mono">
                        {{ $clinic->currency }} {{ number_format($totalCollected, 2) }}
                    </span>
                    <span class="text-[10px] text-emerald-600 font-semibold">{{ $collectionRate }}% collection rate</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Period Receivables</span>
                    <span class="text-lg font-black text-amber-700 mt-0.5 block font-mono">
                        {{ $clinic->currency }} {{ number_format($periodOutstanding, 2) }}
                    </span>
                    <span class="text-[10px] text-slate-500">Unsettled period balance</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Appointments Attended</span>
                    <span class="text-lg font-black text-cyan-800 mt-0.5 block font-mono">
                        {{ $completedAppointments }} / {{ $totalAppointments }}
                    </span>
                    <span class="text-[10px] text-cyan-700 font-semibold">{{ $appointmentCompletionRate }}% attendance</span>
                </div>
            </div>
        </div>

        <!-- Doctor Performance Breakdown Table -->
        <div class="mb-8">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3">
                2. Doctor Productivity & Financial Attribution
            </h3>
            <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                <thead class="bg-slate-100 text-[10px] font-black uppercase text-slate-500">
                    <tr>
                        <th class="p-3">Dental Surgeon</th>
                        <th class="p-3">Specialty</th>
                        <th class="p-3 text-center">Appts (Done/Total)</th>
                        <th class="p-3 text-center">Treatments</th>
                        <th class="p-3 text-right">Gross Billed</th>
                        <th class="p-3 text-right">Collections</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($doctorPerformance as $dp)
                        <tr>
                            <td class="p-3 font-bold text-slate-900">{{ $dp['doctor']->name }}</td>
                            <td class="p-3 text-slate-600">{{ $dp['specialization'] }}</td>
                            <td class="p-3 text-center">{{ $dp['completed_appointments_count'] }} / {{ $dp['appointments_count'] }}</td>
                            <td class="p-3 text-center font-bold text-cyan-700">{{ $dp['treatments_count'] }}</td>
                            <td class="p-3 text-right font-mono">{{ $clinic->currency }} {{ number_format($dp['invoiced_amount'], 2) }}</td>
                            <td class="p-3 text-right font-mono font-bold text-emerald-700">{{ $clinic->currency }} {{ number_format($dp['collected_amount'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-slate-400 italic">No doctor activity recorded for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 2-Column: Collections by Method & Specialty Categories -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
            <!-- Payment Methods -->
            <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3">
                    3. Payment Collections by Channel
                </h3>
                <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                    <thead class="bg-slate-100 text-[10px] font-black uppercase text-slate-500">
                        <tr>
                            <th class="p-3">Payment Channel</th>
                            <th class="p-3 text-center">Txns</th>
                            <th class="p-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($paymentsByMethod as $pm)
                            <tr>
                                <td class="p-3 font-semibold capitalize">{{ str_replace('_', ' ', $pm->payment_method) }}</td>
                                <td class="p-3 text-center text-slate-500">{{ $pm->count }}</td>
                                <td class="p-3 text-right font-mono font-bold">{{ $clinic->currency }} {{ number_format($pm->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-3 text-center text-slate-400 italic">No payments logged.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Revenue by Specialty -->
            <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3">
                    4. Procedural Turnover by Specialty
                </h3>
                <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                    <thead class="bg-slate-100 text-[10px] font-black uppercase text-slate-500">
                        <tr>
                            <th class="p-3">Specialty Category</th>
                            <th class="p-3 text-center">Count</th>
                            <th class="p-3 text-right">Turnover</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($categoryBreakdown as $cb)
                            <tr>
                                <td class="p-3 font-semibold">{{ $cb->category_name }}</td>
                                <td class="p-3 text-center text-slate-500">{{ $cb->total_count }}</td>
                                <td class="p-3 text-right font-mono font-bold">{{ $clinic->currency }} {{ number_format($cb->total_revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-3 text-center text-slate-400 italic">No billings recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Verification Sign-off & Audit Stamp -->
        <div class="pt-8 border-t border-slate-200 mt-12 grid grid-cols-2 gap-8 text-xs text-slate-600">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-8">Prepared By:</span>
                <div class="border-b border-slate-400 pb-1 max-w-xs font-bold text-slate-900">
                    {{ $user->name }} ({{ str_replace('_', ' ', $user->role) }})
                </div>
                <span class="text-[10px] text-slate-400">Authorized Clinic Administrator</span>
            </div>

            <div class="text-right">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-8">Clinical & Audit Stamp:</span>
                <div class="border-b border-slate-400 pb-1 inline-block min-w-[180px] font-mono text-[11px] text-slate-500">
                    Clinic Stamp & Signature
                </div>
                <span class="text-[10px] text-slate-400 block mt-1">Generated by DentalCare Cloud SaaS</span>
            </div>
        </div>
    </div>
</body>
</html>
