<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientDocument extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'uploaded_by_user_id',
        'title',
        'document_type',
        'tooth_number',
        'notes',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
    ];

    /**
     * The patient this document belongs to.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * User/Doctor who uploaded the document.
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    /**
     * Clinic relationship.
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Format file size for human display.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;

        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        }

        return round($bytes / 1048576, 2) . ' MB';
    }

    /**
     * Check if the document is an image (X-ray, clinical photo).
     */
    public function getIsImageAttribute(): bool
    {
        return in_array($this->mime_type, [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
        ]);
    }

    /**
     * Check if document is a PDF.
     */
    public function getIsPdfAttribute(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Human readable type badge.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->document_type) {
            'xray' => 'Dental X-Ray',
            'lab_report' => 'Lab Report',
            'photo' => 'Clinical Photo',
            'prescription' => 'Prescription Scan',
            default => 'Clinical Document',
        };
    }
}
