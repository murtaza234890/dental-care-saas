<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DentalService;
use App\Models\FollowUp;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\TreatmentRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TreatmentRecordController extends Controller
{
    /**
     * Display the clinical treatments directory with filters and metrics.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $search = $request->input('search');
        $doctorId = $request->input('doctor_id');
        $serviceId = $request->input('service_id');
        $tooth = $request->input('tooth');

        // Scoped automatically to current clinic via BelongsToClinic trait
        $query = TreatmentRecord::with(['patient', 'doctor.doctorProfile', 'service', 'appointment']);

        // If logged-in user is a dentist, allow viewing all clinic treatments or their own
        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        if ($tooth) {
            $query->where('tooth_numbers', 'like', "%{$tooth}%");
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('diagnosis', 'like', "%{$search}%")
                    ->orWhere('treatment_rendered', 'like', "%{$search}%")
                    ->orWhere('tooth_numbers', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $treatments = $query->latest()->paginate(15)->withQueryString();

        // Metrics for summary cards
        $totalTreatments = TreatmentRecord::count();
        $uniquePatientsTreated = TreatmentRecord::distinct('patient_id')->count('patient_id');
        $treatmentsThisMonth = TreatmentRecord::where('created_at', '>=', Carbon::now()->startOfMonth())->count();

        // Available doctors and services for filter dropdowns
        $doctors = User::where('clinic_id', $user->clinic_id)
            ->whereIn('role', ['dentist', 'clinic_admin'])
            ->with('doctorProfile')
            ->get();

        $services = DentalService::where('clinic_id', $user->clinic_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('treatments.index', compact(
            'treatments',
            'search',
            'doctorId',
            'serviceId',
            'tooth',
            'totalTreatments',
            'uniquePatientsTreated',
            'treatmentsThisMonth',
            'doctors',
            'services'
        ));
    }

    /**
     * Show form to document a new clinical dental procedure.
     */
    public function create(Request $request): View
    {
        $user = Auth::user();

        // Role authorization: Receptionists cannot document clinical procedures
        if ($user->isReceptionist()) {
            abort(403, 'Unauthorized. Only Dentists and Clinic Administrators can record clinical treatments.');
        }

        $patientId = $request->input('patient_id');
        $appointmentId = $request->input('appointment_id');
        $selectedAppointment = null;

        if ($appointmentId) {
            $selectedAppointment = Appointment::with(['patient', 'doctor', 'service'])->find($appointmentId);
            if ($selectedAppointment) {
                $patientId = $selectedAppointment->patient_id;
            }
        }

        $patients = Patient::where('clinic_id', $user->clinic_id)
            ->orderBy('name')
            ->get();

        $doctors = User::where('clinic_id', $user->clinic_id)
            ->whereIn('role', ['dentist', 'clinic_admin'])
            ->with('doctorProfile')
            ->get();

        $services = DentalService::where('clinic_id', $user->clinic_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedTeeth = old('tooth_numbers')
            ? array_values(array_filter(array_map('trim', explode(',', old('tooth_numbers')))))
            : [];

        return view('treatments.create', compact(
            'patients',
            'doctors',
            'services',
            'patientId',
            'appointmentId',
            'selectedAppointment',
            'selectedTeeth'
        ));
    }

    /**
     * Store a new treatment record, with optional appointment completion, follow-up, and invoicing.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isReceptionist()) {
            abort(403, 'Unauthorized. Only Dentists and Clinic Administrators can record clinical treatments.');
        }

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['required', 'exists:users,id'],
            'service_id' => ['nullable', 'exists:dental_services,id'],
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'tooth_numbers' => ['nullable', 'string', 'max:100'],
            'diagnosis' => ['required', 'string', 'max:1000'],
            'treatment_rendered' => ['required', 'string', 'max:1000'],
            'clinical_notes' => ['nullable', 'string', 'max:2000'],
            'prescription' => ['nullable', 'string', 'max:1000'],
            // Optional workflows
            'complete_appointment' => ['nullable', 'boolean'],
            'schedule_followup' => ['nullable', 'boolean'],
            'followup_due_date' => ['nullable', 'required_if:schedule_followup,1', 'date', 'after_or_equal:today'],
            'followup_reason' => ['nullable', 'required_if:schedule_followup,1', 'string', 'max:255'],
            'create_invoice' => ['nullable', 'boolean'],
        ]);

        // 1. Create Treatment Record
        $treatment = TreatmentRecord::create([
            'clinic_id' => $user->clinic_id,
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'service_id' => $validated['service_id'] ?? null,
            'appointment_id' => $validated['appointment_id'] ?? null,
            'tooth_numbers' => $validated['tooth_numbers'] ?? null,
            'diagnosis' => $validated['diagnosis'],
            'treatment_rendered' => $validated['treatment_rendered'],
            'clinical_notes' => $validated['clinical_notes'] ?? null,
            'prescription' => $validated['prescription'] ?? null,
        ]);

        // 2. Mark appointment as completed if toggled
        if (!empty($validated['complete_appointment']) && !empty($validated['appointment_id'])) {
            $appointment = Appointment::find($validated['appointment_id']);
            if ($appointment) {
                $appointment->update(['status' => 'completed']);
            }
        }

        // 3. Create Follow-Up / Recall if toggled
        if (!empty($validated['schedule_followup']) && !empty($validated['followup_due_date'])) {
            FollowUp::create([
                'clinic_id' => $user->clinic_id,
                'patient_id' => $validated['patient_id'],
                'treatment_record_id' => $treatment->id,
                'due_date' => $validated['followup_due_date'],
                'reason' => $validated['followup_reason'] ?? 'Post-procedure dental checkup',
                'status' => 'pending',
                'staff_notes' => "Auto-scheduled from treatment record #{$treatment->id}",
            ]);
        }

        // 4. Auto-generate draft invoice if toggled
        if (!empty($validated['create_invoice']) && !empty($validated['service_id'])) {
            $service = DentalService::find($validated['service_id']);
            if ($service) {
                $invoiceCount = Invoice::where('clinic_id', $user->clinic_id)->count() + 1;
                $invoiceNumber = 'INV-' . Carbon::now()->format('Ym') . '-' . str_pad($invoiceCount, 4, '0', STR_PAD_LEFT);

                $invoice = Invoice::create([
                    'clinic_id' => $user->clinic_id,
                    'patient_id' => $validated['patient_id'],
                    'appointment_id' => $validated['appointment_id'] ?? null,
                    'invoice_number' => $invoiceNumber,
                    'total_amount' => $service->standard_fee,
                    'discount_amount' => 0.00,
                    'paid_amount' => 0.00,
                    'balance_amount' => $service->standard_fee,
                    'status' => 'unpaid',
                    'due_date' => Carbon::today(),
                ]);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $service->id,
                    'description' => $service->name . ($validated['tooth_numbers'] ? " (Tooth: {$validated['tooth_numbers']})" : ''),
                    'unit_price' => $service->standard_fee,
                    'quantity' => 1,
                    'total_price' => $service->standard_fee,
                ]);

                $invoice->recalculateFinancials();
            }
        }

        return redirect()->route('treatments.show', $treatment->id)
            ->with('status', 'Clinical treatment record documented successfully.');
    }

    /**
     * Display a comprehensive treatment record summary with dental odontogram and Rx slip.
     */
    public function show(TreatmentRecord $treatment): View
    {
        $user = Auth::user();

        $treatment->load([
            'patient',
            'doctor.doctorProfile',
            'service',
            'appointment',
            'followUps',
        ]);

        // Find any invoice linked to this appointment or patient around this treatment
        $linkedInvoice = null;
        if ($treatment->appointment_id) {
            $linkedInvoice = Invoice::where('appointment_id', $treatment->appointment_id)->first();
        }

        return view('treatments.show', compact('treatment', 'user', 'linkedInvoice'));
    }

    /**
     * Show form to edit an existing clinical treatment record.
     */
    public function edit(TreatmentRecord $treatment): View
    {
        $user = Auth::user();

        if ($user->isReceptionist()) {
            abort(403, 'Unauthorized. Only Dentists and Clinic Administrators can edit clinical treatments.');
        }

        $treatment->load(['patient', 'doctor', 'service', 'appointment']);

        $services = DentalService::where('clinic_id', $user->clinic_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $doctors = User::where('clinic_id', $user->clinic_id)
            ->whereIn('role', ['dentist', 'clinic_admin'])
            ->with('doctorProfile')
            ->get();

        $selectedTeeth = $treatment->tooth_numbers
            ? array_values(array_filter(array_map('trim', explode(',', $treatment->tooth_numbers))))
            : [];

        return view('treatments.edit', compact('treatment', 'services', 'doctors', 'selectedTeeth'));
    }

    /**
     * Update the treatment record.
     */
    public function update(Request $request, TreatmentRecord $treatment): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isReceptionist()) {
            abort(403, 'Unauthorized. Only Dentists and Clinic Administrators can edit clinical treatments.');
        }

        $validated = $request->validate([
            'tooth_numbers' => ['nullable', 'string', 'max:100'],
            'diagnosis' => ['required', 'string', 'max:1000'],
            'treatment_rendered' => ['required', 'string', 'max:1000'],
            'clinical_notes' => ['nullable', 'string', 'max:2000'],
            'prescription' => ['nullable', 'string', 'max:1000'],
            'service_id' => ['nullable', 'exists:dental_services,id'],
        ]);

        $treatment->update($validated);

        return redirect()->route('treatments.show', $treatment->id)
            ->with('status', 'Treatment record updated successfully.');
    }

    /**
     * Archive or delete a treatment record.
     */
    public function destroy(TreatmentRecord $treatment): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isReceptionist()) {
            abort(403, 'Unauthorized. Only Dentists and Clinic Administrators can delete clinical treatments.');
        }

        $treatment->delete();

        return redirect()->route('treatments.index')
            ->with('status', 'Treatment record archived successfully.');
    }
}
