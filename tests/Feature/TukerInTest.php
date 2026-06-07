<?php

namespace Tests\Feature;

use App\Models\BottleType;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\ExchangeTransaction;
use App\Models\RedemptionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TukerInTest extends TestCase
{
    use RefreshDatabase;

    private $adminRole;
    private $employeeRole;
    private $userRole;

    private $adminUser;
    private $employeeUser;
    private $normalUser;

    private $bottle1;
    private $bottle2;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Seed Roles
        $this->adminRole = Role::create(['name' => 'admin', 'label' => 'Admin']);
        $this->employeeRole = Role::create(['name' => 'employee', 'label' => 'Pegawai']);
        $this->userRole = Role::create(['name' => 'user', 'label' => 'User']);

        // 2. Seed Users
        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role_id' => $this->adminRole->id,
        ]);

        $this->employeeUser = User::create([
            'name' => 'Employee Test',
            'email' => 'employee@test.com',
            'password' => Hash::make('password'),
            'role_id' => $this->employeeRole->id,
        ]);

        $this->normalUser = User::create([
            'name' => 'Nasabah Test',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'role_id' => $this->userRole->id,
        ]);

        UserProfile::create([
            'user_id' => $this->normalUser->id,
            'phone' => '0812345678',
            'qr_code' => 'TK-TEST1234',
            'points_balance' => 100, // start with 100 points for test
        ]);

        // 3. Seed Bottle Types
        $this->bottle1 = BottleType::create([
            'name' => 'Botol Kecil',
            'barcode' => '11111',
            'points_value' => 10,
        ]);

        $this->bottle2 = BottleType::create([
            'name' => 'Botol Besar',
            'barcode' => '22222',
            'points_value' => 25,
        ]);
    }

    /**
     * Test guest access redirects to login.
     */
    public function test_guests_are_redirected_to_login()
    {
        $response = $this->get('/user/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test registration creates user, profile and unique QR.
     */
    public function test_nasabah_registration_flow()
    {
        $response = $this->post('/register', [
            'name' => 'New Customer',
            'email' => 'newcustomer@test.com',
            'phone' => '0899999999',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/user/dashboard');
        
        $this->assertDatabaseHas('users', [
            'email' => 'newcustomer@test.com',
            'role_id' => $this->userRole->id,
        ]);

        $user = User::where('email', 'newcustomer@test.com')->first();
        $this->assertNotNull($user->profile);
        $this->assertEquals('0899999999', $user->profile->phone);
        $this->assertNotEmpty($user->profile->qr_code);
        $this->assertEquals(0, $user->profile->points_balance);
    }

    /**
     * Test role-based authorization redirects / restricts page access.
     */
    public function test_role_based_authorization_restrictions()
    {
        // 1. User trying to access Admin pages -> 403 Forbidden
        $response = $this->actingAs($this->normalUser)->get('/admin/dashboard');
        $response->assertStatus(403);

        // 2. Employee trying to access Admin pages -> 403 Forbidden
        $response = $this->actingAs($this->employeeUser)->get('/admin/dashboard');
        $response->assertStatus(403);

        // 3. Admin trying to access User pages -> 403 Forbidden
        $response = $this->actingAs($this->adminUser)->get('/user/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test AJAX lookup endpoints for Employee scanner.
     */
    public function test_employee_lookup_endpoints()
    {
        // Lookup User QR
        $response = $this->actingAs($this->employeeUser)->get('/employee/scan/user/TK-TEST1234');
        $response->assertStatus(200);
        $response->assertJsonPath('user.name', 'Nasabah Test');

        // Lookup invalid User QR
        $response = $this->actingAs($this->employeeUser)->get('/employee/scan/user/TK-INVALID');
        $response->assertStatus(404);

        // Lookup Bottle Barcode
        $response = $this->actingAs($this->employeeUser)->get('/employee/scan/bottle/11111');
        $response->assertStatus(200);
        $response->assertJsonPath('bottle.name', 'Botol Kecil');
    }

    /**
     * Test transaction drop-off submissions (points increment).
     */
    public function test_employee_transaction_processing()
    {
        $payload = [
            'user_id' => $this->normalUser->id,
            'items' => [
                [
                    'bottle_type_id' => $this->bottle1->id,
                    'quantity' => 3, // 3 * 10 = 30 points
                ],
                [
                    'bottle_type_id' => $this->bottle2->id,
                    'quantity' => 2, // 2 * 25 = 50 points
                ]
            ]
        ];

        // Total points earned: 80 points. Original user points: 100 points.
        $response = $this->actingAs($this->employeeUser)->post('/employee/transactions', $payload);
        
        $response->assertRedirect('/employee/dashboard');
        
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $this->normalUser->id,
            'points_balance' => 180,
        ]);

        $this->assertDatabaseHas('exchange_transactions', [
            'user_id' => $this->normalUser->id,
            'employee_id' => $this->employeeUser->id,
            'total_points' => 80,
        ]);
    }

    /**
     * Test point redemption request validation.
     */
    public function test_user_redemption_validation()
    {
        // 1. Attempt to redeem more points than active balance (100 pts)
        $response = $this->actingAs($this->normalUser)->post('/user/rewards', [
            'points_used' => 120, // invalid
            'method' => 'ewallet',
            'bank_name' => 'GoPay',
            'recipient_account' => '0812345678',
        ]);
        $response->assertSessionHasErrors(['points_used']);

        // 2. Successful redemption request (points are NOT decremented immediately)
        $response = $this->actingAs($this->normalUser)->post('/user/rewards', [
            'points_used' => 50, // valid
            'method' => 'ewallet',
            'bank_name' => 'GoPay',
            'recipient_account' => '0812345678',
        ]);
        
        $response->assertRedirect('/user/rewards');
        
        // Assert points remain 100
        $this->normalUser->profile->refresh();
        $this->assertEquals(100, $this->normalUser->profile->points_balance);

        // Assert record created in pending status
        $this->assertDatabaseHas('redemption_requests', [
            'user_id' => $this->normalUser->id,
            'points_used' => 50,
            'status' => 'pending',
        ]);
    }

    /**
     * Test admin approval of redemption request.
     */
    public function test_admin_redemption_approval_flow()
    {
        $redemption = RedemptionRequest::create([
            'user_id' => $this->normalUser->id,
            'points_used' => 40,
            'amount' => 8000,
            'method' => 'cash',
            'bank_name' => 'Bank Mandiri',
            'recipient_account' => '1234567890',
            'status' => 'pending',
        ]);

        // Approve redemption
        $response = $this->actingAs($this->adminUser)->post('/admin/redemptions/' . $redemption->id . '/approve');
        
        $response->assertRedirect('/admin/redemptions');
        
        // Assert status changed to approved
        $redemption->refresh();
        $this->assertEquals('approved', $redemption->status);
        $this->assertNotNull($redemption->processed_at);

        // Assert points balance decremented (100 - 40 = 60 pts)
        $this->normalUser->profile->refresh();
        $this->assertEquals(60, $this->normalUser->profile->points_balance);
    }

    /**
     * Test admin rejection of redemption request.
     */
    public function test_admin_redemption_rejection_flow()
    {
        $redemption = RedemptionRequest::create([
            'user_id' => $this->normalUser->id,
            'points_used' => 40,
            'amount' => 8000,
            'method' => 'cash',
            'bank_name' => 'Bank Mandiri',
            'recipient_account' => '1234567890',
            'status' => 'pending',
        ]);

        // Reject redemption
        $response = $this->actingAs($this->adminUser)->post('/admin/redemptions/' . $redemption->id . '/reject', [
            'rejection_note' => 'Dokumen rekening tidak cocok dengan nama nasabah.',
        ]);
        
        $response->assertRedirect('/admin/redemptions');
        
        // Assert status changed to rejected and note recorded
        $redemption->refresh();
        $this->assertEquals('rejected', $redemption->status);
        $this->assertEquals('Dokumen rekening tidak cocok dengan nama nasabah.', $redemption->rejection_note);
        $this->assertNotNull($redemption->processed_at);

        // Assert points balance is NOT decremented (remains 100 pts)
        $this->normalUser->profile->refresh();
        $this->assertEquals(100, $this->normalUser->profile->points_balance);
    }
}
