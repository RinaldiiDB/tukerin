<?php

namespace Tests\Feature;

use App\Models\ExchangeTransaction;
use App\Models\RedemptionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase, CreatesTestFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFixtures();
    }

    public function test_dashboard_renders_with_statistics()
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('totalUsers');
        $response->assertViewHas('totalTransactions');
        $response->assertViewHas('totalPointsCirculated');
        $response->assertViewHas('pendingRedemptions');
    }

    public function test_dashboard_shows_correct_counts()
    {
        RedemptionRequest::create([
            'user_id' => $this->normalUser->id,
            'points_used' => 10,
            'amount' => 2000,
            'method' => 'cash',
            'bank_name' => 'Bank Mandiri',
            'recipient_account' => '12345',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->adminUser)->get('/admin/dashboard');
        $response->assertViewHas('totalUsers', 1);
        $response->assertViewHas('totalTransactions', 0);
        $response->assertViewHas('pendingRedemptions', 1);
    }

    public function test_users_list_renders()
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertViewIs('admin.users');
        $response->assertViewHas('users');
    }

    public function test_transactions_list_renders()
    {
        ExchangeTransaction::create([
            'user_id' => $this->normalUser->id,
            'employee_id' => $this->employeeUser->id,
            'total_points' => 30,
            'transacted_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)->get('/admin/transactions');
        $response->assertStatus(200);
        $response->assertViewIs('admin.transactions');
        $response->assertViewHas('transactions');
    }

    public function test_redemptions_list_renders()
    {
        RedemptionRequest::create([
            'user_id' => $this->normalUser->id,
            'points_used' => 20,
            'amount' => 4000,
            'method' => 'ewallet',
            'bank_name' => 'GoPay',
            'recipient_account' => '0812345678',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->adminUser)->get('/admin/redemptions');
        $response->assertStatus(200);
        $response->assertViewIs('admin.redemptions');
        $response->assertViewHas('redemptions');
    }

    public function test_approve_non_existent_redemption_fails()
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/redemptions/non-existent-id/approve');
        $response->assertRedirect();
    }

    public function test_approve_already_processed_redemption_fails()
    {
        $redemption = RedemptionRequest::create([
            'user_id' => $this->normalUser->id,
            'points_used' => 10,
            'amount' => 2000,
            'method' => 'cash',
            'bank_name' => 'Bank Mandiri',
            'recipient_account' => '12345',
            'status' => 'approved',
            'processed_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)->post("/admin/redemptions/{$redemption->id}/approve");
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_reject_non_existent_redemption_fails()
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/redemptions/non-existent-id/reject', [
            'rejection_note' => 'Alasan penolakan.',
        ]);
        $response->assertRedirect();
    }

    public function test_reject_already_processed_redemption_fails()
    {
        $redemption = RedemptionRequest::create([
            'user_id' => $this->normalUser->id,
            'points_used' => 10,
            'amount' => 2000,
            'method' => 'cash',
            'bank_name' => 'Bank Mandiri',
            'recipient_account' => '12345',
            'status' => 'rejected',
            'processed_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)->post("/admin/redemptions/{$redemption->id}/reject", [
            'rejection_note' => 'Alasan penolakan.',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_reject_without_note_fails()
    {
        $redemption = RedemptionRequest::create([
            'user_id' => $this->normalUser->id,
            'points_used' => 10,
            'amount' => 2000,
            'method' => 'cash',
            'bank_name' => 'Bank Mandiri',
            'recipient_account' => '12345',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->adminUser)->post("/admin/redemptions/{$redemption->id}/reject", []);
        $response->assertSessionHasErrors('rejection_note');
    }

    public function test_admin_cannot_access_user_pages()
    {
        $response = $this->actingAs($this->adminUser)->get('/user/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_employee_pages()
    {
        $response = $this->actingAs($this->adminUser)->get('/employee/dashboard');
        $response->assertStatus(403);
    }
}
