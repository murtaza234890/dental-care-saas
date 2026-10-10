<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\TreatmentRecord;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PatientController extends Controller
{
    /**
     * Display the clinic patient directory with search and filters.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $bloodGroup = $request->input('blood_group');
        $gender = $request->input('gender');
        $hasMedicalAlert = $request->boolean('medical_alert');

        // Scoped automatically to current clinic via BelongsToClinic trait
        $query = Patient::withCount(['appointments', 'treatmentRecords', 'invoices'])
            ->with(['invoices']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($bloodGroup) {
            $query->where('blood_group', $bloodGroup);
        }

        if ($gender) {
            $query->where('gender', $gender);
        }

        if ($hasMedicalAlert) {
            $query->whereNotNull('medical_history')
                ->where('medical_history', '!=', '');
        }

        $patients = $query->latest()->paginate(15)->withQueryString();

        $totalPatients = Patient::count();
        $patientsWithAlerts = Patient::whereNotNull('medical_history')->where('medical_history', '!=', '')->count();

        return view('patients.index', compact(
            'patients',
            'search',
            'bloodGroup',
            'gender',
            'hasMedicalAlert',
            'totalPatients',
            'patientsWithAlerts'
        ));
    }

    /**
     * Show form to create a new patient file.
     */
    public function create(): View|RedirectResponse
    {
        $clinic = Auth::user()->clinic;
        if ($clinic && ! $clinic->canAddPatient()) {
            $max = $clinic->currentPlan()?->max_patients ?? 200;
            return redirect()->route('admin.subscription')
                ->withErrors(['subscription' => "Patient quota limit reached ({$max} patients for your plan). Please upgrade your subscription to register more patients."]);
        }

        return view('patients.create');
    }

    /**
     * Store a new patient in the clinic directory.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $clinic = $user->clinic;

        if ($clinic && ! $clinic->canAddPatient()) {
            $max = $clinic->currentPlan()?->max_patients ?? 200;
            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => "Patient quota limit reached ({$max} patients for your plan). Please upgrade your subscription to register more patients."]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string', 'max:500'],
            'medical_history' => ['nullable', 'string', 'max:1000'],
        ]);

        $patient = Patient::create(array_merge($validated, [
            'clinic_id' => $user->clinic_id,
        ]));

        return redirect()->route('patients.show', $patient->id)
            ->with('status', "Patient file for {$patient->name} has been created successfully.");
    }

    /**
     * Display a comprehensive patient clinical profile and history.
     */
    public function show(Patient $patient): View
    {
        $user = Auth::user();

        // Eager load relations for the dossier
        $patient->load([
            'appointments' => fn ($q) => $q->with(['doctor.doctorProfile', 'service'])->latest('appointment_date'),
            'invoices' => fn ($q) => $q->with('items')->latest(),
            'payments' => fn ($q) => $q->with('receivedBy')->latest(),
            'followUps' => fn ($q) => $q->latest(),
        ]);

        // Eager load treatment records (with clinical notes restriction for receptionist)
        $treatmentRecords = TreatmentRecord::where('patient_id', $patient->id)
            ->with(['doctor', 'service'])
            ->latest()
            ->get();

        $outstandingBalance = $patient->totalOutstandingBalance();
        $totalPaid = $patient->payments->sum('amount');

        return view('patients.show', compact(
            'patient',
            'treatmentRecords',
            'outstandingBalance',
            'totalPaid',
            'user'
        ));
    }

    /**
     * Show form to edit patient information.
     */
    public function edit(Patient $patient): View
    {
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the patient record.
     */
    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string', 'max:500'],
            'medical_history' => ['nullable', 'string', 'max:1000'],
        ]);

        $patient->update($validated);

        return redirect()->route('patients.show', $patient->id)
            ->with('status', "Patient information updated successfully.");
    }

    /**
     * Remove the patient record from clinic directory.
     */
    public function destroy(Patient $patient): RedirectResponse
    {
        $name = $patient->name;
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('status', "Patient file for {$name} was archived successfully.");
    }
}
