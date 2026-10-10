<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DentalService;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Display the clinic appointment schedule with filters.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        $selectedDoctorId = $request->input('doctor_id');
        $selectedStatus = $request->input('status');
        $search = $request->input('search');

        // Scoped automatically to current clinic via BelongsToClinic trait
        $query = Appointment::with(['patient', 'doctor.doctorProfile', 'service'])
            ->whereDate('appointment_date', $selectedDate);

        // If Dentist is logged in, restrict to their appointments by default unless overridden by admin
        if ($user->isDentist()) {
            $query->where('doctor_id', $user->id);
        } elseif ($selectedDoctorId) {
            $query->where('doctor_id', $selectedDoctorId);
        }

        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        if ($search) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $appointments = $query->orderBy('starts_at')->get();

        // Doctors available for filtering
        $doctors = User::where('clinic_id', $user->clinic_id)
            ->whereIn('role', ['dentist', 'clinic_admin'])
            ->with('doctorProfile')
            ->get();

        // Metrics for the selected date
        $allForDate = Appointment::whereDate('appointment_date', $selectedDate)->get();
        $metrics = [
            'total' => $allForDate->count(),
            'confirmed' => $allForDate->where('status', 'confirmed')->count(),
            'checked_in' => $allForDate->where('status', 'checked_in')->count(),
            'in_progress' => $allForDate->where('status', 'in_progress')->count(),
            'completed' => $allForDate->where('status', 'completed')->count(),
            'cancelled' => $allForDate->where('status', 'cancelled')->count(),
        ];

        return view('appointments.index', compact(
            'appointments',
            'doctors',
            'selectedDate',
            'selectedDoctorId',
            'selectedStatus',
            'search',
            'metrics'
        ));
    }

    /**
     * Show front-desk form to create a new appointment.
     */
    public function create(Request $request): View
    {
        $user = Auth::user();

        $doctors = User::where('clinic_id', $user->clinic_id)
            ->whereIn('role', ['dentist', 'clinic_admin'])
            ->with('doctorProfile')
            ->get();

        $services = DentalService::where('clinic_id', $user->clinic_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $patients = Patient::where('clinic_id', $user->clinic_id)
            ->orderBy('name')
            ->get();

        $defaultDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        $defaultDoctorId = $request->input('doctor_id', $user->isDentist() ? $user->id : null);

        return view('appointments.create', compact(
            'doctors',
            'services',
            'patients',
            'defaultDate',
            'defaultDoctorId'
        ));
    }

    /**
     * Store a new appointment created by clinic staff.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'patient_type' => ['required', 'in:existing,new'],
            'patient_id' => ['required_if:patient_type,existing', 'nullable', 'exists:patients,id'],
            'patient_name' => ['required_if:patient_type,new', 'nullable', 'string', 'max:255'],
            'patient_phone' => ['required_if:patient_type,new', 'nullable', 'string', 'max:30'],
            'patient_email' => ['nullable', 'email', 'max:255'],
            'doctor_id' => ['required', 'exists:users,id'],
            'service_id' => ['nullable', 'exists:dental_services,id'],
            'appointment_date' => ['required', 'date'],
            'starts_at' => ['required', 'date_format:H:i'],
            'patient_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $doctor = User::where('clinic_id', $user->clinic_id)->findOrFail($validated['doctor_id']);
        $duration = 30;
        if ($doctor->doctorProfile) {
            $duration = $doctor->doctorProfile->slot_duration_minutes ?? 30;
        }

        $startsAt = Carbon::parse("{$validated['appointment_date']} {$validated['starts_at']}");
        $endsAt = $startsAt->copy()->addMinutes($duration);

        // Check for double booking conflicts
        $hasConflict = Appointment::where('clinic_id', $user->clinic_id)
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->where(function ($q) use ($startsAt) {
                $q->where('starts_at', $startsAt->format('H:i:s'))
                    ->orWhere('starts_at', $startsAt->format('H:i'));
            })
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($hasConflict) {
            return back()->withInput()->withErrors([
                'starts_at' => "Conflict: Dr. {$doctor->name} already has an appointment booked at {$startsAt->format('h:i A')} on this date.",
            ]);
        }

        DB::transaction(function () use ($user, $doctor, $validated, $startsAt, $endsAt) {
            if ($validated['patient_type'] === 'existing') {
                $patient = Patient::where('clinic_id', $user->clinic_id)->findOrFail($validated['patient_id']);
            } else {
                $patient = Patient::create([
                    'clinic_id' => $user->clinic_id,
                    'name' => $validated['patient_name'],
                    'phone' => $validated['patient_phone'],
                    'email' => $validated['patient_email'] ?? null,
                ]);
            }

            Appointment::create([
                'clinic_id' => $user->clinic_id,
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'service_id' => $validated['service_id'] ?? null,
                'appointment_date' => $validated['appointment_date'],
                'starts_at' => $startsAt->format('H:i:s'),
                'ends_at' => $endsAt->format('H:i:s'),
                'status' => 'confirmed',
                'patient_notes' => $validated['patient_notes'] ?? null,
            ]);
        });

        return redirect()->route('appointments.index', ['date' => $validated['appointment_date']])
            ->with('status', 'Appointment successfully scheduled.');
    }

    /**
     * Update the lifecycle status of an appointment.
     */
    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,checked_in,in_progress,completed,cancelled,no_show'],
            'cancellation_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $updateData = ['status' => $validated['status']];
        if ($validated['status'] === 'cancelled') {
            $updateData['cancellation_reason'] = $validated['cancellation_reason'] ?? 'Cancelled by staff';
        }

        $appointment->update($updateData);

        return back()->with('status', "Appointment status updated to " . ucfirst(str_replace('_', ' ', $validated['status'])) . ".");
    }

    /**
     * Reschedule an appointment to a new date and time.
     */
    public function reschedule(Request $request, Appointment $appointment): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'appointment_date' => ['required', 'date'],
            'starts_at' => ['required', 'date_format:H:i'],
        ]);

        $doctor = $appointment->doctor;
        $duration = 30;
        if ($doctor && $doctor->doctorProfile) {
            $duration = $doctor->doctorProfile->slot_duration_minutes ?? 30;
        }

        $startsAt = Carbon::parse("{$validated['appointment_date']} {$validated['starts_at']}");
        $endsAt = $startsAt->copy()->addMinutes($duration);

        // Check conflict excluding current appointment
        $hasConflict = Appointment::where('clinic_id', $user->clinic_id)
            ->where('doctor_id', $appointment->doctor_id)
            ->where('id', '!=', $appointment->id)
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->where(function ($q) use ($startsAt) {
                $q->where('starts_at', $startsAt->format('H:i:s'))
                    ->orWhere('starts_at', $startsAt->format('H:i'));
            })
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($hasConflict) {
            return back()->withErrors([
                'reschedule' => "Conflict: Dr. {$doctor->name} already has an appointment booked at {$startsAt->format('h:i A')} on this date.",
            ]);
        }

        $appointment->update([
            'appointment_date' => $validated['appointment_date'],
            'starts_at' => $startsAt->format('H:i:s'),
            'ends_at' => $endsAt->format('H:i:s'),
            'status' => 'confirmed',
        ]);

        return redirect()->route('appointments.index', ['date' => $validated['appointment_date']])
            ->with('status', "Appointment successfully rescheduled to {$startsAt->format('d M Y, h:i A')}.");
    }
}
