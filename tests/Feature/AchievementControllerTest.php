<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_application_returns_a_successful_response(): void
    {
        $user = User::factory()->create();

        $response = $this->get("/users/{$user->id}/achievements");

        $response
            ->assertJsonCount(5)
            ->assertJsonStructure([
                'unlocked_achievements',
                'next_available_achievements',
                'current_badge',
                'next_badge',
                'remaing_to_unlock_next_badge',
            ])
            ->assertOk();
    }

    /** @test */
    public function the_application_returns_unlocked_achievements_as_array_of_strings()
    {
        $user = User::factory()->create();
        $achievements = Achievement::factory()->count(4)->create();
        $user->userAchievements()->sync($achievements);

        $response = $this->get("/users/{$user->id}/achievements");

        $response
            ->assertJsonCount(5)
            ->assertJsonStructure([
                'unlocked_achievements',
                'next_available_achievements',
                'current_badge',
                'next_badge',
                'remaing_to_unlock_next_badge',
            ])
            ->assertJsonFragment([
                'unlocked_achievements' => $user->unlockedAchievements
                    ->pluck('text')
                    ->toArray()
            ])
            ->assertOk();
    }
}
