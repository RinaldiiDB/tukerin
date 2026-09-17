<?php

namespace Tests\Feature\Support;

/**
 * Flag bersama antar test class agar file log integration testing
 * hanya dikosongkan sekali per proses `php artisan test`.
 * (Static property di trait bersifat per-class, jadi tidak bisa dipakai.)
 */
final class IntegrationLog
{
    public static bool $started = false;
}
