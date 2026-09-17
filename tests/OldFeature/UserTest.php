<?php

namespace Tests\Feature;

use App\Models\ExchangeTransaction;
use App\Models\RedemptionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, CreatesTestFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFixtures();
    }

    public function test_dashboard_renders_with_profile_and_recent_data()
    {
        ExchangeTransaction::create([
            'user_id' => $this->normalUser->id,
            'employee_id' => $this->employeeUser->id,
            'total_points' => 50,
            'transacted_at' => now(),
        ]);

        RedemptionRequest::create([
            'user_id' => $this->normalUser->id,
            'points_used' => 10,
            'amount' => 2000,
            'method' => 'cash',
            'bank_name' => 'Bank Mandiri',
            'recipient_account' => '12345',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->normalUser)->get('/user/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('user.dashboard');
        $response->assertViewHas('profile');
        $response->assertViewHas('recentTransactions');
        $response->assertViewHas('recentRedemptions');
    }

    public function test_empty_dashboard_renders_with_zero_data()
    {
        $response = $this->actingAs($this->normalUser)->get('/user/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('recentTransactions', function ($transactions) {
            return $transactions->isEmpty();
        });
        $response->assertViewHas('recentRedemptions', function ($redemptions) {
            return $redemptions->isEmpty();
        });
    }

    public function test_qr_page_renders_with_profile()
    {
        $response = $this->actingAs($this->normalUser)->get('/user/qr');
        $response->assertStatus(200);
        $response->assertViewIs('user.qr');
        $response->assertViewHas('profile');
    }

    public function test_transactions_page_renders_with_paginated_data()
    {
        ExchangeTransaction::create([
            'user_id' => $this->normalUser->id,
            'employee_id' => $this->employeeUser->id,
            'total_points' => 30,
            'transacted_at' => now(),
        ]);

        $response = $this->actingAs($this->normalUser)->get('/user/transactions');
        $response->assertStatus(200);
        $response->assertViewIs('user.transactions');
        $response->assertViewHas('transactions');
    }

    public function test_rewards_page_renders_with_paginated_data()
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

        $response = $this->actingAs($this->normalUser)->get('/user/rewards');
        $response->assertStatus(200);
        $response->assertViewIs('user.rewards');
        $response->assertViewHas('redemptions');
    }

    public function test_rewards_create_form_renders()
    {
        $response = $this->actingAs($this->normalUser)->get('/user/rewards/create');
        $response->assertStatus(200);
        $response->assertViewIs('user.rewards_create');
        $response->assertViewHas('profile');
    }

    public function test_user_cannot_access_employee_pages()
    {
        $response = $this->actingAs($this->normalUser)->get('/employee/dashboard');
        $response->assertStatus(403);
    }

    public function test_user_cannot_access_admin_pages()
    {
        $response = $this->actingAs($this->normalUser)->get('/admin/dashboard');
        $response->assertStatus(403);
    }
}
