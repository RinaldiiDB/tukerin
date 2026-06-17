<?php

namespace Tests\Feature;

use App\Models\BottleType;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;

trait CreatesTestFixtures
{
    protected Role $adminRole;
    protected Role $employeeRole;
    protected Role $userRole;

    protected User $adminUser;
    protected User $employeeUser;
    protected User $normalUser;

    protected BottleType $bottle1;
    protected BottleType $bottle2;

    protected function setUpFixtures(): void
    {
        $this->adminRole = Role::create(['name' => 'admin', 'label' => 'Admin']);
        $this->employeeRole = Role::create(['name' => 'employee', 'label' => 'Pegawai']);
        $this->userRole = Role::create(['name' => 'user', 'label' => 'User']);

        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role_id' => $this->adminRole->id,
        ]);

        $this->employeeUser = User::create([
            'name' => 'Employee Test',
            'email' => 'employee@test.com',
            'password' => Hash::make('password'),
            'role_id' => $this->employeeRole->id,
        ]);

        $this->normalUser = User::create([
            'name' => 'Nasabah Test',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'role_id' => $this->userRole->id,
        ]);

        UserProfile::create([
            'user_id' => $this->normalUser->id,
            'phone' => '0812345678',
            'qr_code' => 'TK-TEST1234',
            'points_balance' => 100,
        ]);

        $this->bottle1 = BottleType::create([
            'name' => 'Botol Kecil',
            'barcode' => '11111',
            'points_value' => 10,
        ]);

        $this->bottle2 = BottleType::create([
            'name' => 'Botol Besar',
            'barcode' => '22222',
            'points_value' => 25,
        ]);
    }
}
