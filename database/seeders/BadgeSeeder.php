<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badgesWithTimeStamps = $this->getBadges()
            ->map(fn (array $badge): array => [
                ...$badge,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        DB::table('badges')->insert($badgesWithTimeStamps->toArray());
    }

    private function getBadges(): Collection
    {
        return collect([
            ['name' => 'Beginner', 'required_achievements' => 0],
            ['name' => 'Intermediate', 'required_achievements' => 4],
            ['name' => 'Advanced', 'required_achievements' => 8],
            ['name' => 'Master', 'required_achievements' => 10],
        ]);
    }
}
