<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClinicStaffController extends Controller
{
    /**
     * Display staff roster with role badges, status toggles, and search filter.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $clinicId = $user->clinic_id;

        $roleFilter = $request->query('role', 'all');
        $statusFilter = $request->query('status', 'all');
        $search = trim($request->query('search', ''));

        $query = User::where('clinic_id', $clinicId)->with(['doctorProfile']);

        if ($roleFilter !== 'all' && in_array($roleFilter, ['clinic_admin', 'dentist', 'receptionist'])) {
            $query->where('role', $roleFilter);
        }

        if ($statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $staffMembers = $query->orderBy('name')->paginate(15)->withQueryString();

        // Staffing metrics
        $totalStaff = User::where('clinic_id', $clinicId)->count();
        $activeStaff = User::where('clinic_id', $clinicId)->where('is_active', true)->count();
        $dentistsCount = User::where('clinic_id', $clinicId)->where('role', 'dentist')->count();
        $receptionistsCount = User::where('clinic_id', $clinicId)->where('role', 'receptionist')->count();

        $metrics = [
            'total' => $totalStaff,
            'active' => $activeStaff,
            'inactive' => $totalStaff - $activeStaff,
            'dentists' => $dentistsCount,
            'receptionists' => $receptionistsCount,
        ];

        return view('admin.staff.index', compact(
            'staffMembers',
            'metrics',
            'roleFilter',
            'statusFilter',
            'search',
            'user'
        ));
    }

    /**
     * Show form to invite or register a new clinic staff member.
     */
    public function create(): View
    {
        return view('admin.staff.create');
    }

    /**
     * Store newly onboarded clinic staff member.
     */
    public function store(Request $request): RedirectResponse
    {
        $admin = Auth::user();
        $clinicId = $admin->clinic_id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'string', 'in:clinic_admin,dentist,receptionist'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // Doctor profile attributes
            'specialization' => ['nullable', 'string', 'max:255'],
            'consultation_fee' => ['nullable', 'numeric', 'min:0'],
            'slot_duration_minutes' => ['nullable', 'integer', 'min:10', 'max:180'],
        ]);

        $newUser = User::create([
            'clinic_id' => $clinicId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        // Auto-provision DoctorProfile if onboarded as dentist
        if ($validated['role'] === 'dentist') {
            DoctorProfile::create([
                'clinic_id' => $clinicId,
                'user_id' => $newUser->id,
                'specialization' => $validated['specialization'] ?? 'General Dental Practitioner',
                'consultation_fee' => $validated['consultation_fee'] ?? 1500.00,
                'slot_duration_minutes' => $validated['slot_duration_minutes'] ?? 30,
                'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'start_time' => '10:00:00',
                'end_time' => '19:00:00',
                'break_start' => '14:00:00',
                'break_end' => '15:00:00',
            ]);
        }

        return redirect()->route('admin.staff.index')
            ->with('status', "Staff member '{$newUser->name}' onboarded successfully.");
    }

    /**
     * Show form to edit staff member credentials & role.
     */
    public function edit(User $staff): View
    {
        $admin = Auth::user();

        if ($staff->clinic_id !== $admin->clinic_id) {
            abort(404);
        }

        $staff->load('doctorProfile');
        $isReferenced = $staff->isReferenced();

        return view('admin.staff.edit', compact('staff', 'isReferenced'));
    }

    /**
     * Update existing staff profile and roles.
     */
    public function update(Request $request, User $staff): RedirectResponse
    {
        $admin = Auth::user();

        if ($staff->clinic_id !== $admin->clinic_id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($staff->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'string', 'in:clinic_admin,dentist,receptionist'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'consultation_fee' => ['nullable', 'numeric', 'min:0'],
            'slot_duration_minutes' => ['nullable', 'integer', 'min:10', 'max:180'],
        ]);

        $isActive = $request->boolean('is_active', false);

        // Prevent self-lockout
        if ($staff->id === $admin->id) {
            if (! $isActive || $validated['role'] !== 'clinic_admin') {
                return redirect()->back()
                    ->withErrors(['role' => 'You cannot deactivate or remove administrator role from your own active account.'])
                    ->withInput();
            }
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'is_active' => $isActive,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $staff->update($updateData);

        // Handle DoctorProfile sync if role is dentist
        if ($validated['role'] === 'dentist') {
            DoctorProfile::updateOrCreate(
                ['clinic_id' => $admin->clinic_id, 'user_id' => $staff->id],
                [
                    'specialization' => $validated['specialization'] ?? 'General Dental Practitioner',
                    'consultation_fee' => $validated['consultation_fee'] ?? 1500.00,
                    'slot_duration_minutes' => $validated['slot_duration_minutes'] ?? 30,
                ]
            );
        }

        return redirect()->route('admin.staff.index')
            ->with('status', "Staff member '{$staff->name}' profile updated successfully.");
    }

    /**
     * Toggle active/inactive status of a staff member.
     */
    public function toggleStatus(User $staff): RedirectResponse
    {
        $admin = Auth::user();

        if ($staff->clinic_id !== $admin->clinic_id) {
            abort(404);
        }

        if ($staff->id === $admin->id) {
            return redirect()->back()
                ->with('error', 'You cannot deactivate your own administrator account.');
        }

        $staff->update([
            'is_active' => ! $staff->is_active,
        ]);

        $state = $staff->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('status', "Account for '{$staff->name}' has been {$state}.");
    }

    /**
     * Safely archive or remove staff member.
     */
    public function destroy(User $staff): RedirectResponse
    {
        $admin = Auth::user();

        if ($staff->clinic_id !== $admin->clinic_id) {
            abort(404);
        }

        if ($staff->id === $admin->id) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own administrator account.');
        }

        if ($staff->isReferenced()) {
            $staff->update(['is_active' => false]);

            return redirect()->route('admin.staff.index')
                ->with('status', "Staff member '{$staff->name}' has historical appointments, treatments, or payment records. The account has been deactivated instead of deleted to protect historical audit logs.");
        }

        $name = $staff->name;
        if ($staff->doctorProfile) {
            $staff->doctorProfile->delete();
        }
        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('status', "Staff member '{$name}' permanently removed.");
    }
}
