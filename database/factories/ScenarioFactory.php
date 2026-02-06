<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Scenario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Scenario>
 */
class ScenarioFactory extends Factory
{
    protected $model = Scenario::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'current_state' => 'initial',
            'state_machine' => [
                'states' => ['initial', 'active', 'completed'],
                'transitions' => [
                    ['from' => 'initial', 'to' => 'active', 'on' => 'start'],
                    ['from' => 'active', 'to' => 'completed', 'on' => 'finish'],
                ],
            ],
            'is_active' => true,
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
}
