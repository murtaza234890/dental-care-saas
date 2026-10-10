<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'appointment_id',
        'invoice_number',
        'total_amount',
        'discount_amount',
        'paid_amount',
        'balance_amount',
        'status',
        'due_date',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Recalculate totals and balance based on items and recorded payments.
     */
    public function recalculateFinancials(): void
    {
        $subtotal = (float) $this->items()->sum('total_price');
        $discount = (float) $this->discount_amount;
        $total = max(0, $subtotal - $discount);
        $paid = (float) $this->payments()->sum('amount');
        $balance = max(0, $total - $paid);

        $status = $this->status === 'cancelled' ? 'cancelled' : 'unpaid';
        if ($this->status !== 'cancelled') {
            if ($balance <= 0) {
                $status = 'paid';
            } elseif ($paid > 0 && $balance > 0) {
                $status = 'partially_paid';
            } else {
                $status = 'unpaid';
            }
        }

        $this->update([
            'total_amount' => $total,
            'paid_amount' => $paid,
            'balance_amount' => $balance,
            'status' => $status,
        ]);
    }
}
