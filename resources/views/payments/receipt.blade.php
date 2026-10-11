<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #REC-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }} - {{ $payment->clinic->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; background: white; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-10 px-4 flex flex-col items-center justify-start text-slate-800">

    <!-- Print Control Bar -->
    <div class="no-print mb-6 flex items-center gap-4">
        <a href="{{ route('invoices.show', $payment->invoice_id) }}"
            class="px-4 py-2 rounded-xl bg-white border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm">
            &larr; Back to Invoice
        </a>

        <button onclick="window.print()"
            class="px-5 py-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition">
            Print Official Receipt
        </button>
    </div>

    <!-- Official Voucher Card -->
    <div class="bg-white rounded-3xl p-8 max-w-lg w-full border border-slate-200/90 shadow-lg space-y-6">
        <!-- Header -->
        <div class="text-center pb-4 border-b border-slate-100">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-cyan-600 to-teal-600 text-white flex items-center justify-center mx-auto mb-2 shadow-sm">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3C4.5 3 3 5.5 3 8c0 4 2.5 7.5 3.5 11 .5 1.5 2 2 3.5 2s2-1 2.5-3.5c.5-2.5 1-2.5 1.5 0 .5 2.5 1 3.5 2.5 3.5s3-.5 3.5-2C21 15.5 21 12 21 8c0-2.5-1.5-5-4-5-2 0-3.5 1.5-5 1.5S9 3 7 3z"/></svg>
            </div>
            <h1 class="text-lg font-black text-slate-900 tracking-tight">
                {{ $payment->clinic->name }}
            </h1>
            <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                Official Payment Receipt Voucher
            </p>
            <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                {{ $payment->clinic->phone ?? 'Clinical Desk' }} &bull; {{ $payment->clinic->address ?? 'Clinic Premises' }}
            </p>
        </div>

        <!-- Receipt Reference Details -->
        <div class="grid grid-cols-2 gap-4 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-100">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Receipt Voucher #</span>
                <span class="font-mono font-bold text-slate-900 text-sm">
                    REC-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                </span>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Date & Time</span>
                <span class="font-mono text-slate-700 text-xs">
                    {{ $payment->payment_date ? $payment->payment_date->format('M d, Y - h:i A') : $payment->created_at->format('M d, Y - h:i A') }}
                </span>
            </div>
        </div>

        <!-- Patient Demographics -->
        <div class="text-xs space-y-1 pb-3 border-b border-slate-100">
            <div class="flex justify-between">
                <span class="text-slate-400 font-medium">Received From:</span>
                <span class="font-bold text-slate-900">{{ $payment->patient->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400 font-medium">Patient Contact:</span>
                <span class="font-mono font-semibold text-slate-700">{{ $payment->patient->phone }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400 font-medium">Invoice Reference:</span>
                <span class="font-mono font-bold text-cyan-700">{{ $payment->invoice->invoice_number }}</span>
            </div>
        </div>

        <!-- Amount Received Box -->
        <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-5 text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 block">
                Amount Received
            </span>
            <div class="text-3xl font-black font-mono text-emerald-700 mt-1">
                Rs. {{ number_format($payment->amount, 0) }}
            </div>
            <span class="text-[11px] text-emerald-800 font-semibold uppercase mt-1 block">
                Method: {{ strtoupper(str_replace('_', ' ', $payment->payment_method)) }}
                @if($payment->transaction_reference)
                    &bull; Ref: {{ $payment->transaction_reference }}
                @endif
            </span>
        </div>

        <!-- Invoice Balance Summary -->
        <div class="text-xs space-y-2 bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
            <div class="flex justify-between text-slate-600">
                <span>Invoice Total:</span>
                <span class="font-mono font-semibold text-slate-800">Rs. {{ number_format($payment->invoice->total_amount, 0) }}</span>
            </div>
            <div class="flex justify-between text-slate-600">
                <span>Total Paid To Date:</span>
                <span class="font-mono font-semibold text-emerald-700">Rs. {{ number_format($payment->invoice->paid_amount, 0) }}</span>
            </div>
            <div class="pt-1.5 border-t border-slate-200 flex justify-between font-bold text-slate-900">
                <span>Remaining Balance Due:</span>
                <span class="font-mono {{ $payment->invoice->balance_amount > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                    Rs. {{ number_format($payment->invoice->balance_amount, 0) }}
                </span>
            </div>
        </div>

        <!-- Signature Lines -->
        <div class="pt-8 flex justify-between items-end text-[11px] text-slate-500">
            <div>
                <span class="text-slate-400 block text-[10px]">Received By:</span>
                <span class="font-semibold text-slate-800">{{ $payment->receivedBy->name ?? 'Front Desk Cashier' }}</span>
            </div>
            <div class="border-t border-slate-300 w-32 text-center pt-1 text-[10px]">
                Authorized Stamp
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center pt-2 text-[10px] text-slate-400">
            Thank you for trusting {{ $payment->clinic->name }}. This is a computer-generated voucher.
        </div>
    </div>
</body>
</html>
