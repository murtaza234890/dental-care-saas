<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\FollowUp;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\TreatmentRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the authenticated role-specific dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');

        // Platform Owner Dashboard View
        if ($user->isPlatformOwner()) {
            $totalClinics = Clinic::count();
            $activeClinics = Clinic::where('status', 'active')->count();
            $recentClinics = Clinic::latest()->take(5)->get();

            return view('dashboard.platform-owner', compact(
                'user',
                'totalClinics',
                'activeClinics',
                'recentClinics'
            ));
        }

        // Clinic Staff / Doctor / Receptionist Dashboard View
        $clinic = $user->clinic;

        // Scoped to clinic automatically via BelongsToClinic trait
        $todayAppointmentsQuery = Appointment::with(['patient', 'doctor', 'service'])
            ->where('appointment_date', $today);

        // If logged in as Dentist, filter appointments to their own
        if ($user->isDentist()) {
            $todayAppointmentsQuery->where('doctor_id', $user->id);
        }

        $todayAppointments = $todayAppointmentsQuery->orderBy('starts_at')->get();
        $totalPatients = Patient::count();

        // Financial Metrics
        $todayCollections = Payment::whereDate('payment_date', $today)->sum('amount');
        $totalOutstandingBalance = Invoice::sum('balance_amount');

        // Follow-ups due
        $pendingFollowUpsCount = FollowUp::where('status', 'pending')
            ->whereDate('due_date', '<=', Carbon::today()->addDays(7))
            ->count();

        $todayFollowUps = FollowUp::with('patient')
            ->whereDate('due_date', '<=', Carbon::today())
            ->whereIn('status', ['pending', 'contacted'])
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $recentTreatments = [];
        if (! $user->isReceptionist()) {
            $recentTreatments = TreatmentRecord::with(['patient', 'doctor', 'service'])
                ->latest()
                ->take(5)
                ->get();
        }

        return view('dashboard.index', compact(
            'user',
            'clinic',
            'todayAppointments',
            'totalPatients',
            'todayCollections',
            'totalOutstandingBalance',
            'pendingFollowUpsCount',
            'todayFollowUps',
            'recentTreatments'
        ));
    }
}
