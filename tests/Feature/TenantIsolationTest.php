<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_isolation_filters_records_by_active_clinic(): void
    {
        // 1. Create two distinct clinics
        $clinicA = Clinic::create(['name' => 'Clinic Alpha', 'slug' => 'clinic-alpha']);
        $clinicB = Clinic::create(['name' => 'Clinic Beta', 'slug' => 'clinic-beta']);

        // 2. Create patient for Clinic A
        app()->instance('current_clinic_id', $clinicA->id);
        $patientA = Patient::create([
            'name' => 'Patient Alpha',
            'phone' => '0300-1111111',
        ]);

        // 3. Create patient for Clinic B
        app()->instance('current_clinic_id', $clinicB->id);
        $patientB = Patient::create([
            'name' => 'Patient Beta',
            'phone' => '0300-2222222',
        ]);

        // 4. Act as Clinic A: Queries must ONLY return Patient Alpha
        app()->instance('current_clinic_id', $clinicA->id);
        $clinicAPatients = Patient::all();

        $this->assertCount(1, $clinicAPatients);
        $this->assertTrue($clinicAPatients->contains('id', $patientA->id));
        $this->assertFalse($clinicAPatients->contains('id', $patientB->id));

        // 5. Act as Clinic B: Queries must ONLY return Patient Beta
        app()->instance('current_clinic_id', $clinicB->id);
        $clinicBPatients = Patient::all();

        $this->assertCount(1, $clinicBPatients);
        $this->assertTrue($clinicBPatients->contains('id', $patientB->id));
        $this->assertFalse($clinicBPatients->contains('id', $patientA->id));
    }
}
