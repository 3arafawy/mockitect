<?php

declare(strict_types=1);

use App\Models\Mock;
use App\Models\Scenario;

it('can create a mock', function () {
    $mock = Mock::factory()->create();

    expect($mock)->toBeInstanceOf(Mock::class)
        ->and($mock->name)->not->toBeEmpty()
        ->and($mock->is_active)->toBeTrue()
        ->and($mock->match_rules)->toBeArray()
        ->and($mock->response_config)->toBeArray();
});

it('has casted attributes', function () {
    $mock = Mock::factory()->create([
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => '/api/users'],
        ],
        'response_config' => [
            'type' => 'static',
            'status' => 200,
        ],
        'is_active' => true,
        'priority' => 10,
    ]);

    expect($mock->match_rules)->toBe([['type' => 'path', 'matcher' => 'exact', 'value' => '/api/users']])
        ->and($mock->response_config)->toBe(['type' => 'static', 'status' => 200])
        ->and($mock->is_active)->toBeTrue()
        ->and($mock->priority)->toBe(10);
});

it('can belong to a scenario', function () {
    $scenario = Scenario::factory()->create();
    $mock = Mock::factory()->create(['scenario_id' => $scenario->id]);

    expect($mock->scenario)->toBeInstanceOf(Scenario::class)
        ->and($mock->scenario->id)->toBe($scenario->id);
});

it('has many request logs', function () {
    $mock = Mock::factory()->create();

    expect($mock->requestLogs)->toHaveCount(0);
});

it('can scope active mocks', function () {
    Mock::factory()->count(3)->active()->create();
    Mock::factory()->count(2)->inactive()->create();

    expect(Mock::active()->count())->toBe(3);
});

it('can order by priority', function () {
    $mock1 = Mock::factory()->create(['priority' => 5]);
    $mock2 = Mock::factory()->create(['priority' => 10]);
    $mock3 = Mock::factory()->create(['priority' => 1]);

    $ordered = Mock::orderedByPriority()->get();

    expect($ordered->first()->id)->toBe($mock2->id)
        ->and($ordered->last()->id)->toBe($mock3->id);
});
