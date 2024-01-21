<?php

namespace Tests\Feature;

use App\Concerns\Enums\AchievementTypeEnum;
use App\Events\AchievementUnlocked;
use App\Listeners\AchievementUnlockedListener;
use App\Models\Achievement;
use App\Models\Comment;
use App\Models\User;
use App\Services\AchievementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AchievementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_unlocks_achievement_for_comment_type(): void
    {
        $commentAchievement = Achievement::factory()->create([
            'type' => AchievementTypeEnum::COMMENT,
            'count' => 1,
        ]);
        $user = User::factory()->create();
        Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        // Mock the event to assert later
        Event::fake();

        $service = app(AchievementService::class);
        $service->unlockAchievement($user, AchievementTypeEnum::COMMENT);
        $user->refresh();

        Event::assertDispatched(
            AchievementUnlocked::class,
            fn ($event) => ($event->achievement_name === $commentAchievement->text && $event->user === $user)
        );

        Event::assertListening(
            AchievementUnlocked::class,
            AchievementUnlockedListener::class,
        );

        $this->assertCount(1, $user->unlockedAchievements);
        $this->assertTrue($user->unlockedAchievements->contains($commentAchievement));
    }
}
