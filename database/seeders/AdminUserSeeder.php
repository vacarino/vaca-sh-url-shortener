<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\InviteCode;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if it doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@vaca.sh'],
            [
                'name' => 'Vaca.Sh Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        echo "Admin user created: admin@vaca.sh / password\n";

        // Create some initial invite codes
        $codes = [
            ['code' => 'WELCOME2024', 'description' => 'Welcome code for 2024'],
            ['code' => 'BETA_TEST', 'description' => 'Beta tester invite'],
            ['code' => 'FRIEND_INVITE', 'description' => 'Friend invitation'],
        ];

        foreach ($codes as $codeData) {
            InviteCode::firstOrCreate(
                ['code' => $codeData['code']],
                [
                    'description' => $codeData['description'],
                    'created_by' => $admin->id,
                    'is_active' => true,
                    'is_single_use' => true,
                ]
            );
        }

        echo "Initial invite codes created: WELCOME2024, BETA_TEST, FRIEND_INVITE\n";
    }
} 