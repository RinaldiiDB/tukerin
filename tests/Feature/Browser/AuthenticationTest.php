<?php

use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;

function createAuthenticationUser(): void
{
    $role = Role::create([
        'name' => 'user',
        'label' => 'User',
    ]);

    $user = User::create([
        'name' => 'Browser User',
        'email' => 'browser-user@example.com',
        'password' => Hash::make('password123'),
        'role_id' => $role->id,
    ]);

    UserProfile::create([
        'user_id' => $user->id,
        'phone' => '081234567890',
        'qr_code' => 'TK-BROWSER01',
        'points_balance' => 125,
    ]);
}

it('allows a registered user to log in through the browser', function () {
    createAuthenticationUser();

    visit('/login')
        ->fill('email', 'browser-user@example.com')
        ->fill('password', 'password123')
        ->click('Masuk ke Akun')
        ->assertPathIs('/user/dashboard')
        ->assertSee('Saldo Aktif Anda')
        ->assertSee('125 Poin')
        ->assertSee('Hai, Browser User');
});

it('shows a validation error for invalid browser login credentials', function () {
    visit('/login')
        ->fill('email', 'unknown@example.com')
        ->fill('password', 'wrong-password')
        ->click('Masuk ke Akun')
        ->assertPathIs('/login')
        ->assertSee('Email atau password yang Anda masukkan salah.');
});

it('allows a visitor to register as a nasabah through the browser', function () {
    Role::create(['name' => 'user', 'label' => 'User']);

    visit('/register')
        ->fill('name', 'Browser Registrant')
        ->fill('email', 'browser-registrant@example.com')
        ->fill('phone', '081298765432')
        ->fill('password', 'password123')
        ->fill('password_confirmation', 'password123')
        ->click('Buat Akun Nasabah')
        ->assertPathIs('/user/dashboard')
        ->assertSee('Registrasi berhasil! Selamat datang di Tuker.in.')
        ->assertSee('Hai, Browser Registrant')
        ->assertSee('0 Poin');

    $user = User::where('email', 'browser-registrant@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->profile->phone)->toBe('081298765432');
    expect($user->profile->qr_code)->toStartWith('TK-');
});
