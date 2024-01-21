<?php

namespace Tests\Feature;

use App\Concerns\Enums\AchievementTypeEnum;
use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Listeners\AchievementUnlockedListener;
use App\Listeners\BadgeUnlockedListener;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Comment;
use App\Models\User;
use App\Services\AchievementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BadgeTest extends TestCase
{
    Use RefreshDatabase;

    /** @test */
    public function it_creates_a_user_badge()
    {
        $user = User::factory()->create();
        $commentAchievement = Achievement::factory()->create([
            'type' => AchievementTypeEnum::COMMENT,
            'count' => 1,
        ]);

        DB::table('badges')->insert(
            [
                ['name' => 'Beginner', 'required_achievements' => 0],
                ['name' => 'Intermediate', 'required_achievements' => 4],
                ['name' => 'Advanced', 'required_achievements' => 8],
                ['name' => 'Master', 'required_achievements' => 10],
            ]
        );

        Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        // Mock the event to assert later
        Event::fake();

        $service = app(AchievementService::class); // Resolve through the container
        $service->unlockAchievement($user, AchievementTypeEnum::COMMENT);
        $user->refresh();

        $listener = new AchievementUnlockedListener();
        $listener->handle(new AchievementUnlocked($user->unlockedAchievements->first()->text, $user));

        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($commentAchievement, $user) {
            return $event->achievement_name === $commentAchievement->text
                && $event->user === $user;
        });

        Event::assertListening(
            AchievementUnlocked::class,
            AchievementUnlockedListener::class
        );

        Event::assertDispatched(BadgeUnlocked::class, function ($event) use ($user,) {
            $listener = new BadgeUnlockedListener();
            $listener->handle(new BadgeUnlocked($event->badge_name, $user));
            $user->refresh();

            return $event->badge_name === $user->badge->badge->name && $event->user === $user;
        });

        Event::assertListening(
            BadgeUnlocked::class,
            BadgeUnlockedListener::class
        );

        $this->assertDatabaseHas('badges', $user->badge->badge->toArray());
        $this->assertCount(1, $user->unlockedAchievements);
        $this->assertSame(1, $user->badge()->count());
        $this->assertTrue($user->unlockedAchievements->contains($commentAchievement));
    }

    /** @test */
    public function it_does_not_create_badge_when_achievements_does_not_meet_required_count(): void
    {
        // Mock the events to assert later
        Event::fake([
            AchievementUnlocked::class,
            BadgeUnlocked::class,
        ]);

        $user = User::factory()->create();
        Achievement::factory()->create([
            'type' => AchievementTypeEnum::COMMENT,
            'count' => 1,
        ]);

        Comment::factory()->create([
            'user_id' => $user->id
        ]);

        Badge::create(['name' => 'Beginner', 'required_achievements' => 8]);

        $service = app(AchievementService::class);
        $service->unlockAchievement($user, AchievementTypeEnum::COMMENT);
        $user->refresh();

        $listener = new AchievementUnlockedListener();
        $listener->handle(new AchievementUnlocked($user->unlockedAchievements->first()->text, $user));

        Event::assertNotDispatched(BadgeUnlocked::class);
        Event::assertListening(
            AchievementUnlocked::class,
            AchievementUnlockedListener::class
        );
        $this->assertNull($user->badge);
    }

    /** @test */
    public function it_create_badge_for_zero_achievements_when_first_comment_is_written(): void
    {
        $user = User::factory()->create();
        $commentAchievement = Achievement::factory()->create([
            'type' => AchievementTypeEnum::COMMENT,
            'count' => 1,
        ]);

        Comment::factory()->create([
            'user_id' => $user->id
        ]);

        Badge::create(['name' => 'Beginner', 'required_achievements' => 0]);

        // Mock the events to assert later
        Event::fake([
            AchievementUnlocked::class,
            BadgeUnlocked::class,
        ]);
        $service = app(AchievementService::class);
        $service->unlockAchievement($user, AchievementTypeEnum::COMMENT);
        $user->refresh();

        $listener = new AchievementUnlockedListener();
        $listener->handle(new AchievementUnlocked($user->unlockedAchievements->first()->text, $user));

        Event::assertDispatched(BadgeUnlocked::class, function ($event) use ($user,) {
            $listener = new BadgeUnlockedListener();
            $listener->handle(new BadgeUnlocked($event->badge_name, $user));
            $user->refresh();

            return $event->badge_name === $user->badge->badge->name && $event->user === $user;
        });

        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($commentAchievement, $user) {
            return $event->achievement_name === $commentAchievement->text
                && $event->user === $user;
        });

        Event::assertListening(
            AchievementUnlocked::class,
            AchievementUnlockedListener::class
        );
        $this->assertSame('Beginner', $user->badge->badge->name);
    }

    /** @test */
    public function it_replaces_existing_user_badge(): void
    {
        $beginnerBadge = Badge::create(['name' => 'Beginner', 'required_achievements' => 0]);
        $intermediateBadge = Badge::create(['name' => 'Intermediate', 'required_achievements' => 4]);

        // Create a user with an existing badge
        $user = User::factory()->create();
        $user->badge()->create(['badge_id' => $beginnerBadge->id]);

        // create Achievements
        $achievements = Achievement::factory()->count(4)->create();
        $user->userAchievements()->sync($achievements);

        // Mock the event to assert later
        Event::fake();

        $service = app(AchievementService::class); // Resolve through the container
        $service->unlockAchievement($user, AchievementTypeEnum::COMMENT);

        $listener = new AchievementUnlockedListener();
        // Unlock an achievement
        $listener->handle(new AchievementUnlocked($user->unlockedAchievements->first()->text, $user));

        Event::assertDispatched(BadgeUnlocked::class, function ($event) use ($user, $intermediateBadge) {
            $listener = new BadgeUnlockedListener();
            $listener->handle(new BadgeUnlocked($event->badge_name, $user));
            $user->refresh();

            return $event->badge_name === $intermediateBadge->name && $event->user === $user;
        });

        Event::assertListening(BadgeUnlocked::class, BadgeUnlockedListener::class);
        $this->assertCount(4, $user->unlockedAchievements);
        $this->assertSame(1, $user->badge()->count());
    }
}
