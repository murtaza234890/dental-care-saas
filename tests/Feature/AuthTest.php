<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('DentalCare');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $clinic = Clinic::create(['name' => 'Demo Dental', 'slug' => 'demo-dental']);
        $user = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Test',
            'email' => 'dr.test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'dr.test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $clinic = Clinic::create(['name' => 'Demo Dental', 'slug' => 'demo-dental']);
        User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Dr. Test',
            'email' => 'dr.test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'clinic_admin',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'dr.test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_users_cannot_log_in(): void
    {
        $clinic = Clinic::create(['name' => 'Demo Dental', 'slug' => 'demo-dental']);
        User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Deactivated User',
            'email' => 'inactive@example.com',
            'password' => Hash::make('password123'),
            'role' => 'dentist',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ]);

        $this->assertGuest();
    }

    public function test_clinic_registration_creates_clinic_and_owner_user(): void
    {
        $response = $this->post('/register-clinic', [
            'clinic_name' => 'Islamabad Smile Studio',
            'clinic_phone' => '0300-9998877',
            'address' => 'Blue Area, Islamabad',
            'owner_name' => 'Dr. Usman Qureshi',
            'email' => 'usman@smilestudio.pk',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('clinics', [
            'name' => 'Islamabad Smile Studio',
            'slug' => 'islamabad-smile-studio',
            'phone' => '0300-9998877',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'usman@smilestudio.pk',
            'role' => 'clinic_admin',
        ]);

        $this->assertAuthenticated();
    }

    public function test_role_middleware_restricts_unauthorized_access(): void
    {
        $clinic = Clinic::create(['name' => 'Demo Dental', 'slug' => 'demo-dental']);
        $receptionist = User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Receptionist User',
            'email' => 'reception@example.com',
            'password' => Hash::make('password123'),
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        // Receptionist attempting to access admin-only settings route
        $response = $this->actingAs($receptionist)->get('/admin/settings');

        $response->assertStatus(403);
    }
}
