<?php

declare(strict_types=1);

use App\Models\Scenario;
use App\Models\Mock;

it('can create a scenario', function () {
    $scenario = Scenario::factory()->create();

    expect($scenario)->toBeInstanceOf(Scenario::class)
        ->and($scenario->name)->not->toBeEmpty()
        ->and($scenario->is_active)->toBeTrue()
        ->and($scenario->state_machine)->toBeArray()
        ->and($scenario->current_state)->toBe('initial');
});

it('has casted attributes', function () {
    $scenario = Scenario::factory()->create([
        'state_machine' => [
            'states' => ['a', 'b'],
            'transitions' => [],
        ],
        'is_active' => true,
    ]);

    expect($scenario->state_machine)->toBe(['states' => ['a', 'b'], 'transitions' => []])
        ->and($scenario->is_active)->toBeTrue();
});

it('has many mocks', function () {
    $scenario = Scenario::factory()->create();
    Mock::factory()->count(3)->create(['scenario_id' => $scenario->id]);

    expect($scenario->mocks)->toHaveCount(3);
});

it('can scope active scenarios', function () {
    Scenario::factory()->count(3)->active()->create();
    Scenario::factory()->count(2)->inactive()->create();

    expect(Scenario::active()->count())->toBe(3);
});
