<?php

namespace Database\Seeders;

use App\Concerns\Enums\AchievementTypeEnum;
use App\Models\Achievement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommentAchievementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commentsWrittenTimestamp = $this->getCommentsWrittenAchievements()
            ->map(fn (array $achievement): array => [
               ...$achievement,
               'type' => AchievementTypeEnum::COMMENT,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        DB::table('achievements')->insert($commentsWrittenTimestamp->toArray());
    }

    private function getCommentsWrittenAchievements(): Collection
    {
        return collect([
            [
                'count' => 1,
                'text' => 'First Comment Written',
            ],
            [
                'count' => 3,
                'text' => '3 Comments Written',
            ],
            [
                'count' => 5,
                'text' => '5 Comments Written',
            ],
            [
                'count' => 10,
                'text' => '10 Comments Written',
            ],
            [
                'count' => 20,
                'text' => '20 Comments Written',
            ],
        ]);
    }
}
