<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'phone',
        'whatsapp_number',
        'email',
        'address',
        'emergency_notice',
        'logo_path',
        'currency',
        'timezone',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($clinic) {
            if (empty($clinic->slug)) {
                $clinic->slug = Str::slug($clinic->name);
            }
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'dentist');
    }

    public function doctorProfiles(): HasMany
    {
        return $this->hasMany(DoctorProfile::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(DentalService::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function treatmentRecords(): HasMany
    {
        return $this->hasMany(TreatmentRecord::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function platformInvoices(): HasMany
    {
        return $this->hasMany(PlatformInvoice::class);
    }

    public function currentPlan(): ?SubscriptionPlan
    {
        return $this->subscription?->plan;
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscription ? $this->subscription->isValid() : true;
    }

    public function canAddDoctor(): bool
    {
        $plan = $this->currentPlan();
        if (! $plan) {
            return true; // No plan restriction by default
        }

        if ($plan->hasUnlimitedDoctors()) {
            return true;
        }

        $activeDoctorsCount = $this->doctors()->count();

        return $activeDoctorsCount < $plan->max_doctors;
    }

    public function canAddStaff(): bool
    {
        $plan = $this->currentPlan();
        if (! $plan) {
            return true;
        }

        if ($plan->hasUnlimitedStaff()) {
            return true;
        }

        $staffCount = $this->users()->count();

        return $staffCount < $plan->max_staff;
    }

    public function canAddPatient(): bool
    {
        $plan = $this->currentPlan();
        if (! $plan) {
            return true;
        }

        if ($plan->hasUnlimitedPatients()) {
            return true;
        }

        $patientsCount = $this->patients()->count();

        return $patientsCount < $plan->max_patients;
    }

    public function hasFeature(string $feature): bool
    {
        $plan = $this->currentPlan();
        if (! $plan || empty($plan->features)) {
            return true;
        }

        foreach ($plan->features as $f) {
            if (stripos($f, $feature) !== false) {
                return true;
            }
        }

        return false;
    }
}
