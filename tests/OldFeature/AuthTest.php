<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, CreatesTestFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFixtures();
    }

    public function test_login_form_renders()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_admin_can_login_and_redirect_to_admin_dashboard()
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->adminUser);
    }

    public function test_employee_can_login_and_redirect_to_employee_dashboard()
    {
        $response = $this->post('/login', [
            'email' => 'employee@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('employee.dashboard'));
        $this->assertAuthenticatedAs($this->employeeUser);
    }

    public function test_user_can_login_and_redirect_to_user_dashboard()
    {
        $response = $this->post('/login', [
            'email' => 'user@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($this->normalUser);
    }

    public function test_login_with_invalid_credentials_fails()
    {
        $response = $this->post('/login', [
            'email' => 'user@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_with_non_existent_email_fails()
    {
        $response = $this->post('/login', [
            'email' => 'nobody@test.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_visiting_login_redirects_to_root()
    {
        $response = $this->actingAs($this->normalUser)->get('/login');
        $response->assertRedirect('/');
    }

    public function test_logout_ends_session_and_redirects_to_login()
    {
        $response = $this->actingAs($this->normalUser)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_registration_with_duplicate_email_fails()
    {
        $response = $this->post('/register', [
            'name' => 'Another User',
            'email' => 'user@test.com',
            'phone' => '0811111111',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_with_password_mismatch_fails()
    {
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'new@test.com',
            'phone' => '0811111111',
            'password' => 'password123',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_with_short_password_fails()
    {
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'new@test.com',
            'phone' => '0811111111',
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_register_form_renders()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_authenticated_user_visiting_register_redirects_to_root()
    {
        $response = $this->actingAs($this->normalUser)->get('/register');
        $response->assertRedirect('/');
    }
}
