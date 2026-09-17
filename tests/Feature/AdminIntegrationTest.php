<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Support\CreateFixtures;
use Tests\TestCase;

/**
 * Admin: ADM-02, ADM-03, ADM-04, ADM-05, ADM-06, ADM-07
 */
class AdminIntegrationTest extends TestCase
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

    /** ADM-02: Membuat akun pegawai valid -> role employee. */
    public function test_adm_02_buat_pegawai_berhasil(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post('/admin/employees', [
            'name' => 'Pegawai Baru',
            'email' => 'pegawai-baru@example.com',
            'password' => 'pegawai123',
        ]);

        $response->assertRedirect(route('admin.employees.index'));

        $employee = User::where('email', 'pegawai-baru@example.com')->first();
        $this->assertNotNull($employee);
        $this->assertTrue($employee->isEmployee());
    }

    /** ADM-03: Mengubah data pegawai -> data diperbarui. */
    public function test_adm_03_ubah_pegawai_berhasil(): void
    {
        $admin = $this->makeAdmin();
        $employee = $this->makeEmployee(['email' => 'lama-adm03@example.com', 'name' => 'Nama Lama']);

        $response = $this->actingAs($admin)->put(route('admin.employees.update', $employee->id), [
            'name' => 'Nama Baru',
            'email' => 'baru-adm03@example.com',
        ]);

        $response->assertRedirect(route('admin.employees.index'));
        $this->assertDatabaseHas('users', [
            'id' => $employee->id,
            'name' => 'Nama Baru',
            'email' => 'baru-adm03@example.com',
        ]);
    }

    /** ADM-04: Menghapus akun pegawai -> akun terhapus. */
    public function test_adm_04_hapus_pegawai_berhasil(): void
    {
        $admin = $this->makeAdmin();
        $employee = $this->makeEmployee(['email' => 'hapus-adm04@example.com']);

        $response = $this->actingAs($admin)->delete(route('admin.employees.destroy', $employee->id));

        $response->assertRedirect(route('admin.employees.index'));
        $this->assertDatabaseMissing('users', ['id' => $employee->id]);
    }

    /** ADM-05: Approve pencairan pending -> approved + saldo berkurang. */
    public function test_adm_05_approve_pencairan_kurangi_saldo(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser();
        $this->giveBalance($user, 500);
        $redemption = $this->makeRedemption($user, ['points_used' => 150, 'status' => 'pending']);

        $response = $this->actingAs($admin)->post(route('admin.redemptions.approve', $redemption->id));

        $response->assertRedirect(route('admin.redemptions'));

        $this->assertDatabaseHas('redemption_requests', [
            'id' => $redemption->id,
            'status' => 'approved',
        ]);
        $this->assertNotNull($redemption->fresh()->processed_at);
        // 500 - 150 = 350.
        $this->assertEquals(350, $user->profile->fresh()->points_balance);
    }

    /** ADM-06: Reject pencairan pending + alasan -> rejected, alasan tersimpan, saldo tetap. */
    public function test_adm_06_reject_pencairan_saldo_tetap(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser();
        $this->giveBalance($user, 500);
        $redemption = $this->makeRedemption($user, ['points_used' => 200, 'status' => 'pending']);

        $response = $this->actingAs($admin)->post(route('admin.redemptions.reject', $redemption->id), [
            'rejection_note' => 'Data rekening tidak valid',
        ]);

        $response->assertRedirect(route('admin.redemptions'));

        $this->assertDatabaseHas('redemption_requests', [
            'id' => $redemption->id,
            'status' => 'rejected',
            'rejection_note' => 'Data rekening tidak valid',
        ]);
        $this->assertEquals(500, $user->profile->fresh()->points_balance);
    }

    /** ADM-06 (negatif): reject tanpa alasan ditolak validasi. */
    public function test_adm_06_reject_tanpa_alasan_ditolak(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser();
        $redemption = $this->makeRedemption($user, ['status' => 'pending']);

        $response = $this->actingAs($admin)->post(route('admin.redemptions.reject', $redemption->id), []);

        $response->assertSessionHasErrors('rejection_note');
        $this->assertEquals('pending', $redemption->fresh()->status);
    }

    /** ADM-07: Melihat seluruh transaksi sistem. */
    public function test_adm_07_lihat_seluruh_transaksi(): void
    {
        $admin = $this->makeAdmin();
        $userA = $this->makeUser();
        $userB = $this->makeUser();
        $empA = $this->makeEmployee();
        $empB = $this->makeEmployee();

        $trxA = $this->makeTransaction($userA, $empA, 60);
        $trxB = $this->makeTransaction($userB, $empB, 90);

        $response = $this->actingAs($admin)->get('/admin/transactions');

        $response->assertOk();
        $ids = $response->viewData('transactions')->pluck('id')->all();

        $this->assertContains($trxA->id, $ids);
        $this->assertContains($trxB->id, $ids);
    }
}
