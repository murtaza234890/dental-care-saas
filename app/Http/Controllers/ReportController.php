<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\TreatmentRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the Executive Financial Analytics & Clinic Performance Dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        if (! $user->isClinicAdmin()) {
            abort(403, 'Unauthorized. Financial reports and revenue telemetry are restricted to Clinic Administrators.');
        }

        $data = $this->prepareReportData($request);

        return view('reports.index', $data);
    }

    /**
     * Display clean printable version of the financial & performance report.
     */
    public function printReport(Request $request): View
    {
        $user = Auth::user();

        if (! $user->isClinicAdmin()) {
            abort(403, 'Unauthorized. Financial reports and revenue telemetry are restricted to Clinic Administrators.');
        }

        $data = $this->prepareReportData($request);

        return view('reports.print', $data);
    }

    /**
     * Compute and aggregate clinic financial and clinical productivity metrics.
     */
    private function prepareReportData(Request $request): array
    {
        $user = Auth::user();
        $clinic = $user->clinic;
        $clinicId = $user->clinic_id;

        $range = $request->query('range', 'this_month');
        $now = Carbon::now();

        switch ($range) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $rangeLabel = 'Today (' . $now->format('d M Y') . ')';
                break;
            case 'this_week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                $rangeLabel = 'This Week (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
                break;
            case 'last_month':
                $startDate = $now->copy()->subMonth()->startOfMonth();
                $endDate = $now->copy()->subMonth()->endOfMonth();
                $rangeLabel = 'Last Month (' . $startDate->format('F Y') . ')';
                break;
            case 'this_year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                $rangeLabel = 'Year to Date (' . $startDate->format('Y') . ')';
                break;
            case 'custom':
                $startDate = $request->filled('start_date')
                    ? Carbon::parse($request->start_date)->startOfDay()
                    : $now->copy()->startOfMonth();
                $endDate = $request->filled('end_date')
                    ? Carbon::parse($request->end_date)->endOfDay()
                    : $now->copy()->endOfDay();
                $rangeLabel = 'Custom Range (' . $startDate->format('d M Y') . ' to ' . $endDate->format('d M Y') . ')';
                break;
            case 'this_month':
            default:
                $range = 'this_month';
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $rangeLabel = 'This Month (' . $startDate->format('F Y') . ')';
                break;
        }

        // 1. Core Financial Revenue Metrics
        $totalInvoiced = (float) Invoice::where('clinic_id', $clinicId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $totalCollected = (float) Payment::where('clinic_id', $clinicId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        $totalDiscounts = (float) Invoice::where('clinic_id', $clinicId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('discount_amount');

        $periodOutstanding = (float) Invoice::where('clinic_id', $clinicId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->sum('balance_amount');

        $totalClinicReceivables = (float) Invoice::where('clinic_id', $clinicId)
            ->where('status', '!=', 'cancelled')
            ->sum('balance_amount');

        $collectionRate = $totalInvoiced > 0
            ? round(($totalCollected / $totalInvoiced) * 100, 1)
            : ($totalCollected > 0 ? 100.0 : 0.0);

        // Invoices status breakdown
        $invoicesCount = Invoice::where('clinic_id', $clinicId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $paidInvoicesCount = Invoice::where('clinic_id', $clinicId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'paid')
            ->count();

        $partialInvoicesCount = Invoice::where('clinic_id', $clinicId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'partially_paid')
            ->count();

        $unpaidInvoicesCount = Invoice::where('clinic_id', $clinicId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'unpaid')
            ->count();

        // 2. Appointments & Chair Productivity
        $startDateStr = $startDate->toDateString();
        $endDateStr = $endDate->toDateString();

        $totalAppointments = Appointment::where('clinic_id', $clinicId)
            ->whereBetween('appointment_date', [$startDateStr, $endDateStr])
            ->count();

        $completedAppointments = Appointment::where('clinic_id', $clinicId)
            ->whereBetween('appointment_date', [$startDateStr, $endDateStr])
            ->where('status', 'completed')
            ->count();

        $cancelledAppointments = Appointment::where('clinic_id', $clinicId)
            ->whereBetween('appointment_date', [$startDateStr, $endDateStr])
            ->whereIn('status', ['cancelled', 'no_show'])
            ->count();

        $appointmentCompletionRate = $totalAppointments > 0
            ? round(($completedAppointments / $totalAppointments) * 100, 1)
            : 0.0;

        // 3. Payment Methods Distribution
        $paymentsByMethod = Payment::where('clinic_id', $clinicId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('payment_method, sum(amount) as total_amount, count(*) as count')
            ->groupBy('payment_method')
            ->orderByDesc('total_amount')
            ->get();

        // 4. Doctor Productivity & Yield Analysis
        $doctors = User::where('clinic_id', $clinicId)
            ->whereIn('role', ['dentist', 'clinic_admin'])
            ->with(['doctorProfile'])
            ->get();

        $doctorPerformance = [];
        foreach ($doctors as $doc) {
            $docAppts = Appointment::where('clinic_id', $clinicId)
                ->where('doctor_id', $doc->id)
                ->whereBetween('appointment_date', [$startDateStr, $endDateStr])
                ->count();

            $docCompletedAppts = Appointment::where('clinic_id', $clinicId)
                ->where('doctor_id', $doc->id)
                ->whereBetween('appointment_date', [$startDateStr, $endDateStr])
                ->where('status', 'completed')
                ->count();

            $docTreatments = TreatmentRecord::where('clinic_id', $clinicId)
                ->where('doctor_id', $doc->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $docInvoiced = (float) Invoice::where('clinic_id', $clinicId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('appointment', fn ($q) => $q->where('doctor_id', $doc->id))
                ->sum('total_amount');

            $docCollected = (float) Payment::where('clinic_id', $clinicId)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->whereHas('invoice.appointment', fn ($q) => $q->where('doctor_id', $doc->id))
                ->sum('amount');

            $doctorPerformance[] = [
                'doctor' => $doc,
                'specialization' => $doc->doctorProfile->specialization ?? 'General Dental Practitioner',
                'consultation_fee' => (float) ($doc->doctorProfile->consultation_fee ?? 0),
                'appointments_count' => $docAppts,
                'completed_appointments_count' => $docCompletedAppts,
                'treatments_count' => $docTreatments,
                'invoiced_amount' => $docInvoiced,
                'collected_amount' => $docCollected,
            ];
        }

        // Sort doctors by revenue descending
        usort($doctorPerformance, fn ($a, $b) => $b['collected_amount'] <=> $a['collected_amount']);

        // 5. Specialty Categories & Top Billed Procedures
        $categoryBreakdown = InvoiceItem::join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->leftJoin('dental_services', 'invoice_items.service_id', '=', 'dental_services.id')
            ->where('invoices.clinic_id', $clinicId)
            ->whereBetween('invoices.created_at', [$startDate, $endDate])
            ->selectRaw('COALESCE(dental_services.category, "General") as category_name, sum(invoice_items.total_price) as total_revenue, sum(invoice_items.quantity) as total_count')
            ->groupBy('category_name')
            ->orderByDesc('total_revenue')
            ->get();

        $topProcedures = InvoiceItem::join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->leftJoin('dental_services', 'invoice_items.service_id', '=', 'dental_services.id')
            ->where('invoices.clinic_id', $clinicId)
            ->whereBetween('invoices.created_at', [$startDate, $endDate])
            ->selectRaw('invoice_items.description as procedure_name, COALESCE(dental_services.category, "General") as category_name, sum(invoice_items.total_price) as total_revenue, sum(invoice_items.quantity) as total_count')
            ->groupBy('invoice_items.description', 'category_name')
            ->orderByDesc('total_revenue')
            ->limit(8)
            ->get();

        // 6. Recent Payment Transactions Ledger in Selected Period
        $recentTransactions = Payment::where('clinic_id', $clinicId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->with(['patient', 'invoice', 'receivedBy'])
            ->orderByDesc('payment_date')
            ->limit(15)
            ->get();

        return compact(
            'user',
            'clinic',
            'range',
            'rangeLabel',
            'startDate',
            'endDate',
            'totalInvoiced',
            'totalCollected',
            'totalDiscounts',
            'periodOutstanding',
            'totalClinicReceivables',
            'collectionRate',
            'invoicesCount',
            'paidInvoicesCount',
            'partialInvoicesCount',
            'unpaidInvoicesCount',
            'totalAppointments',
            'completedAppointments',
            'cancelledAppointments',
            'appointmentCompletionRate',
            'paymentsByMethod',
            'doctorPerformance',
            'categoryBreakdown',
            'topProcedures',
            'recentTransactions'
        );
    }
}
