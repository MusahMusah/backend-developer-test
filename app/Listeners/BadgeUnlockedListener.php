<?php

namespace App\Listeners;

use App\Events\BadgeUnlocked;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BadgeUnlockedListener
{
    /**
     * Handle the event.
     */
    public function handle(BadgeUnlocked $event): void
    {
        $this->createUserBadge($event->user, $event->badge_name);
    }

    private function createUserBadge(User $user, string $badgeName): void
    {
        // Find or create temporary badge instance
        $badge = Badge::query()->where('name', $badgeName)->firstOrNew();

        // Check if the user already has a badge
        $existingUserBadge = $user->badge;

        // Update the existing user badge or create a new one
        $existingUserBadge
            ? $existingUserBadge->badge()->associate($badge)->save()
            : $user->badge()->create(['badge_id' => $badge->id]);
    }
}
