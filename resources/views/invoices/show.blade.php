<x-layouts.app title="Invoice {{ $invoice->invoice_number }} - DentalCare">
    <div class="max-w-4xl mx-auto space-y-6" x-data="{ showPaymentModal: false }">
        <!-- Top Action Bar (Hidden in Print) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
            <div>
                <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-600 hover:text-cyan-700 transition mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Invoices Directory
                </a>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 font-mono">
                        {{ $invoice->invoice_number }}
                    </h2>
                    @if($invoice->status === 'paid')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Paid &bull; Settled
                        </span>
                    @elseif($invoice->status === 'partially_paid')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-amber-100 text-amber-800 border border-amber-200">
                            Partially Paid
                        </span>
                    @elseif($invoice->status === 'cancelled')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-slate-200 text-slate-700 border border-slate-300">
                            Cancelled / Void
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-rose-100 text-rose-800 border border-rose-200">
                            Unpaid
                        </span>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print Invoice
                </button>

                @if($invoice->balance_amount > 0 && $invoice->status !== 'cancelled')
                    <button type="button" @click="showPaymentModal = true"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-emerald-600/25 hover:from-emerald-700 hover:to-teal-700 transition active:scale-[0.99]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Record Payment
                    </button>
                @endif
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-xs font-semibold text-emerald-800 print:hidden flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs font-semibold text-rose-800 print:hidden">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Printable Standard Clinic Invoice Card -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-sm print:shadow-none print:border-none print:p-0 space-y-8">
            <!-- Header: Clinic Branding & Invoice Metadata -->
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6 pb-6 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-600 to-teal-600 text-white flex items-center justify-center font-extrabold text-2xl shadow-md shrink-0">
                        🦷
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">
                            {{ $invoice->clinic->name }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Comprehensive Dental Care & Oral Surgery
                        </p>
                        <p class="text-[11px] text-slate-400 font-mono mt-0.5">
                            {{ $invoice->clinic->phone ?? 'Clinical Front Desk' }} &bull; {{ $invoice->clinic->email ?? '' }}
                        </p>
                    </div>
                </div>

                <div class="text-left sm:text-right">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Statement Invoice</div>
                    <div class="text-xl font-black font-mono text-slate-900 mt-0.5">{{ $invoice->invoice_number }}</div>
                    <div class="text-xs text-slate-500 mt-1">Date: <span class="font-semibold">{{ $invoice->created_at->format('M d, Y') }}</span></div>
                    <div class="text-xs text-slate-500">Due: <span class="font-semibold">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'Due on receipt' }}</span></div>
                </div>
            </div>

            <!-- Billed Patient Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50/70 p-5 rounded-2xl border border-slate-100">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Billed To (Patient):</span>
                    <a href="{{ route('patients.show', $invoice->patient_id) }}" class="text-sm font-extrabold text-slate-900 hover:text-cyan-700 block">
                        {{ $invoice->patient->name }}
                    </a>
                    <span class="text-xs text-slate-500 font-mono block mt-0.5">{{ $invoice->patient->phone }}</span>
                    @if($invoice->patient->address)
                        <span class="text-xs text-slate-500 block mt-0.5">{{ $invoice->patient->address }}</span>
                    @endif
                </div>

                <div class="sm:text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Encounter Info:</span>
                    <span class="text-xs text-slate-700 block font-medium">Patient File: <span class="font-mono font-bold text-slate-900">#PT-{{ str_pad($invoice->patient->id, 5, '0', STR_PAD_LEFT) }}</span></span>
                    @if($invoice->appointment)
                        <span class="text-xs text-slate-700 block mt-0.5 font-medium">
                            Attending: <span class="font-bold text-slate-900">{{ $invoice->appointment->doctor->name }}</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Itemized Services Table -->
            <div>
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100/80 text-[10px] uppercase font-bold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">Procedure / Description</th>
                            <th class="py-3 px-4 text-center">Qty</th>
                            <th class="py-3 px-4 text-right">Unit Price</th>
                            <th class="py-3 px-4 text-right">Total Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($invoice->items as $idx => $item)
                            <tr>
                                <td class="py-3.5 px-4 font-mono text-slate-400">{{ $idx + 1 }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-900">
                                    {{ $item->description }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-slate-700">{{ $item->quantity }}</td>
                                <td class="py-3.5 px-4 text-right font-mono text-slate-700">Rs. {{ number_format($item->unit_price, 0) }}</td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">Rs. {{ number_format($item->total_price, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Financial Ledger Breakdown -->
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6 pt-4 border-t border-slate-100">
                <div class="text-xs text-slate-500 max-w-sm">
                    <span class="font-bold text-slate-700 block mb-1">Clinical Payment Terms:</span>
                    Payments are due on date of treatment. We accept Cash, Debit/Credit cards, and Raast instant transfers. All clinical receipts are verified electronically.
                </div>

                <div class="w-full sm:w-72 space-y-2 text-xs">
                    @php
                        $subtotal = $invoice->items->sum('total_price');
                    @endphp
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal:</span>
                        <span class="font-mono font-bold text-slate-900">Rs. {{ number_format($subtotal, 0) }}</span>
                    </div>

                    @if($invoice->discount_amount > 0)
                        <div class="flex justify-between text-emerald-700">
                            <span>Discount:</span>
                            <span class="font-mono font-bold">- Rs. {{ number_format($invoice->discount_amount, 0) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-slate-900 font-extrabold text-sm pt-2 border-t border-slate-200">
                        <span>Grand Total:</span>
                        <span class="font-mono font-black text-slate-900">Rs. {{ number_format($invoice->total_amount, 0) }}</span>
                    </div>

                    <div class="flex justify-between text-emerald-700 font-semibold">
                        <span>Total Paid:</span>
                        <span class="font-mono font-bold">Rs. {{ number_format($invoice->paid_amount, 0) }}</span>
                    </div>

                    <div class="flex justify-between font-black text-sm pt-2 border-t border-slate-200 {{ $invoice->balance_amount > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                        <span>Balance Due:</span>
                        <span class="font-mono text-base">Rs. {{ number_format($invoice->balance_amount, 0) }}</span>
                    </div>
                </div>
            </div>

            <!-- Recorded Payment Transactions Table -->
            <div class="pt-6 border-t border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">
                        Payment Transactions Ledger
                    </h4>
                    <span class="text-[11px] text-slate-400">{{ $invoice->payments->count() }} transaction(s) logged</span>
                </div>

                @if($invoice->payments->isNotEmpty())
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="py-2.5 px-3">Date & Time</th>
                                <th class="py-2.5 px-3">Method</th>
                                <th class="py-2.5 px-3">Txn Reference</th>
                                <th class="py-2.5 px-3">Received By</th>
                                <th class="py-2.5 px-3 text-right">Amount</th>
                                <th class="py-2.5 px-3 text-right print:hidden">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($invoice->payments as $pmt)
                                <tr>
                                    <td class="py-2.5 px-3 font-mono text-slate-600">
                                        {{ $pmt->payment_date ? $pmt->payment_date->format('M d, Y - h:i A') : $pmt->created_at->format('M d, Y - h:i A') }}
                                    </td>
                                    <td class="py-2.5 px-3 uppercase font-bold text-slate-700 text-[11px]">
                                        {{ str_replace('_', ' ', $pmt->payment_method) }}
                                    </td>
                                    <td class="py-2.5 px-3 font-mono text-slate-500">
                                        {{ $pmt->transaction_reference ?: 'Direct Counter Cash' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-slate-700">
                                        {{ $pmt->receivedBy->name ?? 'Front Desk Staff' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-mono font-bold text-emerald-700">
                                        Rs. {{ number_format($pmt->amount, 0) }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right print:hidden">
                                        <a href="{{ route('payments.receipt', $pmt->id) }}" target="_blank"
                                            class="text-cyan-600 hover:text-cyan-800 font-bold text-[11px]">
                                            Print Slip &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center text-xs text-slate-400">
                        No payments recorded yet for this invoice.
                    </div>
                @endif
            </div>

            <!-- Authorized Signatures (Visible on Print) -->
            <div class="hidden print:flex justify-between pt-16 text-xs text-slate-500">
                <div class="border-t border-slate-300 w-48 text-center pt-2">
                    Patient Signature
                </div>
                <div class="border-t border-slate-300 w-48 text-center pt-2">
                    Authorized Clinic Stamp
                </div>
            </div>
        </div>

        <!-- Record Payment Modal (Triggered by Alpine.js) -->
        <div x-show="showPaymentModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100"
                @click.away="showPaymentModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-extrabold text-slate-900">
                        Record Payment Transaction
                    </h3>
                    <button type="button" @click="showPaymentModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">
                        &times;
                    </button>
                </div>

                <form action="{{ route('invoices.payments.store', $invoice->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <span class="text-xs text-slate-500 block mb-1">Remaining Outstanding Balance:</span>
                        <div class="text-2xl font-black font-mono text-rose-600">
                            Rs. {{ number_format($invoice->balance_amount, 0) }}
                        </div>
                    </div>

                    <div>
                        <label for="amount" class="block text-xs font-semibold text-slate-700 mb-1">
                            Payment Amount (Rs.) *
                        </label>
                        <input type="number" step="0.01" min="1" max="{{ $invoice->balance_amount }}" id="amount" name="amount"
                            value="{{ old('amount', $invoice->balance_amount) }}" required
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 font-mono focus:border-emerald-600 focus:outline-none">
                    </div>

                    <div>
                        <label for="payment_method" class="block text-xs font-semibold text-slate-700 mb-1">
                            Payment Method *
                        </label>
                        <select id="payment_method" name="payment_method" required
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-emerald-600 focus:outline-none">
                            <option value="cash">Counter Cash</option>
                            <option value="card">POS Debit / Credit Card</option>
                            <option value="bank_transfer">Online Bank Transfer (Raast / IBFT)</option>
                            <option value="easypaisa_jazzcash">EasyPaisa / JazzCash</option>
                        </select>
                    </div>

                    <div>
                        <label for="transaction_reference" class="block text-xs font-semibold text-slate-700 mb-1">
                            Transaction Reference <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <input type="text" id="transaction_reference" name="transaction_reference"
                            placeholder="e.g. Card Auth Code or Raast Ref"
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 font-mono focus:border-emerald-600 focus:outline-none">
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1">
                            Notes / Memo <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <textarea id="notes" name="notes" rows="2"
                            placeholder="e.g. Partial advance payment for RCT..."
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-emerald-600 focus:outline-none"></textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="showPaymentModal = false"
                            class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-emerald-600/25 hover:from-emerald-700 hover:to-teal-700 transition">
                            Confirm & Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
