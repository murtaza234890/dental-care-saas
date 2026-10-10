<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DentalService;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Display the clinic billing and invoicing directory with financial metrics.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $search = $request->input('search');
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Scoped automatically to current clinic via BelongsToClinic trait
        $query = Invoice::with(['patient', 'appointment', 'payments']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->latest()->paginate(15)->withQueryString();

        // Clinic Financial KPIs
        $totalInvoiced = (float) Invoice::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalPaid = (float) Invoice::where('status', '!=', 'cancelled')->sum('paid_amount');
        $totalOutstanding = (float) Invoice::where('status', '!=', 'cancelled')->sum('balance_amount');
        $pendingCount = Invoice::whereIn('status', ['unpaid', 'partially_paid'])->count();

        return view('invoices.index', compact(
            'invoices',
            'search',
            'status',
            'dateFrom',
            'dateTo',
            'totalInvoiced',
            'totalPaid',
            'totalOutstanding',
            'pendingCount'
        ));
    }

    /**
     * Show form to generate a new patient invoice.
     */
    public function create(Request $request): View
    {
        $user = Auth::user();
        $patientId = $request->input('patient_id');
        $appointmentId = $request->input('appointment_id');
        $selectedAppointment = null;

        if ($appointmentId) {
            $selectedAppointment = Appointment::with(['patient', 'service'])->find($appointmentId);
            if ($selectedAppointment) {
                $patientId = $selectedAppointment->patient_id;
            }
        }

        $patients = Patient::where('clinic_id', $user->clinic_id)
            ->orderBy('name')
            ->get();

        $services = DentalService::where('clinic_id', $user->clinic_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('invoices.create', compact(
            'patients',
            'services',
            'patientId',
            'appointmentId',
            'selectedAppointment'
        ));
    }

    /**
     * Store a newly created invoice with dynamic line items and optional upfront payment.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'due_date' => ['nullable', 'date'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => ['nullable', 'exists:dental_services,id'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            // Optional upfront payment collection
            'initial_payment' => ['nullable', 'boolean'],
            'initial_payment_amount' => ['nullable', 'required_if:initial_payment,1', 'numeric', 'min:1'],
            'initial_payment_method' => ['nullable', 'required_if:initial_payment,1', 'in:cash,card,bank_transfer,easypaisa_jazzcash'],
            'initial_payment_reference' => ['nullable', 'string', 'max:100'],
        ]);

        $invoice = DB::transaction(function () use ($user, $validated) {
            $count = Invoice::where('clinic_id', $user->clinic_id)->count() + 1;
            $invoiceNumber = 'INV-' . Carbon::now()->format('Ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'clinic_id' => $user->clinic_id,
                'patient_id' => $validated['patient_id'],
                'appointment_id' => $validated['appointment_id'] ?? null,
                'invoice_number' => $invoiceNumber,
                'total_amount' => 0.00,
                'discount_amount' => $validated['discount_amount'] ?? 0.00,
                'paid_amount' => 0.00,
                'balance_amount' => 0.00,
                'status' => 'unpaid',
                'due_date' => $validated['due_date'] ?? Carbon::today(),
            ]);

            foreach ($validated['items'] as $item) {
                $unitPrice = (float) $item['unit_price'];
                $quantity = (int) $item['quantity'];
                $totalPrice = $unitPrice * $quantity;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['service_id'] ?? null,
                    'description' => $item['description'],
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice,
                ]);
            }

            // Record initial payment if submitted upfront
            if (!empty($validated['initial_payment']) && !empty($validated['initial_payment_amount'])) {
                Payment::create([
                    'clinic_id' => $user->clinic_id,
                    'invoice_id' => $invoice->id,
                    'patient_id' => $validated['patient_id'],
                    'received_by_user_id' => $user->id,
                    'amount' => $validated['initial_payment_amount'],
                    'payment_method' => $validated['initial_payment_method'] ?? 'cash',
                    'transaction_reference' => $validated['initial_payment_reference'] ?? null,
                    'payment_date' => Carbon::now(),
                    'notes' => 'Upfront payment collected at invoice issuance',
                ]);
            }

            $invoice->recalculateFinancials();

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice->id)
            ->with('status', "Invoice {$invoice->invoice_number} created successfully.");
    }

    /**
     * Display a comprehensive, printable clinic invoice and payment ledger.
     */
    public function show(Invoice $invoice): View
    {
        $user = Auth::user();

        $invoice->load([
            'patient',
            'appointment.doctor',
            'items.service',
            'payments.receivedBy',
            'clinic',
        ]);

        return view('invoices.show', compact('invoice', 'user'));
    }

    /**
     * Cancel / void an invoice if permitted.
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isReceptionist()) {
            abort(403, 'Unauthorized. Only Clinic Administrators can cancel or void invoices.');
        }

        $invoice->update(['status' => 'cancelled']);

        return redirect()->route('invoices.index')
            ->with('status', "Invoice {$invoice->invoice_number} has been cancelled.");
    }
}
