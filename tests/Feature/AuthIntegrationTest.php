<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Support\CreateFixtures;
use Tests\TestCase;

/**
 * Auth: USR-01, EMP-01, ADM-01, USR-02
 */
class AuthIntegrationTest extends TestCase
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

    /** USR-01: Login sebagai Nasabah -> diarahkan ke dashboard user. */
    public function test_usr_01_login_nasabah_redirect_dashboard(): void
    {
        $user = $this->makeUser(['email' => 'nasabah-usr01@example.com']);

        $response = $this->post('/login', [
            'email' => 'nasabah-usr01@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /** EMP-01: Login sebagai Pegawai -> diarahkan ke dashboard employee. */
    public function test_emp_01_login_pegawai_redirect_dashboard(): void
    {
        $employee = $this->makeEmployee(['email' => 'pegawai-emp01@example.com']);

        $response = $this->post('/login', [
            'email' => 'pegawai-emp01@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('employee.dashboard'));
        $this->assertAuthenticatedAs($employee);
    }

    /** ADM-01: Login sebagai Admin -> diarahkan ke dashboard admin. */
    public function test_adm_01_login_admin_redirect_dashboard(): void
    {
        $admin = $this->makeAdmin(['email' => 'admin-adm01@example.com']);

        $response = $this->post('/login', [
            'email' => 'admin-adm01@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    /** USR-02: Registrasi Nasabah valid -> akun + profile + role user + QR Code dibuat. */
    public function test_usr_02_registrasi_nasabah_berhasil(): void
    {
        $response = $this->post('/register', [
            'name' => 'Nasabah Baru',
            'email' => 'nasabah-baru@example.com',
            'phone' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', ['email' => 'nasabah-baru@example.com']);
        $user = \App\Models\User::where('email', 'nasabah-baru@example.com')->first();
        $this->assertEquals('user', $user->role->name);

        $this->assertDatabaseHas('user_profiles', ['user_id' => $user->id]);
        $this->assertNotNull($user->profile->qr_code);
        $this->assertStringStartsWith('TK-', $user->profile->qr_code);
        $this->assertEquals(0, $user->profile->points_balance);
    }

    /** USR-02 (negatif): registrasi dengan email duplikat ditolak. */
    public function test_usr_02_registrasi_email_duplikat_ditolak(): void
    {
        $this->makeUser(['email' => 'duplikat@example.com']);

        $response = $this->post('/register', [
            'name' => 'Duplikat',
            'email' => 'duplikat@example.com',
            'phone' => '081234567891',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** USR-01 (negatif): kredensial salah tidak bisa login. */
    public function test_usr_01_login_kredensial_salah_ditolak(): void
    {
        $this->makeUser(['email' => 'salah@example.com']);

        $response = $this->post('/login', [
            'email' => 'salah@example.com',
            'password' => 'password-salah',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
