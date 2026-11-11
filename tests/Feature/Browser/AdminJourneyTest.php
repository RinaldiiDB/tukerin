<?php

it('redirects guests away from all protected role areas', function () {
    visit('/admin/dashboard')
        ->assertPathIs('/login')
        ->assertSee('Selamat Datang Kembali');

    visit('/employee/dashboard')
        ->assertPathIs('/login')
        ->assertSee('Selamat Datang Kembali');

    visit('/user/dashboard')
        ->assertPathIs('/login')
        ->assertSee('Selamat Datang Kembali');
});

it('allows guests to navigate between public authentication pages', function () {
    visit('/login')
        ->click('Daftar Sekarang')
        ->assertPathIs('/register')
        ->assertSee('Mulai Kumpulkan Kebaikan')
        ->click('Masuk di sini')
        ->assertPathIs('/login')
        ->assertSee('Selamat Datang Kembali');
});
