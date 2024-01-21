<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Models\Badge;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AchievementUnlockedListener
{
    /**
     * Handle the event.
     */
    public function handle(AchievementUnlocked $event): void
    {
        $unlockedAchievementsCount = $event->user->unlockedAchievements()->count();

        $badgeTobeUnlocked = Badge::query()
            ->where('required_achievements', '<=', $unlockedAchievementsCount)
            ->orderBy('required_achievements')
            ->get()
            ->last();

        if ($badgeTobeUnlocked) {
            BadgeUnlocked::dispatch($badgeTobeUnlocked->name, $event->user);
        }
    }
}
