<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Mock;
use App\Models\RequestLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RequestLog>
 */
class RequestLogFactory extends Factory
{
    protected $model = RequestLog::class;

    public function definition(): array
    {
        return [
            'mock_id' => null,
            'method' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'path' => '/api/' . $this->faker->word(),
            'headers' => ['Content-Type' => 'application/json'],
            'query_params' => null,
            'body' => null,
            'response_status' => $this->faker->randomElement([200, 201, 404, 500]),
            'response_headers' => ['Content-Type' => 'application/json'],
            'response_body' => json_encode(['message' => $this->faker->sentence()]),
            'response_time_ms' => $this->faker->numberBetween(10, 500),
            'was_matched' => false,
        ];
    }

    public function matched(?Mock $mock = null): static
    {
        return $this->state(function (array $attributes) use ($mock) {
            return [
                'mock_id' => $mock?->id ?? Mock::factory(),
                'was_matched' => true,
            ];
        });
    }

    public function unmatched(): static
    {
        return $this->state(fn (array $attributes) => [
            'mock_id' => null,
            'was_matched' => false,
        ]);
    }
}
