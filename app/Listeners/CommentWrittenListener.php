<?php

namespace App\Listeners;

use App\Concerns\Enums\AchievementTypeEnum;
use App\Events\CommentWritten;
use App\Services\AchievementService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CommentWrittenListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected AchievementService $achievementService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(CommentWritten $event): void
    {
        // we have $event->comment that we're not using
        $this->achievementService->unlockAchievement(
            user: $event->user,
            achievementType: AchievementTypeEnum::COMMENT
        );
    }
}
