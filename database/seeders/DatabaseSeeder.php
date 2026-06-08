<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\BottleType;
use App\Models\ExchangeTransaction;
use App\Models\ExchangeTransactionDetail;
use App\Models\RedemptionRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

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
        $petKecil = BottleType::create([
            'name' => 'Botol PET Kecil (600ml)',
            'barcode' => '60012345',
            'description' => 'Botol plastik PET bening ukuran sedang 600ml',
            'points_value' => 10,
        ]);

        $petBesar = BottleType::create([
            'name' => 'Botol PET Besar (1.5L)',
            'barcode' => '15012345',
            'description' => 'Botol plastik PET bening ukuran besar 1.5L',
            'points_value' => 25,
        ]);

        $kalengAlu = BottleType::create([
            'name' => 'Kaleng Aluminium (330ml)',
            'barcode' => '33012345',
            'description' => 'Kaleng aluminium bekas minuman bersoda',
            'points_value' => 40,
        ]);

        $botolKaca = BottleType::create([
            'name' => 'Botol Kaca Bening',
            'barcode' => '99912345',
            'description' => 'Botol kaca sirup/saus bening',
            'points_value' => 50,
        ]);

        // 3. Seed Admin User
        $admin = User::create([
            'name' => 'Administrator Tuker.in',
            'email' => 'admin@tuker.in',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
        ]);

        // 4. Seed Employees
        $employeeBudi = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@tuker.in',
            'password' => Hash::make('password123'),
            'role_id' => $employeeRole->id,
        ]);

        $employeeSiti = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@tuker.in',
            'password' => Hash::make('password123'),
            'role_id' => $employeeRole->id,
        ]);

        // 5. Seed Users (Nasabah)
        $nasabahsData = [
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad@tuker.in',
                'phone' => '08123456789',
                'qr_code' => 'TK-AHMADF1',
            ],
            [
                'name' => 'Rina Wijaya',
                'email' => 'rina@tuker.in',
                'phone' => '08234567890',
                'qr_code' => 'TK-RINAWJ2',
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'eko@tuker.in',
                'phone' => '08345678901',
                'qr_code' => 'TK-EKOPR33',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@tuker.in',
                'phone' => '08456789012',
                'qr_code' => 'TK-DEWILS4',
            ],
            [
                'name' => 'Bambang Susilo',
                'email' => 'bambang@tuker.in',
                'phone' => '08567890123',
                'qr_code' => 'TK-BAMBSS5',
            ]
        ];

        $nasabahs = [];
        foreach ($nasabahsData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password123'),
                'role_id' => $userRole->id,
            ]);

            $profile = UserProfile::create([
                'user_id' => $user->id,
                'phone' => $data['phone'],
                'qr_code' => $data['qr_code'],
                'points_balance' => 0, // Will be updated later
            ]);

            $nasabahs[$data['email']] = [
                'user' => $user,
                'profile' => $profile,
                'accumulated_points' => 0,
            ];
        }

        // 6. Helper function to create Transactions
        $createTransaction = function ($nasabahEmail, $employee, $daysAgo, $items) use (&$nasabahs) {
            $nasabah = &$nasabahs[$nasabahEmail];
            $totalPoints = 0;
            $details = [];

            foreach ($items as $item) {
                $points = $item['qty'] * $item['type']->points_value;
                $totalPoints += $points;
                $details[] = [
                    'bottle_type_id' => $item['type']->id,
                    'quantity' => $item['qty'],
                    'points_earned' => $points,
                ];
            }

            $transaction = ExchangeTransaction::create([
                'user_id' => $nasabah['user']->id,
                'employee_id' => $employee->id,
                'total_points' => $totalPoints,
                'transacted_at' => Carbon::now()->subDays($daysAgo),
            ]);

            foreach ($details as $detail) {
                ExchangeTransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'bottle_type_id' => $detail['bottle_type_id'],
                    'quantity' => $detail['quantity'],
                    'points_earned' => $detail['points_earned'],
                ]);
            }

            $nasabah['accumulated_points'] += $totalPoints;
        };

        // Seed Transactions
        // Ahmad Fauzi transactions
        $createTransaction('ahmad@tuker.in', $employeeBudi, 5, [
            ['qty' => 5, 'type' => $petKecil],
            ['qty' => 2, 'type' => $petBesar],
        ]); // 5*10 + 2*25 = 100 points
        $createTransaction('ahmad@tuker.in', $employeeSiti, 3, [
            ['qty' => 3, 'type' => $kalengAlu],
            ['qty' => 2, 'type' => $botolKaca],
        ]); // 3*40 + 2*50 = 220 points
        $createTransaction('ahmad@tuker.in', $employeeBudi, 2, [
            ['qty' => 10, 'type' => $petKecil],
        ]); // 10*10 = 100 points
        // Ahmad's Total Earned: 420 points

        // Rina Wijaya transactions
        $createTransaction('rina@tuker.in', $employeeSiti, 6, [
            ['qty' => 4, 'type' => $kalengAlu],
            ['qty' => 1, 'type' => $botolKaca],
        ]); // 4*40 + 1*50 = 210 points
        $createTransaction('rina@tuker.in', $employeeBudi, 4, [
            ['qty' => 8, 'type' => $petBesar],
        ]); // 8*25 = 200 points
        // Rina's Total Earned: 410 points

        // Eko Prasetyo transactions
        $createTransaction('eko@tuker.in', $employeeBudi, 8, [
            ['qty' => 1, 'type' => $botolKaca],
            ['qty' => 5, 'type' => $petKecil],
        ]); // 1*50 + 5*10 = 100 points
        // Eko's Total Earned: 100 points

        // Dewi Lestari transactions
        $createTransaction('dewi@tuker.in', $employeeSiti, 10, [
            ['qty' => 2, 'type' => $botolKaca],
        ]); // 2*50 = 100 points
        $createTransaction('dewi@tuker.in', $employeeBudi, 9, [
            ['qty' => 4, 'type' => $kalengAlu],
        ]); // 4*40 = 160 points
        $createTransaction('dewi@tuker.in', $employeeSiti, 7, [
            ['qty' => 6, 'type' => $petBesar],
            ['qty' => 5, 'type' => $petKecil],
        ]); // 6*25 + 5*10 = 200 points
        $createTransaction('dewi@tuker.in', $employeeBudi, 3, [
            ['qty' => 8, 'type' => $botolKaca],
        ]); // 8*50 = 400 points
        // Dewi's Total Earned: 860 points

        // 7. Seed Redemption Requests
        $pointRate = 200;

        // Ahmad Fauzi: 1 approved (150 pts), 1 pending (200 pts)
        RedemptionRequest::create([
            'user_id' => $nasabahs['ahmad@tuker.in']['user']->id,
            'points_used' => 150,
            'amount' => 150 * $pointRate,
            'method' => 'ewallet',
            'bank_name' => 'GOPAY',
            'recipient_account' => '08123456789',
            'status' => 'approved',
            'processed_at' => Carbon::now()->subDays(4),
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subDays(4),
        ]);
        $nasabahs['ahmad@tuker.in']['accumulated_points'] -= 150;

        RedemptionRequest::create([
            'user_id' => $nasabahs['ahmad@tuker.in']['user']->id,
            'points_used' => 200,
            'amount' => 200 * $pointRate,
            'method' => 'cash',
            'bank_name' => 'Cash',
            'recipient_account' => '-',
            'status' => 'pending',
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        // Rina Wijaya: 1 rejected (300 pts)
        RedemptionRequest::create([
            'user_id' => $nasabahs['rina@tuker.in']['user']->id,
            'points_used' => 300,
            'amount' => 300 * $pointRate,
            'method' => 'ewallet',
            'bank_name' => 'OVO',
            'recipient_account' => '08234567890',
            'status' => 'rejected',
            'rejection_note' => 'Nomor e-wallet tidak aktif atau tidak ditemukan.',
            'processed_at' => Carbon::now()->subDays(2),
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        // Dewi Lestari: 2 approved (200 pts and 300 pts), 1 pending (100 pts)
        RedemptionRequest::create([
            'user_id' => $nasabahs['dewi@tuker.in']['user']->id,
            'points_used' => 200,
            'amount' => 200 * $pointRate,
            'method' => 'ewallet',
            'bank_name' => 'DANA',
            'recipient_account' => '08456789012',
            'status' => 'approved',
            'processed_at' => Carbon::now()->subDays(5),
            'created_at' => Carbon::now()->subDays(6),
            'updated_at' => Carbon::now()->subDays(5),
        ]);
        $nasabahs['dewi@tuker.in']['accumulated_points'] -= 200;

        RedemptionRequest::create([
            'user_id' => $nasabahs['dewi@tuker.in']['user']->id,
            'points_used' => 300,
            'amount' => 300 * $pointRate,
            'method' => 'ewallet',
            'bank_name' => 'LinkAja',
            'recipient_account' => '08456789012',
            'status' => 'approved',
            'processed_at' => Carbon::now()->subDays(1),
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(1),
        ]);
        $nasabahs['dewi@tuker.in']['accumulated_points'] -= 300;

        RedemptionRequest::create([
            'user_id' => $nasabahs['dewi@tuker.in']['user']->id,
            'points_used' => 100,
            'amount' => 100 * $pointRate,
            'method' => 'cash',
            'bank_name' => 'Cash',
            'recipient_account' => '-',
            'status' => 'pending',
            'created_at' => Carbon::now()->subHours(12),
            'updated_at' => Carbon::now()->subHours(12),
        ]);

        // 8. Update points balance on user profiles
        foreach ($nasabahs as $email => $nasabah) {
            $nasabah['profile']->update([
                'points_balance' => max(0, $nasabah['accumulated_points'])
            ]);
        }
    }
}
