<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Patient;
use App\Models\TreatmentRecord;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    /**
     * Display the clinic follow-up and patient recall directory.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $filter = $request->input('filter', 'all');
        $status = $request->input('status');
        $search = $request->input('search');

        // Scoped automatically to current clinic via BelongsToClinic trait
        $query = FollowUp::with(['patient', 'treatmentRecord.doctor', 'treatmentRecord.service']);

        // Apply tab filters
        if ($filter === 'overdue') {
            $query->where('status', 'pending')->whereDate('due_date', '<', $today);
        } elseif ($filter === 'today') {
            $query->whereDate('due_date', $today)->whereIn('status', ['pending', 'contacted']);
        } elseif ($filter === 'upcoming') {
            $query->whereDate('due_date', '>', $today)
                ->whereDate('due_date', '<=', Carbon::today()->addDays(7)->toDateString())
                ->where('status', 'pending');
        } elseif ($filter === 'contacted') {
            $query->where('status', 'contacted');
        } elseif ($filter === 'booked') {
            $query->where('status', 'booked');
        }

        // Additional status filter dropdown
        if ($status && $filter === 'all') {
            $query->where('status', $status);
        }

        // Search by patient name or phone
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhere('staff_notes', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $followUps = $query->orderBy('due_date', 'asc')->paginate(15)->withQueryString();

        // Metrics for summary cards
        $overdueCount = FollowUp::where('status', 'pending')->whereDate('due_date', '<', $today)->count();
        $dueTodayCount = FollowUp::whereDate('due_date', $today)->whereIn('status', ['pending', 'contacted'])->count();
        $upcomingCount = FollowUp::whereDate('due_date', '>', $today)
            ->whereDate('due_date', '<=', Carbon::today()->addDays(7)->toDateString())
            ->where('status', 'pending')
            ->count();
        $bookedThisMonthCount = FollowUp::where('status', 'booked')
            ->where('updated_at', '>=', Carbon::now()->startOfMonth())
            ->count();

        return view('follow_ups.index', compact(
            'followUps',
            'filter',
            'status',
            'search',
            'overdueCount',
            'dueTodayCount',
            'upcomingCount',
            'bookedThisMonthCount'
        ));
    }

    /**
     * Show form to schedule a new clinical recall or follow-up.
     */
    public function create(Request $request): View
    {
        $user = Auth::user();
        $patientId = $request->input('patient_id');
        $treatmentRecordId = $request->input('treatment_record_id');

        $patients = Patient::where('clinic_id', $user->clinic_id)
            ->orderBy('name')
            ->get();

        $selectedTreatment = null;
        if ($treatmentRecordId) {
            $selectedTreatment = TreatmentRecord::with(['patient', 'service'])->find($treatmentRecordId);
            if ($selectedTreatment) {
                $patientId = $selectedTreatment->patient_id;
            }
        }

        return view('follow_ups.create', compact(
            'patients',
            'patientId',
            'treatmentRecordId',
            'selectedTreatment'
        ));
    }

    /**
     * Store a new follow-up recall task.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'treatment_record_id' => ['nullable', 'exists:treatment_records,id'],
            'due_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:255'],
            'staff_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        FollowUp::create([
            'clinic_id' => $user->clinic_id,
            'patient_id' => $validated['patient_id'],
            'treatment_record_id' => $validated['treatment_record_id'] ?? null,
            'due_date' => $validated['due_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
            'staff_notes' => $validated['staff_notes'] ?? null,
        ]);

        return redirect()->route('follow-ups.index')
            ->with('status', 'Follow-up recall task scheduled successfully.');
    }

    /**
     * Show form to edit an existing follow-up task.
     */
    public function edit(FollowUp $followUp): View
    {
        $followUp->load(['patient', 'treatmentRecord']);

        return view('follow_ups.edit', compact('followUp'));
    }

    /**
     * Update follow-up task details.
     */
    public function update(Request $request, FollowUp $followUp): RedirectResponse
    {
        $validated = $request->validate([
            'due_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:pending,contacted,booked,cancelled'],
            'staff_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['status'] === 'contacted' && ! $followUp->last_contacted_at) {
            $validated['last_contacted_at'] = Carbon::now();
        }

        $followUp->update($validated);

        return redirect()->route('follow-ups.index')
            ->with('status', 'Follow-up recall task updated successfully.');
    }

    /**
     * Quick status update action from list or modals.
     */
    public function updateStatus(Request $request, FollowUp $followUp): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,contacted,booked,cancelled'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $updates = ['status' => $validated['status']];

        if ($validated['status'] === 'contacted') {
            $updates['last_contacted_at'] = Carbon::now();
        }

        if (!empty($validated['notes'])) {
            $timestamp = Carbon::now()->format('d M h:i A');
            $newNote = "[{$timestamp}] " . $validated['notes'];
            $updates['staff_notes'] = $followUp->staff_notes
                ? $followUp->staff_notes . "\n" . $newNote
                : $newNote;
        }

        $followUp->update($updates);

        return back()->with('status', "Follow-up task status updated to {$validated['status']}.");
    }

    /**
     * Delete/archive a follow-up task.
     */
    public function destroy(FollowUp $followUp): RedirectResponse
    {
        $followUp->delete();

        return redirect()->route('follow-ups.index')
            ->with('status', 'Follow-up task dismissed successfully.');
    }
}
