<?php

namespace Tests\Feature\Support;

use App\Models\BottleType;
use App\Models\ExchangeTransaction;
use App\Models\RedemptionRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Helper untuk integration testing Tuker.in.
 */
trait CreateFixtures
{
    protected function ensureRoles(): array
    {
        $user = Role::firstOrCreate(['name' => 'user'], ['label' => 'User']);
        $employee = Role::firstOrCreate(['name' => 'employee'], ['label' => 'Pegawai']);
        $admin = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Admin']);

        return compact('user', 'employee', 'admin');
    }

    protected function roleId(string $name): int
    {
        $roles = $this->ensureRoles();

        return $roles[$name]->id;
    }

    protected function makeUser(array $overrides = []): User
    {
        $this->ensureRoles();

        $email = $overrides['email'] ?? 'user-'.Str::lower(Str::random(6)).'@example.com';
        $password = $overrides['password_plain'] ?? 'password123';

        $user = User::create([
            'name' => $overrides['name'] ?? 'Nasabah Test',
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => $overrides['role_id'] ?? $this->roleId('user'),
        ]);

        // Simpan plain password untuk kebutuhan login di test.
        $user->password_plain = $password;

        $qrCode = $overrides['qr_code'] ?? 'TK-'.strtoupper(Str::random(8));

        UserProfile::create([
            'user_id' => $user->id,
            'phone' => $overrides['phone'] ?? '081200000001',
            'qr_code' => $qrCode,
            'points_balance' => $overrides['points_balance'] ?? 0,
        ]);

        return $user->load('profile', 'role');
    }

    protected function makeEmployee(array $overrides = []): User
    {
        $this->ensureRoles();

        $password = $overrides['password_plain'] ?? 'password123';

        $employee = User::create([
            'name' => $overrides['name'] ?? 'Pegawai Test',
            'email' => $overrides['email'] ?? 'emp-'.Str::lower(Str::random(6)).'@example.com',
            'password' => Hash::make($password),
            'role_id' => $this->roleId('employee'),
        ]);

        $employee->password_plain = $password;

        return $employee->load('role');
    }

    protected function makeAdmin(array $overrides = []): User
    {
        $this->ensureRoles();

        $password = $overrides['password_plain'] ?? 'password123';

        $admin = User::create([
            'name' => $overrides['name'] ?? 'Admin Test',
            'email' => $overrides['email'] ?? 'adm-'.Str::lower(Str::random(6)).'@example.com',
            'password' => Hash::make($password),
            'role_id' => $this->roleId('admin'),
        ]);

        $admin->password_plain = $password;

        return $admin->load('role');
    }

    protected function makeBottle(array $overrides = []): BottleType
    {
        return BottleType::create([
            'name' => $overrides['name'] ?? 'Botol Test '.Str::random(4),
            'barcode' => $overrides['barcode'] ?? '899'.rand(1000000000, 9999999999),
            'description' => $overrides['description'] ?? null,
            'points_value' => $overrides['points_value'] ?? 10,
        ]);
    }

    protected function giveBalance(User $user, int $balance): UserProfile
    {
        $profile = UserProfile::where('user_id', $user->id)->firstOrFail();
        $profile->update(['points_balance' => $balance]);

        return $profile->fresh();
    }

    protected function makeTransaction(User $nasabah, User $employee, int $totalPoints = 100): ExchangeTransaction
    {
        return ExchangeTransaction::create([
            'user_id' => $nasabah->id,
            'employee_id' => $employee->id,
            'total_points' => $totalPoints,
            'transacted_at' => Carbon::now(),
        ]);
    }

    protected function makeRedemption(User $nasabah, array $overrides = []): RedemptionRequest
    {
        return RedemptionRequest::create([
            'user_id' => $nasabah->id,
            'points_used' => $overrides['points_used'] ?? 50,
            'amount' => $overrides['amount'] ?? (($overrides['points_used'] ?? 50) * 200),
            'method' => $overrides['method'] ?? 'cash',
            'bank_name' => $overrides['bank_name'] ?? 'BCA',
            'recipient_account' => $overrides['recipient_account'] ?? '1234567890',
            'status' => $overrides['status'] ?? 'pending',
            'rejection_note' => $overrides['rejection_note'] ?? null,
            'processed_at' => $overrides['processed_at'] ?? null,
        ]);
    }
}
