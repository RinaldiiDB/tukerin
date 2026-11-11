<?php

use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;

function createBrowserUser(): void
{
    $role = Role::create(['name' => 'user', 'label' => 'User']);

    $user = User::create([
        'name' => 'Browser Nasabah',
        'email' => 'browser-nasabah@example.com',
        'password' => Hash::make('password123'),
        'role_id' => $role->id,
    ]);

    UserProfile::create([
        'user_id' => $user->id,
        'phone' => '081234567890',
        'qr_code' => 'TK-BROWSERUSER',
        'points_balance' => 500,
    ]);
}

function loginAsBrowserUser()
{
    createBrowserUser();

    return visit('/login')
        ->fill('email', 'browser-nasabah@example.com')
        ->fill('password', 'password123')
        ->click('Masuk ke Akun')
        ->assertPathIs('/user/dashboard');
}

it('allows a user to navigate from dashboard to qr and reward pages', function () {
    loginAsBrowserUser()
        ->assertSee('500 Poin')
        ->click('Tampilkan QR')
        ->assertPathIs('/user/qr')
        ->assertSee('QR Code Nasabah')
        ->assertSee('TK-BROWSERUSER')
        ->click('Kembali ke Beranda')
        ->click('Cairkan Poin')
        ->assertPathIs('/user/rewards/create')
        ->assertSee('Ajukan Pencairan Poin');
});

it('allows a user to open transaction and reward history pages', function () {
    loginAsBrowserUser()
        ->click('Lihat Semua')
        ->assertPathIs('/user/transactions')
        ->assertSee('Histori Penukaran Botol')
        ->navigate('/user/rewards')
        ->assertSee('Riwayat Pencairan Poin');
});

it('allows a user to log out from the browser', function () {
    loginAsBrowserUser()
        ->click('Keluar')
        ->assertPathIs('/login')
        ->assertSee('Anda telah berhasil logout.');
});
