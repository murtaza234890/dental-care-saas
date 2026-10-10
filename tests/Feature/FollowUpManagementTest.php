<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\DentalService;
use App\Models\FollowUp;
use App\Models\Patient;
use App\Models\TreatmentRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowUpManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_view_follow_ups_index_with_metrics(): void
    {
        $clinic = Clinic::create(['name' => 'Islamabad Dental Care', 'slug' => 'isb-dental']);
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Sara Receptionist',
            'email' => 'sara@isbdental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Bilal Ahmed',
            'phone' => '0300-1112233',
        ]);

        // Overdue follow-up
        FollowUp::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'due_date' => Carbon::yesterday(),
            'reason' => 'Suture removal post extraction',
            'status' => 'pending',
        ]);

        // Due today follow-up
        FollowUp::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'due_date' => Carbon::today(),
            'reason' => 'Crown fitting check',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($receptionist)->get('/follow-ups');

        $response->assertStatus(200);
        $response->assertSee('Clinical Recall Queue');
        $response->assertSee('Suture removal post extraction');
        $response->assertSee('Crown fitting check');
        $response->assertSee('Bilal Ahmed');
    }

    public function test_staff_can_filter_follow_ups_by_status_and_overdue(): void
    {
        $clinic = Clinic::create(['name' => 'Islamabad Dental Care', 'slug' => 'isb-dental']);
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Sara Receptionist',
            'email' => 'sara@isbdental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Tariq Mehmood',
            'phone' => '0333-5556677',
        ]);

        $overdueTask = FollowUp::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'due_date' => Carbon::yesterday(),
            'reason' => 'Dry socket checkup',
            'status' => 'pending',
        ]);

        $futureTask = FollowUp::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'due_date' => Carbon::today()->addDays(5),
            'reason' => '6-month dental scaling recall',
            'status' => 'pending',
        ]);

        // Filter: Overdue
        $overdueResponse = $this->actingAs($receptionist)->get('/follow-ups?filter=overdue');
        $overdueResponse->assertStatus(200);
        $overdueResponse->assertSee('Dry socket checkup');
        $overdueResponse->assertDontSee('6-month dental scaling recall');

        // Filter: Upcoming
        $upcomingResponse = $this->actingAs($receptionist)->get('/follow-ups?filter=upcoming');
        $upcomingResponse->assertStatus(200);
        $upcomingResponse->assertSee('6-month dental scaling recall');
        $upcomingResponse->assertDontSee('Dry socket checkup');
    }

    public function test_staff_can_schedule_new_follow_up_task(): void
    {
        $clinic = Clinic::create(['name' => 'Islamabad Dental Care', 'slug' => 'isb-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Haris Malik',
            'email' => 'dr.haris@isbdental.test',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Naveed Akhtar',
            'phone' => '0321-9998877',
        ]);

        $createResponse = $this->actingAs($doctor)->get('/follow-ups/create');
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Schedule Clinical Recall Task');

        $storeResponse = $this->actingAs($doctor)->post('/follow-ups', [
            'patient_id' => $patient->id,
            'due_date' => Carbon::today()->addDays(7)->toDateString(),
            'reason' => 'Permanent crown cementation on tooth #46',
            'staff_notes' => 'Lab technician confirmed crown will arrive by Friday.',
        ]);

        $storeResponse->assertRedirect('/follow-ups');

        $this->assertDatabaseHas('follow_ups', [
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'reason' => 'Permanent crown cementation on tooth #46',
            'status' => 'pending',
        ]);
    }

    public function test_staff_can_update_status_to_contacted_and_booked(): void
    {
        $clinic = Clinic::create(['name' => 'Islamabad Dental Care', 'slug' => 'isb-dental']);
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Sara Receptionist',
            'email' => 'sara@isbdental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Khurram Shehzad',
            'phone' => '0345-1234567',
        ]);

        $followUp = FollowUp::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'due_date' => Carbon::today(),
            'reason' => 'Invisalign tray fitting follow-up',
            'status' => 'pending',
        ]);

        // 1. Mark contacted
        $contactResponse = $this->actingAs($receptionist)->patch("/follow-ups/{$followUp->id}/status", [
            'status' => 'contacted',
            'notes' => 'Patient called, confirmed available on Saturday.',
        ]);
        $contactResponse->assertSessionHas('status');

        $followUp->refresh();
        $this->assertEquals('contacted', $followUp->status);
        $this->assertNotNull($followUp->last_contacted_at);
        $this->assertStringContainsString('Patient called, confirmed available on Saturday.', $followUp->staff_notes);

        // 2. Mark booked
        $bookedResponse = $this->actingAs($receptionist)->patch("/follow-ups/{$followUp->id}/status", [
            'status' => 'booked',
        ]);
        $bookedResponse->assertSessionHas('status');

        $followUp->refresh();
        $this->assertEquals('booked', $followUp->status);
    }

    public function test_staff_can_edit_and_delete_follow_up_task(): void
    {
        $clinic = Clinic::create(['name' => 'Islamabad Dental Care', 'slug' => 'isb-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Haris Malik',
            'email' => 'dr.haris@isbdental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinic->id,
            'name' => 'Imran Farooq',
            'phone' => '0313-8887766',
        ]);

        $followUp = FollowUp::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'due_date' => Carbon::today()->addDays(3),
            'reason' => 'Orthodontic wire activation check',
            'status' => 'pending',
        ]);

        // Edit view
        $editResponse = $this->actingAs($doctor)->get("/follow-ups/{$followUp->id}/edit");
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Edit Follow-Up Recall Task #' . $followUp->id);

        // Update
        $updateResponse = $this->actingAs($doctor)->put("/follow-ups/{$followUp->id}", [
            'due_date' => Carbon::today()->addDays(10)->toDateString(),
            'reason' => 'Postponed: Orthodontic wire activation check',
            'status' => 'pending',
            'staff_notes' => 'Patient travelling abroad until next Monday.',
        ]);
        $updateResponse->assertRedirect('/follow-ups');

        $followUp->refresh();
        $this->assertEquals('Postponed: Orthodontic wire activation check', $followUp->reason);

        // Delete
        $deleteResponse = $this->actingAs($doctor)->delete("/follow-ups/{$followUp->id}");
        $deleteResponse->assertRedirect('/follow-ups');

        $this->assertDatabaseMissing('follow_ups', ['id' => $followUp->id]);
    }

    public function test_multi_tenant_isolation_clinic_a_cannot_view_or_modify_clinic_b_follow_ups(): void
    {
        $clinicA = Clinic::create(['name' => 'Clinic Alpha', 'slug' => 'clinic-alpha']);
        $clinicB = Clinic::create(['name' => 'Clinic Beta', 'slug' => 'clinic-beta']);

        $userA = User::create([
            'clinic_id' => $clinicA->id,
            'name' => 'User Alpha',
            'email' => 'alpha@test.com',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);

        $patientB = Patient::create([
            'clinic_id' => $clinicB->id,
            'name' => 'Beta Patient',
            'phone' => '0300-9999999',
        ]);

        $followUpB = FollowUp::create([
            'clinic_id' => $clinicB->id,
            'patient_id' => $patientB->id,
            'due_date' => Carbon::today(),
            'reason' => 'Beta Clinic Private Recall Task',
            'status' => 'pending',
        ]);

        // User A views index: must not see Clinic B's follow-up
        $indexResponse = $this->actingAs($userA)->get('/follow-ups');
        $indexResponse->assertStatus(200);
        $indexResponse->assertDontSee('Beta Clinic Private Recall Task');

        // User A attempts to edit Clinic B's follow-up: must be 404 (due to BelongsToClinic tenant global scope)
        $editResponse = $this->actingAs($userA)->get("/follow-ups/{$followUpB->id}/edit");
        $editResponse->assertStatus(404);

        // User A attempts to update Clinic B's follow-up: must be 404
        $updateResponse = $this->actingAs($userA)->put("/follow-ups/{$followUpB->id}", [
            'due_date' => Carbon::today()->toDateString(),
            'reason' => 'Hacked reason',
            'status' => 'contacted',
        ]);
        $updateResponse->assertStatus(404);
    }
}
