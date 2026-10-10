<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClinicSettingController extends Controller
{
    /**
     * Display clinic workspace configuration form.
     */
    public function edit(): View
    {
        $user = Auth::user();
        $clinic = $user->clinic;

        $currencies = [
            'PKR' => 'PKR - Pakistani Rupee',
            'USD' => 'USD - US Dollar',
            'EUR' => 'EUR - Euro',
            'GBP' => 'GBP - British Pound',
            'AED' => 'AED - UAE Dirham',
            'SAR' => 'SAR - Saudi Riyal',
            'CAD' => 'CAD - Canadian Dollar',
            'AUD' => 'AUD - Australian Dollar',
        ];

        $timezones = [
            'Asia/Karachi' => 'Asia/Karachi (PKT +05:00)',
            'Asia/Dubai' => 'Asia/Dubai (GST +04:00)',
            'Asia/Riyadh' => 'Asia/Riyadh (AST +03:00)',
            'UTC' => 'UTC (GMT +00:00)',
            'Europe/London' => 'Europe/London (GMT/BST)',
            'America/New_York' => 'America/New_York (EST/EDT)',
            'America/Los_Angeles' => 'America/Los_Angeles (PST/PDT)',
        ];

        return view('admin.settings.index', compact('clinic', 'user', 'currencies', 'timezones'));
    }

    /**
     * Update clinic workspace settings & public profile info.
     */
    public function update(Request $request): RedirectResponse
    {
        $clinic = Auth::user()->clinic;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:100',
                Rule::unique('clinics')->ignore($clinic->id),
                'regex:/^[a-z0-9-]+$/',
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'emergency_notice' => ['nullable', 'string', 'max:1000'],
            'currency' => ['required', 'string', 'in:PKR,USD,EUR,GBP,AED,SAR,CAD,AUD'],
            'timezone' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ], [
            'slug.regex' => 'The clinic URL slug must contain only lowercase letters, numbers, and hyphens (e.g. city-dental).',
        ]);

        $clinic->update($validated);

        return redirect()->route('admin.settings')
            ->with('status', 'Clinic workspace settings and public portal branding updated successfully.');
    }
}
