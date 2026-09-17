<?php

namespace Tests\Feature;

use App\Models\ExchangeTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Support\CreateFixtures;
use Tests\Feature\Support\LogsIntegrationSteps;
use Tests\TestCase;

/**
 * Pegawai: EMP-02, EMP-03, EMP-04, EMP-05
 */
class EmployeeIntegrationTest extends TestCase
{
    use RefreshDatabase, CreateFixtures, LogsIntegrationSteps;

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
        $this->step('emp-02', 'SETUP', 'Nasabah + pegawai siap scan', ['qr_code' => 'TK-EMP02QR1', 'name' => 'Nasabah Scan']);

        $response = $this->actingAs($employee)->getJson('/employee/scan/user/TK-EMP02QR1');
        $this->step('emp-02', 'ACT', 'GET /employee/scan/user/TK-EMP02QR1', ['status' => $response->getStatusCode(), 'body' => $response->getContent()]);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.name', 'Nasabah Scan');
        $this->step('emp-02', 'RESULT', 'Nasabah ditemukan via QR', ['user_id' => $user->id, 'name' => 'Nasabah Scan']);
    }

    /** EMP-02 (negatif): QR tidak dikenal -> 404 "tidak ditemukan". */
    public function test_emp_02_scan_qr_tidak_ditemukan(): void
    {
        $employee = $this->makeEmployee();

        $response = $this->actingAs($employee)->getJson('/employee/scan/user/TK-TIDAKADA');
        $this->step('emp-02', 'ACT', 'GET /employee/scan/user/TK-TIDAKADA', ['status' => $response->getStatusCode(), 'body' => $response->getContent()]);

        $response->assertNotFound()
            ->assertJsonPath('status', 'error');
        $this->step('emp-02', 'ASSERT', 'QR tidak dikenal mengembalikan 404 status error');
        $this->assertStringContainsString('tidak ditemukan', $response->json('message'));
        $this->step('emp-02', 'RESULT', 'Pesan tidak ditemukan tampil', ['message' => $response->json('message')]);
    }

    /** EMP-03: Scan barcode valid -> jenis botol + poin ditemukan. */
    public function test_emp_03_scan_barcode_valid(): void
    {
        $employee = $this->makeEmployee();
        $bottle = $this->makeBottle(['barcode' => '8990000000001', 'points_value' => 12]);
        $this->step('emp-03', 'SETUP', 'Jenis botol terdaftar', ['barcode' => '8990000000001', 'points_value' => 12]);

        $response = $this->actingAs($employee)->getJson('/employee/scan/bottle/8990000000001');
        $this->step('emp-03', 'ACT', 'GET /employee/scan/bottle/8990000000001', ['status' => $response->getStatusCode(), 'body' => $response->getContent()]);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('bottle.barcode', '8990000000001')
            ->assertJsonPath('bottle.points_value', 12);
        $this->step('emp-03', 'RESULT', 'Botol + nilai poin ditemukan', ['barcode' => '8990000000001', 'points_value' => 12]);
    }

    /** EMP-03 (negatif): barcode tidak dikenal -> 404 "tidak ditemukan". */
    public function test_emp_03_scan_barcode_tidak_ditemukan(): void
    {
        $employee = $this->makeEmployee();

        $response = $this->actingAs($employee)->getJson('/employee/scan/bottle/0000000000000');
        $this->step('emp-03', 'ACT', 'GET /employee/scan/bottle/0000000000000', ['status' => $response->getStatusCode(), 'body' => $response->getContent()]);

        $response->assertNotFound()
            ->assertJsonPath('status', 'error');
        $this->step('emp-03', 'ASSERT', 'Barcode tidak dikenal mengembalikan 404 status error');
        $this->assertStringContainsString('tidak ditemukan', $response->json('message'));
        $this->step('emp-03', 'RESULT', 'Pesan tidak ditemukan tampil', ['message' => $response->json('message')]);
    }

    /** EMP-04: Konfirmasi transaksi -> tersimpan + saldo nasabah bertambah. */
    public function test_emp_04_konfirmasi_transaksi_tambah_saldo(): void
    {
        $employee = $this->makeEmployee();
        $user = $this->makeUser(['points_balance' => 10]);
        $bottleA = $this->makeBottle(['points_value' => 10]);
        $bottleB = $this->makeBottle(['points_value' => 8]);
        $this->step('emp-04', 'SETUP', 'Nasabah saldo 10 + 2 jenis botol (10 & 8 poin)', ['balance' => 10, 'bottle_a_points' => 10, 'bottle_b_points' => 8]);

        // 2x10 + 3x8 = 44 poin.
        $response = $this->actingAs($employee)->post('/employee/transactions', [
            'user_id' => $user->id,
            'items' => [
                ['bottle_type_id' => $bottleA->id, 'quantity' => 2],
                ['bottle_type_id' => $bottleB->id, 'quantity' => 3],
            ],
        ]);
        $this->step('emp-04', 'ACT', 'POST /employee/transactions 2x botol A + 3x botol B', ['status' => $response->getStatusCode(), 'location' => $response->headers->get('Location')]);

        $response->assertRedirect(route('employee.dashboard'));
        $this->step('emp-04', 'ASSERT', 'Redirect ke dashboard employee');

        $this->assertDatabaseHas('exchange_transactions', [
            'user_id' => $user->id,
            'employee_id' => $employee->id,
            'total_points' => 44,
        ]);
        $this->step('emp-04', 'ASSERT', 'Parent transaksi tersimpan total 44 (2x10+3x8)', ['total_points' => 44]);

        $transaction = ExchangeTransaction::where('user_id', $user->id)->first();
        $this->assertNotNull($transaction);
        $this->step('emp-04', 'ASSERT', 'Parent transaksi ditemukan', ['transaction_id' => $transaction->id]);

        $this->assertDatabaseHas('exchange_transaction_details', [
            'transaction_id' => $transaction->id,
            'bottle_type_id' => $bottleA->id,
            'quantity' => 2,
            'points_earned' => 20,
        ]);
        $this->step('emp-04', 'ASSERT', 'Detail botol A: qty 2 x 10 = 20', ['points_earned' => 20]);
        $this->assertDatabaseHas('exchange_transaction_details', [
            'transaction_id' => $transaction->id,
            'bottle_type_id' => $bottleB->id,
            'quantity' => 3,
            'points_earned' => 24,
        ]);
        $this->step('emp-04', 'ASSERT', 'Detail botol B: qty 3 x 8 = 24', ['points_earned' => 24]);

        // 10 + 44 = 54.
        $this->assertEquals(54, $user->profile->fresh()->points_balance);
        $this->step('emp-04', 'RESULT', 'Saldo nasabah bertambah 10 -> 54', ['balance_before' => 10, 'balance_after' => 54]);
    }

    /** EMP-05: Histori -> hanya transaksi yang diproses pegawai tersebut. */
    public function test_emp_05_histori_hanya_milik_pegawai(): void
    {
        $empA = $this->makeEmployee(['email' => 'empa-emp05@example.com']);
        $empB = $this->makeEmployee(['email' => 'empb-emp05@example.com']);
        $user = $this->makeUser();

        $trxA = $this->makeTransaction($user, $empA, 50);
        $trxB = $this->makeTransaction($user, $empB, 77);
        $this->step('emp-05', 'SETUP', 'Dua pegawai memproses transaksi (50 & 77 poin)', ['trx_a_points' => 50, 'trx_b_points' => 77]);

        $response = $this->actingAs($empA)->get('/employee/transactions');
        $this->step('emp-05', 'ACT', 'GET /employee/transactions sebagai pegawai A', ['status' => $response->getStatusCode()]);

        $response->assertOk();
        $this->step('emp-05', 'ASSERT', 'Histori berhasil dirender (HTTP 200)');
        $ids = $response->viewData('transactions')->pluck('id')->all();
        $this->step('emp-05', 'ASSERT', 'Daftar ID transaksi pegawai A', ['ids' => $ids]);

        $this->assertContains($trxA->id, $ids);
        $this->step('emp-05', 'ASSERT', 'Transaksi prosesan A tampil', ['trx_id' => $trxA->id]);
        $this->assertNotContains($trxB->id, $ids);
        $this->step('emp-05', 'RESULT', 'Hanya transaksi prosesan A, milik B tidak muncul', ['trx_b_id' => $trxB->id]);
    }
}
