<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorScheduleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_view_doctors_directory_and_weekly_roster(): void
    {
        $clinic = Clinic::create(['name' => 'Apex Dental Institute', 'slug' => 'apex-dental']);
        $adminDoctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Kamran Siddiqui',
            'email' => 'dr.kamran@apexdental.test',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'clinic_id' => $clinic->id,
            'user_id' => $adminDoctor->id,
            'specialization' => 'Orthodontist',
            'consultation_fee' => 2500,
            'slot_duration_minutes' => 30,
            'working_days' => ['monday', 'wednesday', 'friday'],
            'start_time' => '11:00:00',
            'end_time' => '19:00:00',
        ]);

        $response = $this->actingAs($adminDoctor)->get('/doctors');

        $response->assertStatus(200);
        $response->assertSee('Doctor Schedules &amp; Chair Roster', false);
        $response->assertSee('Dr. Kamran Siddiqui');
        $response->assertSee('Orthodontist');
        $response->assertSee('Rs. 2,500');
        $response->assertSee('Weekly Clinic Chair Roster Timetable');
    }

    public function test_clinic_admin_can_onboard_new_dentist_with_custom_schedule(): void
    {
        $clinic = Clinic::create(['name' => 'Apex Dental Institute', 'slug' => 'apex-dental']);
        $adminDoctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Kamran Siddiqui',
            'email' => 'dr.kamran@apexdental.test',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);

        $createResponse = $this->actingAs($adminDoctor)->get('/doctors/create');
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Onboard New Dental Surgeon');

        $storeResponse = $this->actingAs($adminDoctor)->post('/doctors', [
            'name' => 'Dr. Fatima Noor',
            'email' => 'dr.fatima@apexdental.test',
            'phone' => '0300-9876543',
            'password' => 'secret123',
            'specialization' => 'Pediatric Dentist',
            'consultation_fee' => 1800,
            'slot_duration_minutes' => 20,
            'bio' => 'BDS, Specialist in child dental health and painless procedures.',
            'working_days' => ['tuesday', 'thursday', 'saturday'],
            'start_time' => '14:00',
            'end_time' => '21:00',
            'break_start' => '17:00',
            'break_end' => '17:30',
        ]);

        $storeResponse->assertRedirect('/doctors');

        $this->assertDatabaseHas('users', [
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Fatima Noor',
            'email' => 'dr.fatima@apexdental.test',
            'role' => 'dentist',
        ]);

        $newDoctor = User::where('email', 'dr.fatima@apexdental.test')->first();

        $this->assertDatabaseHas('doctor_profiles', [
            'clinic_id' => $clinic->id,
            'user_id' => $newDoctor->id,
            'specialization' => 'Pediatric Dentist',
            'consultation_fee' => 1800,
            'slot_duration_minutes' => 20,
            'start_time' => '14:00:00',
            'end_time' => '21:00:00',
        ]);
    }

    public function test_receptionist_cannot_onboard_new_doctors(): void
    {
        $clinic = Clinic::create(['name' => 'Apex Dental Institute', 'slug' => 'apex-dental']);
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Hina Reception',
            'email' => 'hina@apexdental.test',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $createResponse = $this->actingAs($receptionist)->get('/doctors/create');
        $createResponse->assertStatus(403);

        $storeResponse = $this->actingAs($receptionist)->post('/doctors', [
            'name' => 'Dr. Unauthorized',
            'email' => 'unauth@test.com',
            'password' => 'secret123',
            'specialization' => 'Dentist',
            'consultation_fee' => 1000,
            'slot_duration_minutes' => 30,
            'working_days' => ['monday'],
            'start_time' => '10:00',
            'end_time' => '18:00',
        ]);
        $storeResponse->assertStatus(403);
    }

    public function test_doctor_can_update_their_own_schedule_and_chair_hours(): void
    {
        $clinic = Clinic::create(['name' => 'Apex Dental Institute', 'slug' => 'apex-dental']);
        $doctor = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Tariq Jamil',
            'email' => 'dr.tariq@apexdental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $profile = DoctorProfile::create([
            'clinic_id' => $clinic->id,
            'user_id' => $doctor->id,
            'specialization' => 'Endodontist',
            'consultation_fee' => 2000,
            'slot_duration_minutes' => 30,
            'working_days' => ['monday', 'tuesday'],
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
        ]);

        $editResponse = $this->actingAs($doctor)->get("/doctors/{$doctor->id}/edit");
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Edit Schedule');
        $editResponse->assertSee('Dr. Tariq Jamil');

        $updateResponse = $this->actingAs($doctor)->put("/doctors/{$doctor->id}", [
            'name' => 'Dr. Tariq Jamil (Updated)',
            'email' => 'dr.tariq@apexdental.test',
            'specialization' => 'Senior Endodontist & Micro-Dentist',
            'consultation_fee' => 3000,
            'slot_duration_minutes' => 45,
            'working_days' => ['monday', 'wednesday', 'friday'],
            'start_time' => '12:00',
            'end_time' => '20:00',
            'break_start' => '15:00',
            'break_end' => '16:00',
        ]);

        $updateResponse->assertRedirect('/doctors');

        $profile->refresh();
        $this->assertEquals('Senior Endodontist & Micro-Dentist', $profile->specialization);
        $this->assertEquals(3000, $profile->consultation_fee);
        $this->assertEquals(45, $profile->slot_duration_minutes);
        $this->assertEquals(['monday', 'wednesday', 'friday'], $profile->working_days);
        $this->assertEquals('12:00:00', $profile->start_time);
        $this->assertEquals('20:00:00', $profile->end_time);
    }

    public function test_dentist_cannot_modify_another_doctors_schedule(): void
    {
        $clinic = Clinic::create(['name' => 'Apex Dental Institute', 'slug' => 'apex-dental']);
        $doctor1 = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. One',
            'email' => 'dr.one@apexdental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $doctor2 = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Two',
            'email' => 'dr.two@apexdental.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'clinic_id' => $clinic->id,
            'user_id' => $doctor2->id,
            'specialization' => 'Dentist',
            'consultation_fee' => 1000,
            'slot_duration_minutes' => 30,
            'working_days' => ['monday'],
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
        ]);

        // Doctor 1 tries to edit Doctor 2: must receive 403 Forbidden
        $editResponse = $this->actingAs($doctor1)->get("/doctors/{$doctor2->id}/edit");
        $editResponse->assertStatus(403);

        $updateResponse = $this->actingAs($doctor1)->put("/doctors/{$doctor2->id}", [
            'name' => 'Hacked Name',
            'email' => 'dr.two@apexdental.test',
            'specialization' => 'Hacked',
            'consultation_fee' => 0,
            'slot_duration_minutes' => 30,
            'working_days' => ['monday'],
            'start_time' => '10:00',
            'end_time' => '18:00',
        ]);
        $updateResponse->assertStatus(403);
    }

    public function test_multi_tenant_isolation_clinic_a_cannot_view_or_edit_clinic_b_doctor(): void
    {
        $clinicA = Clinic::create(['name' => 'Clinic Alpha', 'slug' => 'clinic-alpha']);
        $clinicB = Clinic::create(['name' => 'Clinic Beta', 'slug' => 'clinic-beta']);

        $adminA = User::create([
            'clinic_id' => $clinicA->id,
            'name' => 'Admin Alpha',
            'email' => 'admin@alpha.test',
            'password' => bcrypt('password'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);

        $doctorB = User::create([
            'clinic_id' => $clinicB->id,
            'name' => 'Dr. Beta Doctor',
            'email' => 'dr.beta@beta.test',
            'password' => bcrypt('password'),
            'role' => 'dentist',
            'is_active' => true,
        ]);

        DoctorProfile::create([
            'clinic_id' => $clinicB->id,
            'user_id' => $doctorB->id,
            'specialization' => 'Beta Specialist',
            'consultation_fee' => 2000,
            'slot_duration_minutes' => 30,
            'working_days' => ['monday'],
            'start_time' => '10:00:00',
            'end_time' => '18:00:00',
        ]);

        // Admin A visits /doctors: must not see Dr. Beta
        $indexResponse = $this->actingAs($adminA)->get('/doctors');
        $indexResponse->assertStatus(200);
        $indexResponse->assertDontSee('Dr. Beta Doctor');

        // Admin A visits /doctors/{doctorB->id}: must be 404
        $showResponse = $this->actingAs($adminA)->get("/doctors/{$doctorB->id}");
        $showResponse->assertStatus(404);

        // Admin A visits /doctors/{doctorB->id}/edit: must be 404
        $editResponse = $this->actingAs($adminA)->get("/doctors/{$doctorB->id}/edit");
        $editResponse->assertStatus(404);
    }
}
