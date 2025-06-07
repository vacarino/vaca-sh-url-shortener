<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ShortUrl;
use App\Models\ClickLog;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create regular users
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'collaborator',
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'role' => 'collaborator',
        ]);

        // Create sample short URLs
        $shortUrls = [
            [
                'user_id' => $user1->id,
                'original_url' => 'https://www.google.com',
                'short_code' => 'ggl123',
                'clicks' => 25,
            ],
            [
                'user_id' => $user1->id,
                'original_url' => 'https://github.com/laravel/laravel',
                'short_code' => 'lrvl01',
                'clicks' => 15,
            ],
            [
                'user_id' => $user2->id,
                'original_url' => 'https://tailwindcss.com',
                'short_code' => 'twcss1',
                'clicks' => 8,
            ],
            [
                'user_id' => $admin->id,
                'original_url' => 'https://laravel.com/docs',
                'short_code' => 'docs01',
                'clicks' => 42,
                'expires_at' => now()->addDays(30),
            ],
        ];

        foreach ($shortUrls as $urlData) {
            $shortUrl = ShortUrl::create($urlData);

            // Create sample click logs for each URL
            $countries = ['United States', 'Canada', 'United Kingdom', 'Germany', 'France', 'Japan'];
            $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge'];
            $platforms = ['Windows', 'macOS', 'Linux', 'Android', 'iOS'];

            for ($i = 0; $i < $shortUrl->clicks; $i++) {
                ClickLog::create([
                    'short_url_id' => $shortUrl->id,
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                    'country' => fake()->randomElement($countries),
                    'browser' => fake()->randomElement($browsers),
                    'platform' => fake()->randomElement($platforms),
                    'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
                ]);
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin credentials: admin@example.com / password');
        $this->command->info('User credentials: john@example.com / password');
        $this->command->info('User credentials: jane@example.com / password');
    }
} 