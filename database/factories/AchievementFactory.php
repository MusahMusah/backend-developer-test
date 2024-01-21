<?php

namespace Database\Factories;

use App\Concerns\Enums\AchievementTypeEnum;
use App\Models\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;

class AchievementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Achievement::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'text' => $this->faker->words(2, true),
            'count' => $this->faker->randomNumber(),
            'type' => collect([AchievementTypeEnum::COMMENT, AchievementTypeEnum::LESSON])->random()
        ];
    }
}
