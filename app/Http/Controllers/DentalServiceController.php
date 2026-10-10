<?php

namespace App\Http\Controllers;

use App\Models\DentalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DentalServiceController extends Controller
{
    /**
     * Display dental services catalog with category tabs, search, and pricing engine KPIs.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $clinicId = $user->clinic_id;

        $categories = DentalService::CATEGORIES;

        $selectedCategory = $request->query('category', 'all');
        $selectedStatus = $request->query('status', 'all');
        $searchQuery = trim($request->query('search', ''));

        $query = DentalService::where('clinic_id', $clinicId);

        if ($selectedCategory !== 'all' && in_array($selectedCategory, $categories)) {
            $query->where('category', $selectedCategory);
        }

        if ($selectedStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($selectedStatus === 'inactive') {
            $query->where('is_active', false);
        }

        if (! empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                  ->orWhere('description', 'like', "%{$searchQuery}%");
            });
        }

        $services = $query->orderBy('category')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // Financial & Procedure KPIs scoped to clinic
        $totalServices = DentalService::where('clinic_id', $clinicId)->count();
        $activeServices = DentalService::where('clinic_id', $clinicId)->where('is_active', true)->count();
        $averageFee = DentalService::where('clinic_id', $clinicId)->where('is_active', true)->avg('standard_fee') ?? 0;
        
        $categoryBreakdown = DentalService::where('clinic_id', $clinicId)
            ->selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $metrics = [
            'total' => $totalServices,
            'active' => $activeServices,
            'inactive' => $totalServices - $activeServices,
            'average_fee' => (float) $averageFee,
            'breakdown' => $categoryBreakdown,
        ];

        return view('services.index', compact(
            'services',
            'categories',
            'metrics',
            'user',
            'selectedCategory',
            'selectedStatus',
            'searchQuery'
        ));
    }

    /**
     * Show form to create a new procedure with 1-click clinical presets.
     */
    public function create(): View
    {
        $user = Auth::user();

        if (! $user->isClinicAdmin()) {
            abort(403, 'Unauthorized. Only Clinic Administrators can add procedures to the catalog.');
        }

        $categories = DentalService::CATEGORIES;

        // Clinical preset quick-starters
        $presets = [
            [
                'name' => 'Scaling & Polishing (Teeth Cleaning)',
                'category' => 'Preventive',
                'fee' => 3500,
                'duration' => 30,
                'description' => 'Ultrasonic scaling followed by prophylaxis paste polishing to remove plaque and tartar.',
            ],
            [
                'name' => 'Composite Tooth Restoration (Filling)',
                'category' => 'Restorative',
                'fee' => 4500,
                'duration' => 30,
                'description' => 'Light-cured tooth-colored composite resin restoration for anterior or posterior teeth.',
            ],
            [
                'name' => 'Root Canal Treatment (Molar Multi-Canal)',
                'category' => 'Endodontics',
                'fee' => 16000,
                'duration' => 60,
                'description' => 'Complete chemo-mechanical instrumentation, pulp extirpation and bio-ceramic obturation.',
            ],
            [
                'name' => 'Monolithic Zirconia Crown',
                'category' => 'Prosthodontics',
                'fee' => 22000,
                'duration' => 30,
                'description' => 'High-strength CAD/CAM milled full contour monolithic zirconia ceramic crown.',
            ],
            [
                'name' => 'Surgical Wisdom Tooth Extraction',
                'category' => 'Oral Surgery',
                'fee' => 18000,
                'duration' => 45,
                'description' => 'Surgical removal of impacted third molar with osteotomy, sectioning, and suturing.',
            ],
            [
                'name' => 'Simple Dental Extraction',
                'category' => 'Oral Surgery',
                'fee' => 5000,
                'duration' => 20,
                'description' => 'Uncomplicated non-surgical forceps extraction under profound local anaesthesia.',
            ],
            [
                'name' => 'In-Office LED Laser Teeth Whitening',
                'category' => 'Cosmetic',
                'fee' => 25000,
                'duration' => 60,
                'description' => 'Professional high-concentration chairside peroxide whitening with gingival barrier.',
            ],
            [
                'name' => 'Orthodontic Braces Diagnostic Assessment',
                'category' => 'Orthodontics',
                'fee' => 3000,
                'duration' => 30,
                'description' => 'Comprehensive malocclusion assessment, intraoral photography and treatment proposal.',
            ],
            [
                'name' => 'Periodontal Deep Root Planing (Per Quadrant)',
                'category' => 'Periodontics',
                'fee' => 6000,
                'duration' => 40,
                'description' => 'Deep subgingival scaling and root debridement for moderate-to-severe periodontal disease.',
            ],
        ];

        return view('services.create', compact('categories', 'presets'));
    }

    /**
     * Store newly created dental service procedure in the catalog.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isClinicAdmin()) {
            abort(403, 'Unauthorized. Only Clinic Administrators can add procedures to the catalog.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(DentalService::CATEGORIES)],
            'standard_fee' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'estimated_duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $service = DentalService::create([
            'clinic_id' => $user->clinic_id,
            'name' => $validated['name'],
            'category' => $validated['category'],
            'standard_fee' => $validated['standard_fee'],
            'estimated_duration_minutes' => $validated['estimated_duration_minutes'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('services.index')
            ->with('status', "Dental procedure '{$service->name}' added to catalog successfully.");
    }

    /**
     * Show form to edit an existing procedure.
     */
    public function edit(DentalService $service): View
    {
        $user = Auth::user();

        if (! $user->isClinicAdmin()) {
            abort(403, 'Unauthorized. Only Clinic Administrators can edit procedure catalog.');
        }

        if ($service->clinic_id !== $user->clinic_id) {
            abort(404);
        }

        $categories = DentalService::CATEGORIES;
        $isReferenced = $service->isReferenced();

        return view('services.edit', compact('service', 'categories', 'isReferenced'));
    }

    /**
     * Update an existing procedure's details and pricing.
     */
    public function update(Request $request, DentalService $service): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isClinicAdmin()) {
            abort(403, 'Unauthorized. Only Clinic Administrators can edit procedure catalog.');
        }

        if ($service->clinic_id !== $user->clinic_id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(DentalService::CATEGORIES)],
            'standard_fee' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'estimated_duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $service->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'standard_fee' => $validated['standard_fee'],
            'estimated_duration_minutes' => $validated['estimated_duration_minutes'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', false),
        ]);

        return redirect()->route('services.index')
            ->with('status', "Dental procedure '{$service->name}' updated successfully.");
    }

    /**
     * Toggle active/inactive status of a service.
     */
    public function toggleStatus(DentalService $service): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isClinicAdmin()) {
            abort(403, 'Unauthorized. Only Clinic Administrators can change procedure availability.');
        }

        if ($service->clinic_id !== $user->clinic_id) {
            abort(404);
        }

        $service->update([
            'is_active' => ! $service->is_active,
        ]);

        $state = $service->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('status', "Procedure '{$service->name}' has been {$state}.");
    }

    /**
     * Remove or safely deactivate the specified procedure.
     */
    public function destroy(DentalService $service): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isClinicAdmin()) {
            abort(403, 'Unauthorized. Only Clinic Administrators can delete procedures.');
        }

        if ($service->clinic_id !== $user->clinic_id) {
            abort(404);
        }

        if ($service->isReferenced()) {
            // Protect historical financial and clinical records by soft-deactivating
            $service->update(['is_active' => false]);

            return redirect()->route('services.index')
                ->with('status', "Procedure '{$service->name}' is linked to historical appointment, invoice, or clinical records. It has been deactivated instead of deleted to protect financial and clinical history.");
        }

        $name = $service->name;
        $service->delete();

        return redirect()->route('services.index')
            ->with('status', "Procedure '{$name}' permanently removed from the catalog.");
    }
}
