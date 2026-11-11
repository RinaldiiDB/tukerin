<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

function createBrowserEmployee(): void
{
    $role = Role::create(['name' => 'employee', 'label' => 'Pegawai']);

    User::create([
        'name' => 'Browser Pegawai',
        'email' => 'browser-pegawai@example.com',
        'password' => Hash::make('password123'),
        'role_id' => $role->id,
    ]);
}

function loginAsBrowserEmployee()
{
    createBrowserEmployee();

    return visit('/login')
        ->fill('email', 'browser-pegawai@example.com')
        ->fill('password', 'password123')
        ->click('Masuk ke Akun')
        ->assertPathIs('/employee/dashboard');
}

it('allows an employee to open the dashboard and scanner', function () {
    loginAsBrowserEmployee()
        ->assertSee('Dasbor Pegawai')
        ->assertSee('Operasional Hari Ini')
        ->click('Mulai Scan Botol')
        ->assertPathIs('/employee/scan')
        ->assertSee('Scanner Transaksi')
        ->assertSee('Tahap 1: Pindai QR Code Nasabah')
        ->assertSee('Input Manual (Alternatif)')
        ->assertSee('Cari');
});

it('allows an employee to open processed transaction history', function () {
    loginAsBrowserEmployee()
        ->click('Lihat Semua Riwayat')
        ->assertPathIs('/employee/transactions')
        ->assertSee('Riwayat Transaksi Diproses')
        ->assertSee('Anda belum pernah memproses transaksi penukaran.');
});
