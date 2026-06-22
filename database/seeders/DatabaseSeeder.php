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
        $aqua600 = BottleType::create([
            'name' => 'Botol PET Aqua 600ml',
            'barcode' => '8992769100100',
            'description' => 'Botol air mineral Aqua PET bening ukuran 600ml',
            'points_value' => 10,
        ]);

        $leMinerale600 = BottleType::create([
            'name' => 'Botol PET Le Minerale 600ml',
            'barcode' => '8997002120015',
            'description' => 'Botol air mineral Le Minerale PET bening 600ml',
            'points_value' => 10,
        ]);

        $cocaCola390 = BottleType::create([
            'name' => 'Botol PET Coca-Cola 390ml',
            'barcode' => '8998123120018',
            'description' => 'Botol plastik Coca-Cola 390ml siap minum',
            'points_value' => 8,
        ]);

        $sprite390 = BottleType::create([
            'name' => 'Botol PET Sprite 390ml',
            'barcode' => '8998123120025',
            'description' => 'Botol plastik Sprite 390ml siap minum',
            'points_value' => 8,
        ]);

        $tehBotol500 = BottleType::create([
            'name' => 'Botol PET Teh Botol Sosro 500ml',
            'barcode' => '8991002100123',
            'description' => 'Botol Teh Botol Sosro PET bening 500ml',
            'points_value' => 12,
        ]);

        $aquaGalon15 = BottleType::create([
            'name' => 'Botol Galon PET Aqua 1.5L',
            'barcode' => '8992769200200',
            'description' => 'Botol galon kecil Aqua PET 1.5 liter',
            'points_value' => 25,
        ]);

        $club15 = BottleType::create([
            'name' => 'Botol PET Club 1.5L',
            'barcode' => '8991111100010',
            'description' => 'Botol air mineral Club PET bening ukuran 1.5L',
            'points_value' => 25,
        ]);

        $aleAle500 = BottleType::create([
            'name' => 'Botol PET Ale-Ale 500ml',
            'barcode' => '8992222200011',
            'description' => 'Botol PET Ale-Ale minuman rasa buah 500ml',
            'points_value' => 12,
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
            ['qty' => 5, 'type' => $aqua600],
            ['qty' => 2, 'type' => $aquaGalon15],
        ]); // 5*10 + 2*25 = 100 points
        $createTransaction('ahmad@tuker.in', $employeeSiti, 3, [
            ['qty' => 3, 'type' => $cocaCola390],
            ['qty' => 2, 'type' => $tehBotol500],
        ]); // 3*8 + 2*12 = 48 points
        $createTransaction('ahmad@tuker.in', $employeeBudi, 2, [
            ['qty' => 10, 'type' => $aqua600],
        ]); // 10*10 = 100 points
        // Ahmad's Total Earned: 248 points

        // Rina Wijaya transactions
        $createTransaction('rina@tuker.in', $employeeSiti, 6, [
            ['qty' => 4, 'type' => $cocaCola390],
            ['qty' => 1, 'type' => $tehBotol500],
        ]); // 4*8 + 1*12 = 44 points
        $createTransaction('rina@tuker.in', $employeeBudi, 4, [
            ['qty' => 8, 'type' => $aquaGalon15],
        ]); // 8*25 = 200 points
        // Rina's Total Earned: 244 points

        // Eko Prasetyo transactions
        $createTransaction('eko@tuker.in', $employeeBudi, 8, [
            ['qty' => 1, 'type' => $tehBotol500],
            ['qty' => 5, 'type' => $aqua600],
        ]); // 1*12 + 5*10 = 62 points
        // Eko's Total Earned: 62 points

        // Dewi Lestari transactions
        $createTransaction('dewi@tuker.in', $employeeSiti, 10, [
            ['qty' => 2, 'type' => $tehBotol500],
        ]); // 2*12 = 24 points
        $createTransaction('dewi@tuker.in', $employeeBudi, 9, [
            ['qty' => 4, 'type' => $cocaCola390],
        ]); // 4*8 = 32 points
        $createTransaction('dewi@tuker.in', $employeeSiti, 7, [
            ['qty' => 6, 'type' => $aquaGalon15],
            ['qty' => 5, 'type' => $aqua600],
        ]); // 6*25 + 5*10 = 200 points
        $createTransaction('dewi@tuker.in', $employeeBudi, 3, [
            ['qty' => 8, 'type' => $tehBotol500],
        ]); // 8*12 = 96 points
        // Dewi's Total Earned: 352 points

        // 7. Seed Redemption Requests
        $pointRate = 200;

        // Ahmad Fauzi: 1 approved (150 pts), 1 pending (80 pts)
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
            'points_used' => 80,
            'amount' => 80 * $pointRate,
            'method' => 'cash',
            'bank_name' => 'Cash',
            'recipient_account' => '-',
            'status' => 'pending',
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        // Rina Wijaya: 1 rejected (200 pts)
        RedemptionRequest::create([
            'user_id' => $nasabahs['rina@tuker.in']['user']->id,
            'points_used' => 200,
            'amount' => 200 * $pointRate,
            'method' => 'ewallet',
            'bank_name' => 'OVO',
            'recipient_account' => '08234567890',
            'status' => 'rejected',
            'rejection_note' => 'Nomor e-wallet tidak aktif atau tidak ditemukan.',
            'processed_at' => Carbon::now()->subDays(2),
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        // Dewi Lestari: 1 approved (150 pts), 1 pending (80 pts)
        RedemptionRequest::create([
            'user_id' => $nasabahs['dewi@tuker.in']['user']->id,
            'points_used' => 150,
            'amount' => 150 * $pointRate,
            'method' => 'ewallet',
            'bank_name' => 'DANA',
            'recipient_account' => '08456789012',
            'status' => 'approved',
            'processed_at' => Carbon::now()->subDays(5),
            'created_at' => Carbon::now()->subDays(6),
            'updated_at' => Carbon::now()->subDays(5),
        ]);
        $nasabahs['dewi@tuker.in']['accumulated_points'] -= 150;

        RedemptionRequest::create([
            'user_id' => $nasabahs['dewi@tuker.in']['user']->id,
            'points_used' => 80,
            'amount' => 80 * $pointRate,
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
