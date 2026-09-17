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
 * Nasabah: USR-03, USR-04, USR-05, USR-06
 */
class UserIntegrationTest extends TestCase
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

    /** USR-03: Melihat QR Code -> QR Code nasabah tampil di halaman. */
    public function test_usr_03_melihat_qr_code(): void
    {
        $user = $this->makeUser(['qr_code' => 'TK-USR03QR1']);
        $this->step('usr-03', 'SETUP', 'Nasabah login dengan QR code', ['qr_code' => 'TK-USR03QR1']);

        $response = $this->actingAs($user)->get('/user/qr');
        $this->step('usr-03', 'ACT', 'GET /user/qr sebagai nasabah', ['status' => $response->getStatusCode()]);

        $response->assertOk();
        $this->step('usr-03', 'ASSERT', 'Halaman QR berhasil dirender (HTTP 200)');
        $response->assertSee('TK-USR03QR1');
        $this->step('usr-03', 'RESULT', 'QR code terlihat di halaman', ['qr_code' => 'TK-USR03QR1']);
    }

    /** USR-04: Histori transaksi -> hanya milik nasabah yang login. */
    public function test_usr_04_histori_hanya_milik_sendiri(): void
    {
        $userA = $this->makeUser(['email' => 'a-usr04@example.com']);
        $userB = $this->makeUser(['email' => 'b-usr04@example.com']);
        $employee = $this->makeEmployee();

        $trxA = $this->makeTransaction($userA, $employee, 120);
        $trxB = $this->makeTransaction($userB, $employee, 999);
        $this->step('usr-04', 'SETUP', 'Dua nasabah + transaksi masing-masing', ['user_a' => 'a-usr04@example.com', 'trx_a_points' => 120, 'user_b' => 'b-usr04@example.com', 'trx_b_points' => 999]);

        $response = $this->actingAs($userA)->get('/user/transactions');
        $this->step('usr-04', 'ACT', 'GET /user/transactions sebagai nasabah A', ['status' => $response->getStatusCode()]);

        $response->assertOk();
        $this->step('usr-04', 'ASSERT', 'Histori berhasil dirender (HTTP 200)');
        $transactions = $response->viewData('transactions');
        $ids = $transactions->pluck('id')->all();
        $this->step('usr-04', 'ASSERT', 'Daftar ID transaksi milik A', ['ids' => $ids]);

        $this->assertContains($trxA->id, $ids);
        $this->step('usr-04', 'ASSERT', 'Transaksi milik A tampil', ['trx_id' => $trxA->id]);
        $this->assertNotContains($trxB->id, $ids);
        $this->step('usr-04', 'RESULT', 'Hanya histori milik A, transaksi B tidak muncul', ['trx_b_id' => $trxB->id]);
    }

    /** USR-05: Pengajuan pencairan poin mencukupi -> status pending, saldo belum berkurang. */
    public function test_usr_05_pengajuan_pencairan_poin_mencukupi(): void
    {
        $user = $this->makeUser();
        $this->giveBalance($user, 500);
        // Refresh agar auth()->user()->profile membaca saldo terbaru (hindari relasi basi).
        $user = $user->fresh()->load('profile', 'role');
        $this->step('usr-05', 'SETUP', 'Nasabah bersaldo 500 poin', ['balance' => 500]);

        $response = $this->actingAs($user)->post('/user/rewards', [
            'points_used' => 100,
            'method' => 'cash',
            'bank_name' => 'BCA',
            'recipient_account' => '1234567890',
        ]);
        $this->step('usr-05', 'ACT', 'POST /user/rewards cairkan 100 poin via cash/BCA', ['points_used' => 100, 'status' => $response->getStatusCode(), 'location' => $response->headers->get('Location')]);

        $response->assertRedirect(route('user.rewards'));
        $this->step('usr-05', 'ASSERT', 'Redirect ke halaman rewards');

        $this->assertDatabaseHas('redemption_requests', [
            'user_id' => $user->id,
            'points_used' => 100,
            'amount' => 20000, // 100 * 200
            'status' => 'pending',
        ]);
        $this->step('usr-05', 'ASSERT', 'Request tersimpan pending, amount = 100 x Rp200', ['amount' => 20000, 'status' => 'pending']);

        // Saldo belum dikurangi saat pending.
        $this->assertEquals(500, $user->profile->fresh()->points_balance);
        $this->step('usr-05', 'RESULT', 'Pengajuan pending, saldo belum berkurang', ['balance' => 500]);
    }

    /** USR-06: Pengajuan melebihi saldo -> ditolak dengan pesan "Poin tidak mencukupi". */
    public function test_usr_06_pengajuan_melebihi_saldo_ditolak(): void
    {
        $user = $this->makeUser();
        $this->giveBalance($user, 50);
        $user = $user->fresh()->load('profile', 'role');
        $this->step('usr-06', 'SETUP', 'Nasabah bersaldo 50 poin', ['balance' => 50]);

        $response = $this->actingAs($user)->post('/user/rewards', [
            'points_used' => 100,
            'method' => 'ewallet',
            'bank_name' => 'DANA',
            'recipient_account' => '081234567890',
        ]);
        $this->step('usr-06', 'ACT', 'POST /user/rewards cairkan 100 poin (melebihi saldo)', ['points_used' => 100, 'status' => $response->getStatusCode()]);

        $response->assertSessionHasErrors('points_used');
        $this->step('usr-06', 'ASSERT', 'Validasi menolak field points_used');

        $errors = session('errors');
        $message = $errors->get('points_used')[0] ?? '';
        $this->step('usr-06', 'ASSERT', 'Pesan error poin tidak mencukupi', ['message' => $message]);
        $this->assertStringContainsString('Poin tidak mencukupi', $message);

        $this->assertDatabaseMissing('redemption_requests', [
            'user_id' => $user->id,
            'points_used' => 100,
        ]);
        $this->step('usr-06', 'RESULT', 'Pengajuan ditolak, tidak ada request tersimpan');
    }

    /** USR-06 (varian): pending ikut mengurangi saldo tersedia. */
    public function test_usr_06_pending_mengurangi_saldo_tersedia(): void
    {
        $user = $this->makeUser();
        $this->giveBalance($user, 200);
        $user = $user->fresh()->load('profile', 'role');
        // 150 poin sedang pending, tersedia tinggal 50.
        $this->makeRedemption($user, ['points_used' => 150, 'status' => 'pending']);
        $this->step('usr-06', 'SETUP', 'Saldo 200 dengan 150 pending (tersedia 50)', ['balance' => 200, 'pending' => 150, 'available' => 50]);

        $response = $this->actingAs($user)->post('/user/rewards', [
            'points_used' => 100,
            'method' => 'cash',
            'bank_name' => 'BRI',
            'recipient_account' => '999888777',
        ]);
        $this->step('usr-06', 'ACT', 'POST /user/rewards cairkan 100 poin', ['points_used' => 100, 'status' => $response->getStatusCode()]);

        $response->assertSessionHasErrors('points_used');
        $this->step('usr-06', 'RESULT', 'Ditolak karena saldo tersedia (50) < 100');
    }
}
