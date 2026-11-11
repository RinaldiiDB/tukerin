<?php

it('can open the login page', function () {
    $page = visit('/login');

    $page
        ->assertSee('Selamat Datang Kembali')
        ->assertSee('Alamat Email')
        ->assertSee('Password')
        ->assertSee('Masuk ke Akun');
});
