<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Mock;
use App\Models\Scenario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mock>
 */
class MockFactory extends Factory
{
    protected $model = Mock::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'match_rules' => [
                ['type' => 'path', 'matcher' => 'exact', 'value' => '/api/' . $this->faker->word()],
                ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
            ],
            'response_config' => [
                'type' => 'static',
                'status' => 200,
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode(['data' => $this->faker->words(3)]),
            ],
            'priority' => $this->faker->numberBetween(0, 100),
            'is_active' => true,
            'scenario_id' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withScenario(): static
    {
        return $this->state(fn (array $attributes) => [
            'scenario_id' => Scenario::factory(),
        ]);
    }
}
