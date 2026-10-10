<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'subscription_id',
        'invoice_number',
        'amount',
        'currency',
        'status',
        'billing_period_start',
        'billing_period_end',
        'paid_at',
        'payment_reference',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'paid_at' => 'datetime',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
