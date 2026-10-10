<?php

namespace App\Traits;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToClinic
{
    /**
     * Boot the trait to attach global scoping and creating hooks.
     */
    protected static function bootBelongsToClinic(): void
    {
        static::addGlobalScope('clinic_scope', function (Builder $builder) {
            $clinicId = static::resolveCurrentClinicId();

            if ($clinicId !== null) {
                $builder->where($builder->getModel()->getTable() . '.clinic_id', $clinicId);
            }
        });

        static::creating(function (Model $model) {
            if (empty($model->clinic_id)) {
                $clinicId = static::resolveCurrentClinicId();
                if ($clinicId !== null) {
                    $model->clinic_id = $clinicId;
                }
            }
        });
    }

    /**
     * Resolve the current clinic id from session, authenticated user, or app container.
     */
    public static function resolveCurrentClinicId(): ?int
    {
        if (app()->bound('current_clinic_id')) {
            return (int) app('current_clinic_id');
        }

        if (session()->has('current_clinic_id')) {
            return (int) session('current_clinic_id');
        }

        if (auth()->check() && auth()->user()->clinic_id) {
            return (int) auth()->user()->clinic_id;
        }

        return null;
    }

    /**
     * Relationship to the clinic.
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
