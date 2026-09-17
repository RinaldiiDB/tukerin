<?php

namespace Tests\Feature;

use App\Models\ExchangeTransaction;
use App\Models\ExchangeTransactionDetail;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Support\CreateFixtures;
use Tests\TestCase;

/**
 * Sistem: SYS-01, SYS-02, SYS-03
 */
class SystemIntegrationTest extends TestCase
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

    /** SYS-01: Integrasi QR + barcode + transaksi + detail + saldo + redeem + approve. */
    public function test_sys_01_integrasi_penukaran_sampai_pencairan(): void
    {
        $employee = $this->makeEmployee();
        $admin = $this->makeAdmin();
        $user = $this->makeUser(['qr_code' => 'TK-SYS01QR1', 'points_balance' => 0]);
        $bottle = $this->makeBottle(['barcode' => '899SYS010001', 'points_value' => 10]);

        // 1. Lookup QR + barcode (tahap scan pegawai).
        $this->actingAs($employee)->getJson('/employee/scan/user/TK-SYS01QR1')
            ->assertOk()->assertJsonPath('status', 'success');
        $this->actingAs($employee)->getJson('/employee/scan/bottle/899SYS010001')
            ->assertOk()->assertJsonPath('bottle.points_value', 10);

        // 2. Konfirmasi transaksi 5 botol x 10 poin = 50 poin.
        $this->actingAs($employee)->post('/employee/transactions', [
            'user_id' => $user->id,
            'items' => [['bottle_type_id' => $bottle->id, 'quantity' => 5]],
        ])->assertRedirect(route('employee.dashboard'));

        $transaction = ExchangeTransaction::where('user_id', $user->id)->firstOrFail();
        $this->assertEquals(50, $transaction->total_points);
        $this->assertEquals($employee->id, $transaction->employee_id);
        $this->assertDatabaseHas('exchange_transaction_details', [
            'transaction_id' => $transaction->id,
            'quantity' => 5,
            'points_earned' => 50,
        ]);
        $this->assertEquals(50, $user->profile->fresh()->points_balance);

        // 3. Nasabah mengajukan pencairan 30 poin -> pending, saldo belum berkurang.
        // Refresh model agar validasi StoreRedemptionRequest membaca saldo 50 (bukan cache 0).
        $user = $user->fresh()->load('profile', 'role');
        $this->actingAs($user)->post('/user/rewards', [
            'points_used' => 30,
            'method' => 'ewallet',
            'bank_name' => 'DANA',
            'recipient_account' => '081200000001',
        ])->assertRedirect(route('user.rewards'));

        $redemption = \App\Models\RedemptionRequest::where('user_id', $user->id)->firstOrFail();
        $this->assertEquals('pending', $redemption->status);
        $this->assertEquals(6000, $redemption->amount);
        $this->assertEquals(50, $user->profile->fresh()->points_balance);

        // 4. Admin approve -> saldo berkurang 50 - 30 = 20.
        $this->actingAs($admin)->post(route('admin.redemptions.approve', $redemption->id))
            ->assertRedirect(route('admin.redemptions'));

        $this->assertEquals('approved', $redemption->fresh()->status);
        $this->assertEquals(20, $user->profile->fresh()->points_balance);
    }

    /**
     * SYS-02: Atomic transaction — exception di tengah penyimpanan membatalkan semuanya.
     * Sesuai permintaan: simulasi via Exception, bukan sekadar input invalid.
     */
    public function test_sys_02_atomic_rollback_saat_exception(): void
    {
        $user = $this->makeUser(['points_balance' => 100]);
        $employee = $this->makeEmployee();
        $bottle = $this->makeBottle(['points_value' => 10]);

        try {
            DB::transaction(function () use ($user, $employee, $bottle) {
                $transaction = ExchangeTransaction::create([
                    'user_id' => $user->id,
                    'employee_id' => $employee->id,
                    'total_points' => 50,
                    'transacted_at' => Carbon::now(),
                ]);

                ExchangeTransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'bottle_type_id' => $bottle->id,
                    'quantity' => 5,
                    'points_earned' => 50,
                ]);

                // Simulasi error setelah parent + detail dibuat, sebelum increment saldo.
                throw new \RuntimeException('simulasi gagal di tengah transaksi');
            });
            $this->fail('Seharusnya melempar RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertEquals('simulasi gagal di tengah transaksi', $e->getMessage());
        }

        // Seluruh perubahan dibatalkan.
        $this->assertDatabaseCount('exchange_transactions', 0);
        $this->assertDatabaseCount('exchange_transaction_details', 0);
        $this->assertEquals(100, $user->profile->fresh()->points_balance);
    }

    /** SYS-02 (varian HTTP): item invalid -> tidak ada transaksi parsial tersimpan. */
    public function test_sys_02_transaksi_invalid_tidak_menyimpan_parsial(): void
    {
        $employee = $this->makeEmployee();
        $user = $this->makeUser(['points_balance' => 100]);
        $bottle = $this->makeBottle(['points_value' => 10]);

        $response = $this->actingAs($employee)->post('/employee/transactions', [
            'user_id' => $user->id,
            'items' => [
                ['bottle_type_id' => $bottle->id, 'quantity' => 2],
                ['bottle_type_id' => 999999, 'quantity' => 1],
            ],
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('exchange_transactions', 0);
        $this->assertDatabaseCount('exchange_transaction_details', 0);
        $this->assertEquals(100, $user->profile->fresh()->points_balance);
    }

    /** SYS-03: Pembatasan akses berdasarkan role. */
    public function test_sys_03_user_ditolak_akses_admin_dan_employee(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($user)->get('/admin/users')->assertForbidden();
        $this->actingAs($user)->get('/admin/transactions')->assertForbidden();
        $this->actingAs($user)->get('/employee/scan')->assertForbidden();
    }

    /** SYS-03: Pegawai ditolak akses admin; guest diarahkan ke login. */
    public function test_sys_03_employee_ditolak_akses_admin_guest_redirect_login(): void
    {
        $employee = $this->makeEmployee();

        $this->actingAs($employee)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($employee)->get('/admin/redemptions')->assertForbidden();

        // Pegawai tidak boleh memakai endpoint nasabah dan sebaliknya.
        $this->actingAs($employee)->get('/user/dashboard')->assertForbidden();
    }

    /** SYS-03: Guest (belum login) diarahkan ke login. */
    public function test_sys_03_guest_redirect_login(): void
    {
        // Tanpa actingAs agar benar-benar guest.
        $this->get('/user/dashboard')->assertRedirect('/login');
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }
}
