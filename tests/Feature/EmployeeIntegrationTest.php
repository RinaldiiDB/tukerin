<?php

namespace Tests\Feature;

use App\Models\ExchangeTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Support\CreateFixtures;
use Tests\TestCase;

/**
 * Pegawai: EMP-02, EMP-03, EMP-04, EMP-05
 */
class EmployeeIntegrationTest extends TestCase
{
    use RefreshDatabase, CreateFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Mail::fake();
        Notification::fake();
        $this->ensureRoles();
    }

    /** EMP-02: Scan QR nasabah valid -> data nasabah ditemukan. */
    public function test_emp_02_scan_qr_valid(): void
    {
        $user = $this->makeUser(['qr_code' => 'TK-EMP02QR1', 'name' => 'Nasabah Scan']);
        $employee = $this->makeEmployee();

        $response = $this->actingAs($employee)->getJson('/employee/scan/user/TK-EMP02QR1');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.name', 'Nasabah Scan');
    }

    /** EMP-02 (negatif): QR tidak dikenal -> 404 "tidak ditemukan". */
    public function test_emp_02_scan_qr_tidak_ditemukan(): void
    {
        $employee = $this->makeEmployee();

        $response = $this->actingAs($employee)->getJson('/employee/scan/user/TK-TIDAKADA');

        $response->assertNotFound()
            ->assertJsonPath('status', 'error');
        $this->assertStringContainsString('tidak ditemukan', $response->json('message'));
    }

    /** EMP-03: Scan barcode valid -> jenis botol + poin ditemukan. */
    public function test_emp_03_scan_barcode_valid(): void
    {
        $employee = $this->makeEmployee();
        $bottle = $this->makeBottle(['barcode' => '8990000000001', 'points_value' => 12]);

        $response = $this->actingAs($employee)->getJson('/employee/scan/bottle/8990000000001');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('bottle.barcode', '8990000000001')
            ->assertJsonPath('bottle.points_value', 12);
    }

    /** EMP-03 (negatif): barcode tidak dikenal -> 404 "tidak ditemukan". */
    public function test_emp_03_scan_barcode_tidak_ditemukan(): void
    {
        $employee = $this->makeEmployee();

        $response = $this->actingAs($employee)->getJson('/employee/scan/bottle/0000000000000');

        $response->assertNotFound()
            ->assertJsonPath('status', 'error');
        $this->assertStringContainsString('tidak ditemukan', $response->json('message'));
    }

    /** EMP-04: Konfirmasi transaksi -> tersimpan + saldo nasabah bertambah. */
    public function test_emp_04_konfirmasi_transaksi_tambah_saldo(): void
    {
        $employee = $this->makeEmployee();
        $user = $this->makeUser(['points_balance' => 10]);
        $bottleA = $this->makeBottle(['points_value' => 10]);
        $bottleB = $this->makeBottle(['points_value' => 8]);

        // 2x10 + 3x8 = 44 poin.
        $response = $this->actingAs($employee)->post('/employee/transactions', [
            'user_id' => $user->id,
            'items' => [
                ['bottle_type_id' => $bottleA->id, 'quantity' => 2],
                ['bottle_type_id' => $bottleB->id, 'quantity' => 3],
            ],
        ]);

        $response->assertRedirect(route('employee.dashboard'));

        $this->assertDatabaseHas('exchange_transactions', [
            'user_id' => $user->id,
            'employee_id' => $employee->id,
            'total_points' => 44,
        ]);

        $transaction = ExchangeTransaction::where('user_id', $user->id)->first();
        $this->assertNotNull($transaction);

        $this->assertDatabaseHas('exchange_transaction_details', [
            'transaction_id' => $transaction->id,
            'bottle_type_id' => $bottleA->id,
            'quantity' => 2,
            'points_earned' => 20,
        ]);
        $this->assertDatabaseHas('exchange_transaction_details', [
            'transaction_id' => $transaction->id,
            'bottle_type_id' => $bottleB->id,
            'quantity' => 3,
            'points_earned' => 24,
        ]);

        // 10 + 44 = 54.
        $this->assertEquals(54, $user->profile->fresh()->points_balance);
    }

    /** EMP-05: Histori -> hanya transaksi yang diproses pegawai tersebut. */
    public function test_emp_05_histori_hanya_milik_pegawai(): void
    {
        $empA = $this->makeEmployee(['email' => 'empa-emp05@example.com']);
        $empB = $this->makeEmployee(['email' => 'empb-emp05@example.com']);
        $user = $this->makeUser();

        $trxA = $this->makeTransaction($user, $empA, 50);
        $trxB = $this->makeTransaction($user, $empB, 77);

        $response = $this->actingAs($empA)->get('/employee/transactions');

        $response->assertOk();
        $ids = $response->viewData('transactions')->pluck('id')->all();

        $this->assertContains($trxA->id, $ids);
        $this->assertNotContains($trxB->id, $ids);
    }
}
