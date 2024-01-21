<?php

namespace Database\Seeders;

use App\Models\Lesson;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    protected const DEMO_USER_EMAIL = 'user@iphonephotographyschool.com';

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory([
            'name' => 'Demo User',
            'email' => self::DEMO_USER_EMAIL,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])
        ->has(Lesson::factory()->count(20))
        ->create();

        $this->call([
            CommentSeeder::class,
            BadgeSeeder::class,
            CommentAchievementsSeeder::class,
            LessonAchievementsSeeder::class,
        ]);
    }
}
