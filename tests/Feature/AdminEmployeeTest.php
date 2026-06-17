<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminEmployeeTest extends TestCase
{
    use RefreshDatabase, CreatesTestFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFixtures();
    }

    public function test_index_lists_employees()
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/employees');
        $response->assertStatus(200);
        $response->assertViewIs('admin.employees.index');
        $response->assertViewHas('employees');
        $response->assertSee('Employee Test');
    }

    public function test_create_form_renders()
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/employees/create');
        $response->assertStatus(200);
        $response->assertViewIs('admin.employees.create');
    }

    public function test_store_creates_employee_and_redirects()
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/employees', [
            'name' => 'New Employee',
            'email' => 'newemployee@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.employees.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'newemployee@test.com',
            'role_id' => $this->employeeRole->id,
        ]);
    }

    public function test_store_with_duplicate_email_fails()
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/employees', [
            'name' => 'Another Employee',
            'email' => 'employee@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_store_with_short_password_fails()
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/employees', [
            'name' => 'New Employee',
            'email' => 'newemployee@test.com',
            'password' => '12345',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_edit_form_renders()
    {
        $response = $this->actingAs($this->adminUser)->get("/admin/employees/{$this->employeeUser->id}/edit");
        $response->assertStatus(200);
        $response->assertViewIs('admin.employees.edit');
        $response->assertViewHas('employee');
    }

    public function test_update_employee()
    {
        $response = $this->actingAs($this->adminUser)->put("/admin/employees/{$this->employeeUser->id}", [
            'name' => 'Updated Employee',
            'email' => 'employee@test.com',
        ]);

        $response->assertRedirect(route('admin.employees.index'));
        $this->assertDatabaseHas('users', [
            'id' => $this->employeeUser->id,
            'name' => 'Updated Employee',
        ]);
    }

    public function test_update_with_new_password()
    {
        $response = $this->actingAs($this->adminUser)->put("/admin/employees/{$this->employeeUser->id}", [
            'name' => 'Employee With New Password',
            'email' => 'employee@test.com',
            'password' => 'newpassword123',
        ]);

        $response->assertRedirect(route('admin.employees.index'));

        $this->employeeUser->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->employeeUser->password));
    }

    public function test_destroy_employee()
    {
        $response = $this->actingAs($this->adminUser)->delete("/admin/employees/{$this->employeeUser->id}");
        $response->assertRedirect(route('admin.employees.index'));
        $this->assertDatabaseMissing('users', ['id' => $this->employeeUser->id]);
    }

    public function test_destroy_non_employee_fails()
    {
        $response = $this->actingAs($this->adminUser)->delete("/admin/employees/{$this->normalUser->id}");
        $response->assertRedirect(route('admin.employees.index'));
        $this->assertDatabaseHas('users', ['id' => $this->normalUser->id]);
    }

    public function test_edit_non_employee_fails()
    {
        $response = $this->actingAs($this->adminUser)->get("/admin/employees/{$this->normalUser->id}/edit");
        $response->assertRedirect(route('admin.employees.index'));
    }

    public function test_show_redirects_to_index()
    {
        $response = $this->actingAs($this->adminUser)->get("/admin/employees/{$this->employeeUser->id}");
        $response->assertRedirect(route('admin.employees.index'));
    }

    public function test_non_admin_cannot_access_employee_management()
    {
        $response = $this->actingAs($this->employeeUser)->get('/admin/employees');
        $response->assertStatus(403);
    }
}
