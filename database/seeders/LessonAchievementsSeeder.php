<?php

namespace Database\Seeders;

use App\Concerns\Enums\AchievementTypeEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LessonAchievementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lessonsWatchedTimestamp = $this->getLessonsWrittenAchievements()
            ->map(fn (array $achievement): array => [
                ...$achievement,
                'type' => AchievementTypeEnum::LESSON,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        DB::table('achievements')->insert($lessonsWatchedTimestamp->toArray());
    }

    private function getLessonsWrittenAchievements(): Collection
    {
        return collect([
            [
                'count' => 1,
                'text' => 'First Lesson Watched',
            ],
            [
                'count' => 5,
                'text' => '5 Lesson Watched',
            ],
            [
                'count' => 10,
                'text' => '10 Lesson Watched',
            ],
            [
                'count' => 25,
                'text' => '25 Lesson Watched',
            ],
            [
                'count' => 50,
                'text' => '50 Lesson Watched',
            ],
        ]);
    }
}
