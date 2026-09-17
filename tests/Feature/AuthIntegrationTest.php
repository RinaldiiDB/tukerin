<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Support\CreateFixtures;
use Tests\Feature\Support\LogsIntegrationSteps;
use Tests\TestCase;

/**
 * Auth: USR-01, EMP-01, ADM-01, USR-02
 */
class AuthIntegrationTest extends TestCase
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

    /** USR-01: Login sebagai Nasabah -> diarahkan ke dashboard user. */
    public function test_usr_01_login_nasabah_redirect_dashboard(): void
    {
        $user = $this->makeUser(['email' => 'nasabah-usr01@example.com']);
        $this->step('usr-01', 'SETUP', 'Nasabah dibuat', ['email' => 'nasabah-usr01@example.com', 'role' => 'user']);

        $response = $this->post('/login', [
            'email' => 'nasabah-usr01@example.com',
            'password' => 'password123',
        ]);
        $this->step('usr-01', 'ACT', 'POST /login sebagai nasabah', ['email' => 'nasabah-usr01@example.com', 'status' => $response->getStatusCode(), 'location' => $response->headers->get('Location')]);

        $response->assertRedirect(route('user.dashboard'));
        $this->step('usr-01', 'ASSERT', 'Redirect ke dashboard user', ['expected' => route('user.dashboard')]);
        $this->assertAuthenticatedAs($user);
        $this->step('usr-01', 'RESULT', 'Nasabah terautentikasi dan diarahkan ke dashboard user');
    }

    /** EMP-01: Login sebagai Pegawai -> diarahkan ke dashboard employee. */
    public function test_emp_01_login_pegawai_redirect_dashboard(): void
    {
        $employee = $this->makeEmployee(['email' => 'pegawai-emp01@example.com']);
        $this->step('emp-01', 'SETUP', 'Pegawai dibuat', ['email' => 'pegawai-emp01@example.com', 'role' => 'employee']);

        $response = $this->post('/login', [
            'email' => 'pegawai-emp01@example.com',
            'password' => 'password123',
        ]);
        $this->step('emp-01', 'ACT', 'POST /login sebagai pegawai', ['email' => 'pegawai-emp01@example.com', 'status' => $response->getStatusCode(), 'location' => $response->headers->get('Location')]);

        $response->assertRedirect(route('employee.dashboard'));
        $this->step('emp-01', 'ASSERT', 'Redirect ke dashboard employee', ['expected' => route('employee.dashboard')]);
        $this->assertAuthenticatedAs($employee);
        $this->step('emp-01', 'RESULT', 'Pegawai terautentikasi dan diarahkan ke dashboard employee');
    }

    /** ADM-01: Login sebagai Admin -> diarahkan ke dashboard admin. */
    public function test_adm_01_login_admin_redirect_dashboard(): void
    {
        $admin = $this->makeAdmin(['email' => 'admin-adm01@example.com']);
        $this->step('adm-01', 'SETUP', 'Admin dibuat', ['email' => 'admin-adm01@example.com', 'role' => 'admin']);

        $response = $this->post('/login', [
            'email' => 'admin-adm01@example.com',
            'password' => 'password123',
        ]);
        $this->step('adm-01', 'ACT', 'POST /login sebagai admin', ['email' => 'admin-adm01@example.com', 'status' => $response->getStatusCode(), 'location' => $response->headers->get('Location')]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->step('adm-01', 'ASSERT', 'Redirect ke dashboard admin', ['expected' => route('admin.dashboard')]);
        $this->assertAuthenticatedAs($admin);
        $this->step('adm-01', 'RESULT', 'Admin terautentikasi dan diarahkan ke dashboard admin');
    }

    /** USR-02: Registrasi Nasabah valid -> akun + profile + role user + QR Code dibuat. */
    public function test_usr_02_registrasi_nasabah_berhasil(): void
    {
        $this->step('usr-02', 'SETUP', 'Registrasi dibuka untuk publik, role user tersedia di database');

        $response = $this->post('/register', [
            'name' => 'Nasabah Baru',
            'email' => 'nasabah-baru@example.com',
            'phone' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);
        $this->step('usr-02', 'ACT', 'POST /register dengan data valid', ['email' => 'nasabah-baru@example.com', 'status' => $response->getStatusCode(), 'location' => $response->headers->get('Location')]);

        $response->assertRedirect(route('user.dashboard'));
        $this->step('usr-02', 'ASSERT', 'Redirect ke dashboard user setelah registrasi');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', ['email' => 'nasabah-baru@example.com']);
        $user = \App\Models\User::where('email', 'nasabah-baru@example.com')->first();
        $this->step('usr-02', 'ASSERT', 'Akun nasabah tersimpan di tabel users', ['user_id' => $user->id]);
        $this->assertEquals('user', $user->role->name);
        $this->step('usr-02', 'ASSERT', 'Role otomatis user', ['role' => $user->role->name]);

        $this->assertDatabaseHas('user_profiles', ['user_id' => $user->id]);
        $this->assertNotNull($user->profile->qr_code);
        $this->assertStringStartsWith('TK-', $user->profile->qr_code);
        $this->step('usr-02', 'ASSERT', 'Profile + QR code otomatis dibuat', ['qr_code' => $user->profile->qr_code]);
        $this->assertEquals(0, $user->profile->points_balance);
        $this->step('usr-02', 'RESULT', 'Registrasi berhasil: akun, profile, role user, QR code', ['qr_code' => $user->profile->qr_code, 'points_balance' => 0]);
    }

    /** USR-02 (negatif): registrasi dengan email duplikat ditolak. */
    public function test_usr_02_registrasi_email_duplikat_ditolak(): void
    {
        $this->makeUser(['email' => 'duplikat@example.com']);
        $this->step('usr-02', 'SETUP', 'Email sudah terdaftar', ['email' => 'duplikat@example.com']);

        $response = $this->post('/register', [
            'name' => 'Duplikat',
            'email' => 'duplikat@example.com',
            'phone' => '081234567891',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);
        $this->step('usr-02', 'ACT', 'POST /register dengan email duplikat', ['status' => $response->getStatusCode()]);

        $response->assertSessionHasErrors('email');
        $this->step('usr-02', 'ASSERT', 'Validasi menolak field email duplikat');
        $this->assertGuest();
        $this->step('usr-02', 'RESULT', 'Registrasi duplikat ditolak, tetap guest');
    }

    /** USR-01 (negatif): kredensial salah tidak bisa login. */
    public function test_usr_01_login_kredensial_salah_ditolak(): void
    {
        $this->makeUser(['email' => 'salah@example.com']);
        $this->step('usr-01', 'SETUP', 'Nasabah terdaftar', ['email' => 'salah@example.com']);

        $response = $this->post('/login', [
            'email' => 'salah@example.com',
            'password' => 'password-salah',
        ]);
        $this->step('usr-01', 'ACT', 'POST /login dengan password salah', ['status' => $response->getStatusCode()]);

        $response->assertSessionHasErrors('email');
        $this->step('usr-01', 'ASSERT', 'Error kredensial pada field email');
        $this->assertGuest();
        $this->step('usr-01', 'RESULT', 'Login gagal, tetap guest');
    }
}
