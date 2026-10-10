<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DentalService extends Model
{
    use HasFactory, BelongsToClinic;

    protected $table = 'dental_services';

    protected $fillable = [
        'clinic_id',
        'name',
        'description',
        'standard_fee',
        'estimated_duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'standard_fee' => 'decimal:2',
        'estimated_duration_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'service_id');
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'service_id');
    }
}
