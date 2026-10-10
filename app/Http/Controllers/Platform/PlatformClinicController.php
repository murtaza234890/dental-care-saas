<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\TreatmentRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PlatformClinicController extends Controller
{
    /**
     * Display multi-tenant clinic registry with search, status filters, and global SaaS KPIs.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'all');
        $search = trim($request->query('search', ''));

        $query = Clinic::withCount(['users', 'patients', 'appointments', 'invoices']);

        if ($statusFilter !== 'all' && in_array($statusFilter, ['active', 'inactive', 'suspended'])) {
            $query->where('status', $statusFilter);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $clinics = $query->orderBy('name')->paginate(12)->withQueryString();

        // Platform-wide Aggregated Telemetry
        $totalClinics = Clinic::count();
        $activeClinics = Clinic::where('status', 'active')->count();
        $suspendedClinics = Clinic::where('status', 'suspended')->count();
        $inactiveClinics = Clinic::where('status', 'inactive')->count();

        $totalPatients = Patient::withoutGlobalScopes()->count();
        $totalAppointments = Appointment::withoutGlobalScopes()->count();
        $totalPlatformGTV = (float) Invoice::withoutGlobalScopes()->sum('total_amount');
        $totalPlatformCollections = (float) Payment::withoutGlobalScopes()->sum('amount');

        $metrics = [
            'total_clinics' => $totalClinics,
            'active_clinics' => $activeClinics,
            'suspended_clinics' => $suspendedClinics,
            'inactive_clinics' => $inactiveClinics,
            'total_patients' => $totalPatients,
            'total_appointments' => $totalAppointments,
            'total_gtv' => $totalPlatformGTV,
            'total_collected' => $totalPlatformCollections,
        ];

        return view('platform.clinics.index', compact(
            'clinics',
            'metrics',
            'statusFilter',
            'search'
        ));
    }

    /**
     * Display drill-down tenant inspection dossier for a specific clinic.
     */
    public function show(Clinic $clinic): View
    {
        $clinic->loadCount(['users', 'patients', 'appointments', 'invoices', 'services', 'treatmentRecords']);

        // Load clinic staff
        $staffMembers = $clinic->users()->with('doctorProfile')->orderBy('role')->get();

        // Financial activity for this tenant
        $invoicedTotal = (float) Invoice::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->sum('total_amount');

        $collectedTotal = (float) Payment::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->sum('amount');

        $outstandingTotal = (float) Invoice::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->where('status', '!=', 'cancelled')
            ->sum('balance_amount');

        $recentAppointments = Appointment::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->with(['doctor', 'patient', 'service'])
            ->latest('appointment_date')
            ->limit(8)
            ->get();

        return view('platform.clinics.show', compact(
            'clinic',
            'staffMembers',
            'invoicedTotal',
            'collectedTotal',
            'outstandingTotal',
            'recentAppointments'
        ));
    }

    /**
     * Update clinic operational status (active, suspended, inactive).
     */
    public function updateStatus(Request $request, Clinic $clinic): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:active,suspended,inactive'],
        ]);

        $clinic->update([
            'status' => $validated['status'],
        ]);

        $statusWord = ucfirst($validated['status']);

        return redirect()->back()
            ->with('status', "Clinic '{$clinic->name}' status updated to {$statusWord}.");
    }

    /**
     * Display comprehensive Platform Telemetry, GTV, and multi-tenant growth metrics.
     */
    public function telemetry(Request $request): View
    {
        $totalClinics = Clinic::count();
        $activeClinics = Clinic::where('status', 'active')->count();
        $suspendedClinics = Clinic::where('status', 'suspended')->count();

        $totalUsers = User::where('role', '!=', 'platform_owner')->count();
        $dentistsCount = User::where('role', 'dentist')->count();
        $adminsCount = User::where('role', 'clinic_admin')->count();
        $receptionistsCount = User::where('role', 'receptionist')->count();

        $totalPatients = Patient::withoutGlobalScopes()->count();
        $totalAppointments = Appointment::withoutGlobalScopes()->count();
        $completedAppointments = Appointment::withoutGlobalScopes()->where('status', 'completed')->count();
        $totalTreatments = TreatmentRecord::withoutGlobalScopes()->count();

        $totalGTV = (float) Invoice::withoutGlobalScopes()->sum('total_amount');
        $totalCollected = (float) Payment::withoutGlobalScopes()->sum('amount');
        $totalReceivables = (float) Invoice::withoutGlobalScopes()->where('status', '!=', 'cancelled')->sum('balance_amount');

        $completionRate = $totalAppointments > 0
            ? round(($completedAppointments / $totalAppointments) * 100, 1)
            : 0;

        // Top tenant clinics leaderboard by appointment volume & patients
        $leaderboardClinics = Clinic::withCount(['appointments', 'patients', 'users'])
            ->orderByDesc('appointments_count')
            ->limit(6)
            ->get();

        return view('platform.telemetry', compact(
            'totalClinics',
            'activeClinics',
            'suspendedClinics',
            'totalUsers',
            'dentistsCount',
            'adminsCount',
            'receptionistsCount',
            'totalPatients',
            'totalAppointments',
            'completedAppointments',
            'totalTreatments',
            'totalGTV',
            'totalCollected',
            'totalReceivables',
            'completionRate',
            'leaderboardClinics'
        ));
    }
}
