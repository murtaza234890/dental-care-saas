<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price_monthly',
        'price_annual',
        'currency',
        'max_doctors',
        'max_staff',
        'max_patients',
        'features',
        'is_active',
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_annual' => 'decimal:2',
        'max_doctors' => 'integer',
        'max_staff' => 'integer',
        'max_patients' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function hasUnlimitedDoctors(): bool
    {
        return $this->max_doctors >= 999;
    }

    public function hasUnlimitedStaff(): bool
    {
        return $this->max_staff >= 999;
    }

    public function hasUnlimitedPatients(): bool
    {
        return $this->max_patients >= 99999;
    }
}
