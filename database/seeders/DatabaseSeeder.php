<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\BottleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles
        $adminRole = Role::create(['name' => 'admin', 'label' => 'Admin']);
        $employeeRole = Role::create(['name' => 'employee', 'label' => 'Pegawai']);
        $userRole = Role::create(['name' => 'user', 'label' => 'User']);

        // 2. Seed Bottle Types
        BottleType::create([
            'name' => 'Botol PET Kecil (600ml)',
            'barcode' => '60012345',
            'description' => 'Botol plastik PET bening ukuran sedang 600ml',
            'points_value' => 10,
        ]);

        BottleType::create([
            'name' => 'Botol PET Besar (1.5L)',
            'barcode' => '15012345',
            'description' => 'Botol plastik PET bening ukuran besar 1.5L',
            'points_value' => 25,
        ]);

        BottleType::create([
            'name' => 'Kaleng Aluminium (330ml)',
            'barcode' => '33012345',
            'description' => 'Kaleng aluminium bekas minuman bersoda',
            'points_value' => 40,
        ]);

        BottleType::create([
            'name' => 'Botol Kaca Bening',
            'barcode' => '99912345',
            'description' => 'Botol kaca sirup/saus bening',
            'points_value' => 50,
        ]);

        // 3. Seed Admin User
        User::create([
            'name' => 'Administrator Tuker.in',
            'email' => 'admin@tuker.in',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
        ]);
    }
}
