<?php

declare(strict_types=1);

use App\Models\RequestLog;
use App\Models\Mock;

it('can create a request log', function () {
    $log = RequestLog::factory()->create();

    expect($log)->toBeInstanceOf(RequestLog::class)
        ->and($log->method)->not->toBeEmpty()
        ->and($log->path)->not->toBeEmpty()
        ->and($log->was_matched)->toBeFalse();
});

it('has casted attributes', function () {
    $log = RequestLog::factory()->create([
        'headers' => ['X-Custom' => 'value'],
        'query_params' => ['page' => 1],
        'response_headers' => ['Content-Type' => 'json'],
        'was_matched' => true,
        'response_time_ms' => 150,
    ]);

    expect($log->headers)->toBe(['X-Custom' => 'value'])
        ->and($log->query_params)->toBe(['page' => 1])
        ->and($log->response_headers)->toBe(['Content-Type' => 'json'])
        ->and($log->was_matched)->toBeTrue()
        ->and($log->response_time_ms)->toBe(150);
});

it('can belong to a mock', function () {
    $mock = Mock::factory()->create();
    $log = RequestLog::factory()->matched($mock)->create();

    expect($log->mock)->toBeInstanceOf(Mock::class)
        ->and($log->mock->id)->toBe($mock->id);
});

it('can be matched', function () {
    $mock = Mock::factory()->create();
    $log = RequestLog::factory()->matched($mock)->create();

    expect($log->was_matched)->toBeTrue()
        ->and($log->mock_id)->toBe($mock->id);
});

it('can be unmatched', function () {
    $log = RequestLog::factory()->unmatched()->create();

    expect($log->was_matched)->toBeFalse()
        ->and($log->mock_id)->toBeNull();
});

it('can scope recent logs', function () {
    RequestLog::factory()->count(5)->create();

    expect(RequestLog::recent(3)->count())->toBe(3);
});

it('can scope matched logs', function () {
    RequestLog::factory()->count(3)->matched()->create();
    RequestLog::factory()->count(2)->unmatched()->create();

    expect(RequestLog::matched()->count())->toBe(3);
});

it('can scope unmatched logs', function () {
    RequestLog::factory()->count(2)->matched()->create();
    RequestLog::factory()->count(4)->unmatched()->create();

    expect(RequestLog::unmatched()->count())->toBe(4);
});
