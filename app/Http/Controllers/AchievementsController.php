<?php

namespace App\Http\Controllers;

use App\Concerns\Enums\AchievementTypeEnum;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user): JsonResponse
    {
        $unlockedAchievements = $user->unlockedAchievements()->orderBy('count');

        $lessonAchievement = $unlockedAchievements
            ->where('type', AchievementTypeEnum::LESSON)
            ->get()
            ->last();

        $commentAchievement = $unlockedAchievements
            ->where('type', AchievementTypeEnum::COMMENT)
            ->get()
            ->last();

        $lessonAchievementsTobeUnlocked = Achievement::query()
            ->where('type', AchievementTypeEnum::LESSON)
            ->where('count', '>', $lessonAchievement?->count ?? 0)
            ->get();

        $commentAchievementsTobeUnlocked = Achievement::query()
            ->where('type', AchievementTypeEnum::COMMENT)
            ->where('count', '>', $commentAchievement?->count ?? 0)
            ->get();

        $nextAvailableAchievements = $lessonAchievementsTobeUnlocked->merge($commentAchievementsTobeUnlocked)
            ->pluck('text');

        $currentBadge = $user?->badge?->badge;
        $nextBadge = Badge::query()
            ->where('required_achievements', '>', $currentBadge?->required_achievements ?? 0)
            ->orderBy('required_achievements')
            ->first();

        return response()->json([
            'unlocked_achievements' => $user->unlockedAchievements()->pluck('text'),
            'next_available_achievements' => $nextAvailableAchievements,
            'current_badge' => $currentBadge?->name,
            'next_badge' => $nextBadge?->name,
            'remaing_to_unlock_next_badge' => $nextBadge?->required_achievements - $user->unlockedAchievements()->count()
        ]);
    }
}
