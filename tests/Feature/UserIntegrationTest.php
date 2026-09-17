<?php

namespace Tests\Feature;

use App\Models\ExchangeTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Support\CreateFixtures;
use Tests\TestCase;

/**
 * Nasabah: USR-03, USR-04, USR-05, USR-06
 */
class UserIntegrationTest extends TestCase
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

    /** USR-03: Melihat QR Code -> QR Code nasabah tampil di halaman. */
    public function test_usr_03_melihat_qr_code(): void
    {
        $user = $this->makeUser(['qr_code' => 'TK-USR03QR1']);

        $response = $this->actingAs($user)->get('/user/qr');

        $response->assertOk();
        $response->assertSee('TK-USR03QR1');
    }

    /** USR-04: Histori transaksi -> hanya milik nasabah yang login. */
    public function test_usr_04_histori_hanya_milik_sendiri(): void
    {
        $userA = $this->makeUser(['email' => 'a-usr04@example.com']);
        $userB = $this->makeUser(['email' => 'b-usr04@example.com']);
        $employee = $this->makeEmployee();

        $trxA = $this->makeTransaction($userA, $employee, 120);
        $trxB = $this->makeTransaction($userB, $employee, 999);

        $response = $this->actingAs($userA)->get('/user/transactions');

        $response->assertOk();
        $transactions = $response->viewData('transactions');
        $ids = $transactions->pluck('id')->all();

        $this->assertContains($trxA->id, $ids);
        $this->assertNotContains($trxB->id, $ids);
    }

    /** USR-05: Pengajuan pencairan poin mencukupi -> status pending, saldo belum berkurang. */
    public function test_usr_05_pengajuan_pencairan_poin_mencukupi(): void
    {
        $user = $this->makeUser();
        $this->giveBalance($user, 500);
        // Refresh agar auth()->user()->profile membaca saldo terbaru (hindari relasi basi).
        $user = $user->fresh()->load('profile', 'role');

        $response = $this->actingAs($user)->post('/user/rewards', [
            'points_used' => 100,
            'method' => 'cash',
            'bank_name' => 'BCA',
            'recipient_account' => '1234567890',
        ]);

        $response->assertRedirect(route('user.rewards'));

        $this->assertDatabaseHas('redemption_requests', [
            'user_id' => $user->id,
            'points_used' => 100,
            'amount' => 20000, // 100 * 200
            'status' => 'pending',
        ]);

        // Saldo belum dikurangi saat pending.
        $this->assertEquals(500, $user->profile->fresh()->points_balance);
    }

    /** USR-06: Pengajuan melebihi saldo -> ditolak dengan pesan "Poin tidak mencukupi". */
    public function test_usr_06_pengajuan_melebihi_saldo_ditolak(): void
    {
        $user = $this->makeUser();
        $this->giveBalance($user, 50);
        $user = $user->fresh()->load('profile', 'role');

        $response = $this->actingAs($user)->post('/user/rewards', [
            'points_used' => 100,
            'method' => 'ewallet',
            'bank_name' => 'DANA',
            'recipient_account' => '081234567890',
        ]);

        $response->assertSessionHasErrors('points_used');

        $errors = session('errors');
        $message = $errors->get('points_used')[0] ?? '';
        $this->assertStringContainsString('Poin tidak mencukupi', $message);

        $this->assertDatabaseMissing('redemption_requests', [
            'user_id' => $user->id,
            'points_used' => 100,
        ]);
    }

    /** USR-06 (varian): pending ikut mengurangi saldo tersedia. */
    public function test_usr_06_pending_mengurangi_saldo_tersedia(): void
    {
        $user = $this->makeUser();
        $this->giveBalance($user, 200);
        $user = $user->fresh()->load('profile', 'role');
        // 150 poin sedang pending, tersedia tinggal 50.
        $this->makeRedemption($user, ['points_used' => 150, 'status' => 'pending']);

        $response = $this->actingAs($user)->post('/user/rewards', [
            'points_used' => 100,
            'method' => 'cash',
            'bank_name' => 'BRI',
            'recipient_account' => '999888777',
        ]);

        $response->assertSessionHasErrors('points_used');
    }
}
