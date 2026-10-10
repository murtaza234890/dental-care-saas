<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Record a payment transaction towards an invoice.
     */
    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        $user = Auth::user();

        // Enforce invoice status check
        if ($invoice->status === 'cancelled') {
            return back()->withErrors(['amount' => 'Cannot record payments on a cancelled invoice.']);
        }

        if ($invoice->balance_amount <= 0) {
            return back()->withErrors(['amount' => 'This invoice has already been fully paid.']);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:' . $invoice->balance_amount],
            'payment_method' => ['required', 'in:cash,card,bank_transfer,easypaisa_jazzcash'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'amount.max' => 'The payment amount cannot exceed the remaining balance of Rs. ' . number_format($invoice->balance_amount, 0) . '.',
        ]);

        $payment = Payment::create([
            'clinic_id' => $user->clinic_id,
            'invoice_id' => $invoice->id,
            'patient_id' => $invoice->patient_id,
            'received_by_user_id' => $user->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'transaction_reference' => $validated['transaction_reference'] ?? null,
            'payment_date' => Carbon::now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        $invoice->recalculateFinancials();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('status', 'Payment of Rs. ' . number_format($payment->amount, 0) . ' recorded successfully. New balance: Rs. ' . number_format($invoice->balance_amount, 0) . '.');
    }

    /**
     * Display a clean, printable payment voucher receipt.
     */
    public function receipt(Payment $payment): View
    {
        $payment->load([
            'invoice.items',
            'patient',
            'receivedBy',
            'clinic',
        ]);

        return view('payments.receipt', compact('payment'));
    }
}
