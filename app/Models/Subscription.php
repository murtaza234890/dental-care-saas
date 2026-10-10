<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'plan_id',
        'status',
        'billing_interval',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'cancelled_at',
        'payment_method',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(PlatformInvoice::class, 'subscription_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && ($this->current_period_end === null || $this->current_period_end->isFuture());
    }

    public function isTrialing(): bool
    {
        return $this->status === 'trialing' && ($this->trial_ends_at === null || $this->trial_ends_at->isFuture());
    }

    public function isPastDue(): bool
    {
        return $this->status === 'past_due';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isInGracePeriod(): bool
    {
        if ($this->status === 'past_due' && $this->current_period_end) {
            return $this->current_period_end->diffInDays(now()) <= 7;
        }

        if ($this->status === 'trialing' && $this->trial_ends_at && $this->trial_ends_at->isPast()) {
            return $this->trial_ends_at->diffInDays(now()) <= 3;
        }

        return false;
    }

    public function isValid(): bool
    {
        return $this->isActive() || $this->isTrialing() || $this->isInGracePeriod();
    }

    public function daysLeftInTrial(): int
    {
        if (! $this->trial_ends_at || $this->trial_ends_at->isPast()) {
            return 0;
        }

        return (int) now()->diffInDays($this->trial_ends_at, false) + 1;
    }

    public function daysLeftInPeriod(): int
    {
        if (! $this->current_period_end || $this->current_period_end->isPast()) {
            return 0;
        }

        return (int) now()->diffInDays($this->current_period_end, false) + 1;
    }
}
