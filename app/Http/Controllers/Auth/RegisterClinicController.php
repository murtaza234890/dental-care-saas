<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\DentalService;
use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterClinicController extends Controller
{
    /**
     * Show the clinic registration form.
     */
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register-clinic');
    }

    /**
     * Handle clinic and owner registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'clinic_name' => ['required', 'string', 'max:255'],
            'clinic_phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'owner_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'owner_phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $slug = Str::slug($validated['clinic_name']);
        // Ensure unique slug
        $baseSlug = $slug;
        $counter = 1;
        while (Clinic::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $user = DB::transaction(function () use ($validated, $slug) {
            // 1. Create Clinic
            $clinic = Clinic::create([
                'name' => $validated['clinic_name'],
                'slug' => $slug,
                'phone' => $validated['clinic_phone'],
                'address' => $validated['address'] ?? null,
                'currency' => 'PKR',
                'timezone' => 'Asia/Karachi',
                'status' => 'active',
            ]);

            // 2. Create Owner User
            $owner = User::create([
                'clinic_id' => $clinic->id,
                'name' => $validated['owner_name'],
                'email' => $validated['email'],
                'phone' => $validated['owner_phone'] ?? $validated['clinic_phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'clinic_admin',
                'is_active' => true,
            ]);

            // 3. Create Doctor Profile for owner
            DoctorProfile::create([
                'clinic_id' => $clinic->id,
                'user_id' => $owner->id,
                'specialization' => 'Dental Surgeon / Clinic Director',
                'consultation_fee' => 1500.00,
                'slot_duration_minutes' => 30,
                'start_time' => '10:00:00',
                'end_time' => '20:00:00',
                'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
            ]);

            // 4. Seed default basic services
            DentalService::create([
                'clinic_id' => $clinic->id,
                'name' => 'Consultation & Dental Checkup',
                'description' => 'Comprehensive dental examination',
                'standard_fee' => 1500.00,
                'estimated_duration_minutes' => 20,
                'is_active' => true,
            ]);

            DentalService::create([
                'clinic_id' => $clinic->id,
                'name' => 'Scaling & Polishing (Teeth Cleaning)',
                'description' => 'Professional ultrasonic cleaning',
                'standard_fee' => 3000.00,
                'estimated_duration_minutes' => 30,
                'is_active' => true,
            ]);

            DentalService::create([
                'clinic_id' => $clinic->id,
                'name' => 'Tooth Restoration / Filling',
                'description' => 'Composite tooth-colored filling',
                'standard_fee' => 4000.00,
                'estimated_duration_minutes' => 30,
                'is_active' => true,
            ]);

            // 5. Assign 14-day Free Trial Subscription (Professional Plan)
            $defaultPlan = \App\Models\SubscriptionPlan::where('slug', 'professional')->first()
                ?? \App\Models\SubscriptionPlan::first();

            if ($defaultPlan) {
                \App\Models\Subscription::create([
                    'clinic_id' => $clinic->id,
                    'plan_id' => $defaultPlan->id,
                    'status' => 'trialing',
                    'billing_interval' => 'monthly',
                    'trial_ends_at' => \Carbon\Carbon::now()->addDays(14),
                    'current_period_start' => \Carbon\Carbon::now(),
                    'current_period_end' => \Carbon\Carbon::now()->addDays(14),
                    'payment_method' => 'trial',
                ]);
            }

            return $owner;
        });

        Auth::login($user);
        session(['current_clinic_id' => $user->clinic_id]);

        return redirect()->route('dashboard')->with('status', 'Welcome! Your dental clinic workspace has been created successfully.');
    }
}
