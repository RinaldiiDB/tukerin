<?php

namespace Tests\Feature;

use App\Models\ExchangeTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase, CreatesTestFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFixtures();
    }

    public function test_dashboard_renders_with_today_stats()
    {
        ExchangeTransaction::create([
            'user_id' => $this->normalUser->id,
            'employee_id' => $this->employeeUser->id,
            'total_points' => 50,
            'transacted_at' => now(),
        ]);

        $response = $this->actingAs($this->employeeUser)->get('/employee/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('employee.dashboard');
        $response->assertViewHas('todayTransactions');
        $response->assertViewHas('todayCount');
        $response->assertViewHas('todayPoints');
    }

    public function test_empty_dashboard_shows_zero_stats()
    {
        $response = $this->actingAs($this->employeeUser)->get('/employee/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('todayCount', 0);
        $response->assertViewHas('todayPoints', 0);
    }

    public function test_scan_page_renders()
    {
        $response = $this->actingAs($this->employeeUser)->get('/employee/scan');
        $response->assertStatus(200);
        $response->assertViewIs('employee.scan');
    }

    public function test_lookup_invalid_barcode_returns_404()
    {
        $response = $this->actingAs($this->employeeUser)->get('/employee/scan/bottle/INVALID');
        $response->assertStatus(404);
        $response->assertJsonPath('status', 'error');
    }

    public function test_transaction_requires_user_id()
    {
        $response = $this->actingAs($this->employeeUser)->post('/employee/transactions', [
            'items' => [['bottle_type_id' => $this->bottle1->id, 'quantity' => 1]],
        ]);

        $response->assertSessionHasErrors('user_id');
    }

    public function test_transaction_requires_at_least_one_item()
    {
        $response = $this->actingAs($this->employeeUser)->post('/employee/transactions', [
            'user_id' => $this->normalUser->id,
            'items' => [],
        ]);

        $response->assertSessionHasErrors('items');
    }

    public function test_transaction_requires_valid_bottle_type()
    {
        $response = $this->actingAs($this->employeeUser)->post('/employee/transactions', [
            'user_id' => $this->normalUser->id,
            'items' => [['bottle_type_id' => 9999, 'quantity' => 1]],
        ]);

        $response->assertSessionHasErrors('items.0.bottle_type_id');
    }

    public function test_transaction_requires_positive_quantity()
    {
        $response = $this->actingAs($this->employeeUser)->post('/employee/transactions', [
            'user_id' => $this->normalUser->id,
            'items' => [['bottle_type_id' => $this->bottle1->id, 'quantity' => 0]],
        ]);

        $response->assertSessionHasErrors('items.0.quantity');
    }

    public function test_transactions_history_renders_scoped_to_employee()
    {
        ExchangeTransaction::create([
            'user_id' => $this->normalUser->id,
            'employee_id' => $this->employeeUser->id,
            'total_points' => 30,
            'transacted_at' => now(),
        ]);

        $response = $this->actingAs($this->employeeUser)->get('/employee/transactions');
        $response->assertStatus(200);
        $response->assertViewIs('employee.transactions');
        $response->assertViewHas('transactions');
    }

    public function test_employee_cannot_access_user_pages()
    {
        $response = $this->actingAs($this->employeeUser)->get('/user/dashboard');
        $response->assertStatus(403);
    }

    public function test_employee_cannot_access_admin_pages()
    {
        $response = $this->actingAs($this->employeeUser)->get('/admin/dashboard');
        $response->assertStatus(403);
    }
}
