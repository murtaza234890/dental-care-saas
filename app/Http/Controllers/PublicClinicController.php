<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DentalService;
use App\Models\DoctorProfile;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicClinicController extends Controller
{
    /**
     * Display the public branded clinic website.
     */
    public function show(string $slug): View
    {
        $clinic = Clinic::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        // Doctors with their profiles
        $doctors = User::where('clinic_id', $clinic->id)
            ->where('role', 'dentist')
            ->orWhere(function ($q) use ($clinic) {
                $q->where('clinic_id', $clinic->id)->where('role', 'clinic_admin');
            })
            ->with('doctorProfile')
            ->get();

        // Active dental services catalog
        $services = DentalService::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->where('is_active', true)
            ->orderBy('standard_fee')
            ->get();

        return view('public.clinic', compact('clinic', 'doctors', 'services'));
    }

    /**
     * Return available appointment slots for a selected doctor and date.
     */
    public function getAvailableSlots(Request $request, string $slug): JsonResponse
    {
        $clinic = Clinic::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'doctor_id' => ['required', 'exists:users,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $doctor = User::where('clinic_id', $clinic->id)
            ->with('doctorProfile')
            ->findOrFail($validated['doctor_id']);

        $profile = $doctor->doctorProfile;
        $date = Carbon::parse($validated['date']);
        $dayName = strtolower($date->format('l'));

        // Check if doctor works on selected day
        if ($profile && $profile->working_days && ! in_array($dayName, $profile->working_days, true)) {
            return response()->json([
                'available' => false,
                'message' => "Doctor is not scheduled on {$date->format('l')}s.",
                'slots' => [],
            ]);
        }

        $startTimeStr = $profile ? ($profile->start_time ?? '10:00:00') : '10:00:00';
        $endTimeStr = $profile ? ($profile->end_time ?? '20:00:00') : '20:00:00';
        $durationMinutes = $profile ? ($profile->slot_duration_minutes ?? 30) : 30;

        $startTime = Carbon::parse("{$validated['date']} {$startTimeStr}");
        $endTime = Carbon::parse("{$validated['date']} {$endTimeStr}");

        $breakStart = $profile && $profile->break_start ? Carbon::parse("{$validated['date']} {$profile->break_start}") : null;
        $breakEnd = $profile && $profile->break_end ? Carbon::parse("{$validated['date']} {$profile->break_end}") : null;

        // Existing booked appointments for this doctor on this date
        $bookedStarts = Appointment::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $validated['date'])
            ->whereNotIn('status', ['cancelled'])
            ->pluck('starts_at')
            ->map(fn ($time) => Carbon::parse("{$validated['date']} {$time}")->format('H:i'))
            ->toArray();

        $slots = [];
        $current = $startTime->copy();

        while ($current->copy()->addMinutes($durationMinutes)->lte($endTime)) {
            $slotStart = $current->copy();
            $slotEnd = $current->copy()->addMinutes($durationMinutes);

            // Check if slot overlaps with break
            $isBreak = false;
            if ($breakStart && $breakEnd) {
                if ($slotStart->gte($breakStart) && $slotStart->lt($breakEnd)) {
                    $isBreak = true;
                }
            }

            $slotFormatted = $slotStart->format('H:i');
            $isBooked = in_array($slotFormatted, $bookedStarts, true);

            if (! $isBreak && ! $isBooked) {
                $slots[] = [
                    'time' => $slotFormatted,
                    'label' => $slotStart->format('h:i A'),
                ];
            }

            $current->addMinutes($durationMinutes);
        }

        return response()->json([
            'available' => true,
            'doctor' => $doctor->name,
            'date' => $date->format('Y-m-d'),
            'slots' => $slots,
        ]);
    }

    /**
     * Process online appointment booking form submission.
     */
    public function storeBooking(Request $request, string $slug): RedirectResponse
    {
        $clinic = Clinic::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $validated = $request->validate([
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_phone' => ['required', 'string', 'max:30'],
            'patient_email' => ['nullable', 'email', 'max:255'],
            'doctor_id' => ['required', 'exists:users,id'],
            'service_id' => ['nullable', 'exists:dental_services,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'starts_at' => ['required', 'date_format:H:i'],
            'patient_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $doctor = User::where('clinic_id', $clinic->id)->findOrFail($validated['doctor_id']);
        $duration = 30;
        if ($doctor->doctorProfile) {
            $duration = $doctor->doctorProfile->slot_duration_minutes ?? 30;
        }

        $startsAt = Carbon::parse("{$validated['appointment_date']} {$validated['starts_at']}");
        $endsAt = $startsAt->copy()->addMinutes($duration);

        // Check if double booking occurs
        $hasConflict = Appointment::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
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
                'starts_at' => 'This appointment slot was just booked by another patient. Please choose another time slot.',
            ]);
        }

        $appointment = DB::transaction(function () use ($clinic, $doctor, $validated, $startsAt, $endsAt) {
            // Find existing patient or create new record for this clinic
            $patient = Patient::withoutGlobalScopes()
                ->where('clinic_id', $clinic->id)
                ->where('phone', $validated['patient_phone'])
                ->first();

            if (! $patient) {
                $patient = Patient::create([
                    'clinic_id' => $clinic->id,
                    'name' => $validated['patient_name'],
                    'phone' => $validated['patient_phone'],
                    'email' => $validated['patient_email'] ?? null,
                ]);
            }

            // Create appointment record
            return Appointment::create([
                'clinic_id' => $clinic->id,
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

        return redirect()->route('clinic.confirmation', [
            'slug' => $clinic->slug,
            'appointment' => $appointment->id,
        ])->with('status', 'Your appointment has been successfully scheduled!');
    }

    /**
     * Display appointment booking confirmation receipt.
     */
    public function confirmation(string $slug, int $appointmentId): View
    {
        $clinic = Clinic::where('slug', $slug)->firstOrFail();

        $appointment = Appointment::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->with(['patient', 'doctor.doctorProfile', 'service'])
            ->findOrFail($appointmentId);

        return view('public.confirmation', compact('clinic', 'appointment'));
    }
}
