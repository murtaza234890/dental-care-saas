<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DoctorScheduleController extends Controller
{
    /**
     * Display the clinic doctors directory and weekly chair timetable roster.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Scoped to current clinic
        $doctors = User::where('clinic_id', $user->clinic_id)
            ->whereIn('role', ['dentist', 'clinic_admin'])
            ->with(['doctorProfile'])
            ->withCount(['doctorAppointments' => function ($q) {
                $q->whereDate('appointment_date', '>=', Carbon::today());
            }])
            ->orderBy('name')
            ->get();

        $daysOfWeek = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        return view('doctors.index', compact('doctors', 'daysOfWeek', 'user'));
    }

    /**
     * Show form to onboard a new dentist with schedule.
     */
    public function create(): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isClinicAdmin()) {
            abort(403, 'Unauthorized. Only Clinic Administrators can onboard new doctors.');
        }

        $clinic = $user->clinic;
        if ($clinic && ! $clinic->canAddDoctor()) {
            $maxDoc = $clinic->currentPlan()?->max_doctors ?? 1;
            return redirect()->route('admin.subscription')
                ->withErrors(['subscription' => "Your current subscription plan allows a maximum of {$maxDoc} doctor(s). Please upgrade your subscription to add more doctors."]);
        }

        $daysOfWeek = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        return view('doctors.create', compact('daysOfWeek'));
    }

    /**
     * Store a newly created doctor and their working schedule.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isClinicAdmin()) {
            abort(403, 'Unauthorized. Only Clinic Administrators can onboard new doctors.');
        }

        $clinic = $user->clinic;
        if ($clinic && ! $clinic->canAddDoctor()) {
            $maxDoc = $clinic->currentPlan()?->max_doctors ?? 1;
            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => "Your current subscription plan allows a maximum of {$maxDoc} doctor(s). Please upgrade your subscription to add more doctors."]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6'],
            'specialization' => ['required', 'string', 'max:255'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'slot_duration_minutes' => ['required', 'integer', 'in:15,20,30,45,60'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'working_days' => ['required', 'array', 'min:1'],
            'working_days.*' => ['in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'break_start' => ['nullable', 'date_format:H:i'],
            'break_end' => ['nullable', 'required_with:break_start', 'date_format:H:i'],
        ]);

        $doctor = User::create([
            'clinic_id' => $user->clinic_id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'clinic_id' => $user->clinic_id,
            'user_id' => $doctor->id,
            'specialization' => $validated['specialization'],
            'consultation_fee' => $validated['consultation_fee'],
            'slot_duration_minutes' => $validated['slot_duration_minutes'],
            'bio' => $validated['bio'] ?? null,
            'working_days' => $validated['working_days'],
            'start_time' => $validated['start_time'] . ':00',
            'end_time' => $validated['end_time'] . ':00',
            'break_start' => !empty($validated['break_start']) ? $validated['break_start'] . ':00' : null,
            'break_end' => !empty($validated['break_end']) ? $validated['break_end'] . ':00' : null,
        ]);

        return redirect()->route('doctors.index')
            ->with('status', "Dr. {$doctor->name} onboarded successfully with customized chair schedule.");
    }

    /**
     * Display a doctor's schedule and clinical summary.
     */
    public function show(User $doctor): View
    {
        $user = Auth::user();

        // Tenant Isolation Check
        if ($doctor->clinic_id !== $user->clinic_id) {
            abort(404);
        }

        $doctor->load(['doctorProfile']);

        $upcomingAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', '>=', Carbon::today())
            ->with(['patient', 'service'])
            ->orderBy('appointment_date')
            ->orderBy('starts_at')
            ->take(10)
            ->get();

        $daysOfWeek = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        return view('doctors.show', compact('doctor', 'upcomingAppointments', 'daysOfWeek'));
    }

    /**
     * Show form to edit a doctor's profile and working schedule.
     */
    public function edit(User $doctor): View
    {
        $user = Auth::user();

        // Tenant Isolation Check
        if ($doctor->clinic_id !== $user->clinic_id) {
            abort(404);
        }

        // Authorization: Clinic Admin can edit all, Dentist can only edit their own profile
        if (! $user->isClinicAdmin() && $user->id !== $doctor->id) {
            abort(403, 'Unauthorized. You can only edit your own doctor profile and schedule.');
        }

        $doctor->load('doctorProfile');

        $daysOfWeek = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        return view('doctors.edit', compact('doctor', 'daysOfWeek', 'user'));
    }

    /**
     * Update doctor's profile and working shift schedule.
     */
    public function update(Request $request, User $doctor): RedirectResponse
    {
        $user = Auth::user();

        // Tenant Isolation Check
        if ($doctor->clinic_id !== $user->clinic_id) {
            abort(404);
        }

        // Authorization check
        if (! $user->isClinicAdmin() && $user->id !== $doctor->id) {
            abort(403, 'Unauthorized. You can only edit your own doctor profile and schedule.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $doctor->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:6'],
            'specialization' => ['required', 'string', 'max:255'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'slot_duration_minutes' => ['required', 'integer', 'in:15,20,30,45,60'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'working_days' => ['required', 'array', 'min:1'],
            'working_days.*' => ['in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'break_start' => ['nullable'],
            'break_end' => ['nullable'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        if ($user->isClinicAdmin() && isset($validated['is_active'])) {
            $userData['is_active'] = (bool) $validated['is_active'];
        }

        $doctor->update($userData);

        // Normalize time strings to H:i:s
        $startTime = strlen($validated['start_time']) === 5 ? $validated['start_time'] . ':00' : $validated['start_time'];
        $endTime = strlen($validated['end_time']) === 5 ? $validated['end_time'] . ':00' : $validated['end_time'];
        $breakStart = !empty($validated['break_start']) ? (strlen($validated['break_start']) === 5 ? $validated['break_start'] . ':00' : $validated['break_start']) : null;
        $breakEnd = !empty($validated['break_end']) ? (strlen($validated['break_end']) === 5 ? $validated['break_end'] . ':00' : $validated['break_end']) : null;

        $profileData = [
            'clinic_id' => $user->clinic_id,
            'specialization' => $validated['specialization'],
            'consultation_fee' => $validated['consultation_fee'],
            'slot_duration_minutes' => $validated['slot_duration_minutes'],
            'bio' => $validated['bio'] ?? null,
            'working_days' => $validated['working_days'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'break_start' => $breakStart,
            'break_end' => $breakEnd,
        ];

        if ($doctor->doctorProfile) {
            $doctor->doctorProfile->update($profileData);
        } else {
            $doctor->doctorProfile()->create($profileData);
        }

        return redirect()->route('doctors.index')
            ->with('status', "Schedule and profile for Dr. {$doctor->name} updated successfully.");
    }

    /**
     * Deactivate a doctor account.
     */
    public function destroy(User $doctor): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isClinicAdmin()) {
            abort(403, 'Unauthorized. Only Clinic Administrators can deactivate doctors.');
        }

        if ($doctor->id === $user->id) {
            return back()->withErrors(['error' => 'You cannot deactivate your own administrative account.']);
        }

        if ($doctor->clinic_id !== $user->clinic_id) {
            abort(404);
        }

        $doctor->update(['is_active' => false]);

        return redirect()->route('doctors.index')
            ->with('status', "Dr. {$doctor->name} has been deactivated.");
    }
}
