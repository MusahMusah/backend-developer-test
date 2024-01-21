<?php

declare(strict_types=1);

namespace App\Services;

use App\Concerns\Enums\AchievementTypeEnum;
use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\User;

class AchievementService
{
    public function unlockAchievement(User $user, AchievementTypeEnum $achievementType): void
    {
        $achievementTypeMapping = match ($achievementType) {
            AchievementTypeEnum::COMMENT => $user->comments(),
            AchievementTypeEnum::LESSON => $user->lessons(),
        };

        $itemsViewed = $achievementTypeMapping->count();

        $newAchievements = Achievement::query()
            ->where('type', $achievementType)
            ->where('count', '<=', $itemsViewed)
            ->whereNotIn('id', $user->unlockedAchievements->pluck('id'))
            ->get();

        if ($newAchievements->isNotEmpty()) {
            $newAchievements->each(function ($achievement) use ($user) {
                $this->createAchievementForUser($user, $achievement);

                event(new AchievementUnlocked($achievement->text, $user));
            });
        }
    }

    private function createAchievementForUser(User $user, Achievement $achievement): void
    {
        $user->userAchievements()->attach($achievement);
    }
}

