<?php

namespace App\Listeners;

use App\Concerns\Enums\AchievementTypeEnum;
use App\Events\LessonWatched;
use App\Services\AchievementService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LessonWatchedListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected AchievementService $achievementService)
    {}

    /**
     * Handle the event.
     */
    public function handle(LessonWatched $event): void
    {
        $this->achievementService->unlockAchievement(
            user: $event->user,
            achievementType: AchievementTypeEnum::LESSON
        );
    }
}
