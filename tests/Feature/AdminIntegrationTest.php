<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Support\CreateFixtures;
use Tests\Feature\Support\LogsIntegrationSteps;
use Tests\TestCase;

/**
 * Admin: ADM-02, ADM-03, ADM-04, ADM-05, ADM-06, ADM-07
 */
class AdminIntegrationTest extends TestCase
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

    /** ADM-02: Membuat akun pegawai valid -> role employee. */
    public function test_adm_02_buat_pegawai_berhasil(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post('/admin/employees', [
            'name' => 'Pegawai Baru',
            'email' => 'pegawai-baru@example.com',
            'password' => 'pegawai123',
        ]);
        $this->step('adm-02', 'ACT', 'POST /admin/employees buat pegawai baru', ['email' => 'pegawai-baru@example.com', 'status' => $response->getStatusCode(), 'location' => $response->headers->get('Location')]);

        $response->assertRedirect(route('admin.employees.index'));
        $this->step('adm-02', 'ASSERT', 'Redirect ke daftar pegawai');

        $employee = User::where('email', 'pegawai-baru@example.com')->first();
        $this->assertNotNull($employee);
        $this->step('adm-02', 'ASSERT', 'Akun pegawai tersimpan', ['user_id' => $employee->id]);
        $this->assertTrue($employee->isEmployee());
        $this->step('adm-02', 'RESULT', 'Akun pegawai dibuat dengan role employee', ['role' => $employee->role->name]);
    }

    /** ADM-03: Mengubah data pegawai -> data diperbarui. */
    public function test_adm_03_ubah_pegawai_berhasil(): void
    {
        $admin = $this->makeAdmin();
        $employee = $this->makeEmployee(['email' => 'lama-adm03@example.com', 'name' => 'Nama Lama']);
        $this->step('adm-03', 'SETUP', 'Pegawai lama siap diubah', ['name' => 'Nama Lama', 'email' => 'lama-adm03@example.com']);

        $response = $this->actingAs($admin)->put(route('admin.employees.update', $employee->id), [
            'name' => 'Nama Baru',
            'email' => 'baru-adm03@example.com',
        ]);
        $this->step('adm-03', 'ACT', 'PUT /admin/employees/{id} ubah nama+email', ['employee_id' => $employee->id, 'status' => $response->getStatusCode()]);

        $response->assertRedirect(route('admin.employees.index'));
        $this->step('adm-03', 'ASSERT', 'Redirect ke daftar pegawai');
        $this->assertDatabaseHas('users', [
            'id' => $employee->id,
            'name' => 'Nama Baru',
            'email' => 'baru-adm03@example.com',
        ]);
        $this->step('adm-03', 'RESULT', 'Data pegawai diperbarui', ['name' => 'Nama Baru', 'email' => 'baru-adm03@example.com']);
    }

    /** ADM-04: Menghapus akun pegawai -> akun terhapus. */
    public function test_adm_04_hapus_pegawai_berhasil(): void
    {
        $admin = $this->makeAdmin();
        $employee = $this->makeEmployee(['email' => 'hapus-adm04@example.com']);
        $this->step('adm-04', 'SETUP', 'Pegawai siap dihapus', ['email' => 'hapus-adm04@example.com', 'user_id' => $employee->id]);

        $response = $this->actingAs($admin)->delete(route('admin.employees.destroy', $employee->id));
        $this->step('adm-04', 'ACT', 'DELETE /admin/employees/{id}', ['employee_id' => $employee->id, 'status' => $response->getStatusCode()]);

        $response->assertRedirect(route('admin.employees.index'));
        $this->step('adm-04', 'ASSERT', 'Redirect ke daftar pegawai');
        $this->assertDatabaseMissing('users', ['id' => $employee->id]);
        $this->step('adm-04', 'RESULT', 'Akun pegawai terhapus dari tabel users', ['user_id' => $employee->id]);
    }

    /** ADM-05: Approve pencairan pending -> approved + saldo berkurang. */
    public function test_adm_05_approve_pencairan_kurangi_saldo(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser();
        $this->giveBalance($user, 500);
        $redemption = $this->makeRedemption($user, ['points_used' => 150, 'status' => 'pending']);
        $this->step('adm-05', 'SETUP', 'Request pending 150 poin, saldo 500', ['redemption_id' => $redemption->id, 'points_used' => 150, 'balance' => 500]);

        $response = $this->actingAs($admin)->post(route('admin.redemptions.approve', $redemption->id));
        $this->step('adm-05', 'ACT', 'POST /admin/redemptions/{id}/approve', ['redemption_id' => $redemption->id, 'status' => $response->getStatusCode()]);

        $response->assertRedirect(route('admin.redemptions'));
        $this->step('adm-05', 'ASSERT', 'Redirect ke daftar redemptions');

        $this->assertDatabaseHas('redemption_requests', [
            'id' => $redemption->id,
            'status' => 'approved',
        ]);
        $this->step('adm-05', 'ASSERT', 'Status berubah pending -> approved');
        $this->assertNotNull($redemption->fresh()->processed_at);
        $this->step('adm-05', 'ASSERT', 'Waktu proses tercatat', ['processed_at' => (string) $redemption->fresh()->processed_at]);
        // 500 - 150 = 350.
        $this->assertEquals(350, $user->profile->fresh()->points_balance);
        $this->step('adm-05', 'RESULT', 'Saldo berkurang 500 -> 350', ['balance_before' => 500, 'balance_after' => 350]);
    }

    /** ADM-06: Reject pencairan pending + alasan -> rejected, alasan tersimpan, saldo tetap. */
    public function test_adm_06_reject_pencairan_saldo_tetap(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser();
        $this->giveBalance($user, 500);
        $redemption = $this->makeRedemption($user, ['points_used' => 200, 'status' => 'pending']);
        $this->step('adm-06', 'SETUP', 'Request pending 200 poin, saldo 500', ['redemption_id' => $redemption->id, 'points_used' => 200, 'balance' => 500]);

        $response = $this->actingAs($admin)->post(route('admin.redemptions.reject', $redemption->id), [
            'rejection_note' => 'Data rekening tidak valid',
        ]);
        $this->step('adm-06', 'ACT', 'POST /admin/redemptions/{id}/reject + alasan', ['redemption_id' => $redemption->id, 'status' => $response->getStatusCode()]);

        $response->assertRedirect(route('admin.redemptions'));
        $this->step('adm-06', 'ASSERT', 'Redirect ke daftar redemptions');

        $this->assertDatabaseHas('redemption_requests', [
            'id' => $redemption->id,
            'status' => 'rejected',
            'rejection_note' => 'Data rekening tidak valid',
        ]);
        $this->step('adm-06', 'ASSERT', 'Status rejected + alasan tersimpan', ['rejection_note' => 'Data rekening tidak valid']);
        $this->assertEquals(500, $user->profile->fresh()->points_balance);
        $this->step('adm-06', 'RESULT', 'Saldo tidak berubah (tetap 500)', ['balance' => 500]);
    }

    /** ADM-06 (negatif): reject tanpa alasan ditolak validasi. */
    public function test_adm_06_reject_tanpa_alasan_ditolak(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser();
        $redemption = $this->makeRedemption($user, ['status' => 'pending']);
        $this->step('adm-06', 'SETUP', 'Request pending siap ditolak tanpa alasan', ['redemption_id' => $redemption->id]);

        $response = $this->actingAs($admin)->post(route('admin.redemptions.reject', $redemption->id), []);
        $this->step('adm-06', 'ACT', 'POST reject tanpa rejection_note', ['status' => $response->getStatusCode()]);

        $response->assertSessionHasErrors('rejection_note');
        $this->step('adm-06', 'ASSERT', 'Validasi menolak field rejection_note kosong');
        $this->assertEquals('pending', $redemption->fresh()->status);
        $this->step('adm-06', 'RESULT', 'Status tetap pending', ['status' => 'pending']);
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
        $this->step('adm-07', 'SETUP', 'Dua transaksi sistem (60 & 90 poin)', ['trx_a_points' => 60, 'trx_b_points' => 90]);

        $response = $this->actingAs($admin)->get('/admin/transactions');
        $this->step('adm-07', 'ACT', 'GET /admin/transactions sebagai admin', ['status' => $response->getStatusCode()]);

        $response->assertOk();
        $this->step('adm-07', 'ASSERT', 'Halaman transaksi berhasil dirender (HTTP 200)');
        $ids = $response->viewData('transactions')->pluck('id')->all();
        $this->step('adm-07', 'ASSERT', 'Daftar ID transaksi sistem', ['ids' => $ids]);

        $this->assertContains($trxA->id, $ids);
        $this->step('adm-07', 'ASSERT', 'Transaksi pertama tampil', ['trx_id' => $trxA->id]);
        $this->assertContains($trxB->id, $ids);
        $this->step('adm-07', 'RESULT', 'Seluruh transaksi sistem tampil', ['trx_id' => $trxB->id]);
    }
}
