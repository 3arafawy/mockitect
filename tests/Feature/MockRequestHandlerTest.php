<?php

declare(strict_types=1);

use App\Events\MockRequestMatched;
use App\Events\MockRequestNotMatched;
use App\Models\Mock;
use App\Models\RequestLog;
use Illuminate\Support\Facades\Event;

it('returns mocked response for matching request', function () {
    Event::fake([MockRequestMatched::class, MockRequestNotMatched::class]);

    Mock::factory()->create([
        'name' => 'Get Users',
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
        'response_config' => [
            'type' => 'static',
            'status' => 200,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode(['users' => []]),
        ],
    ]);

    $response = $this->get('/api/users');

    $response->assertStatus(200)
        ->assertJson(['users' => []]);

    Event::assertDispatched(MockRequestMatched::class);
});

it('returns 404 when no mock matches', function () {
    Event::fake([MockRequestMatched::class, MockRequestNotMatched::class]);

    $response = $this->get('/api/nonexistent');

    $response->assertStatus(404)
        ->assertJson(['error' => 'No matching mock found']);

    Event::assertDispatched(MockRequestNotMatched::class);
});

it('logs matched request', function () {
    $mock = Mock::factory()->create([
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
        'response_config' => [
            'type' => 'static',
            'status' => 200,
            'body' => '{}',
        ],
    ]);

    $this->get('/api/users');

    $log = RequestLog::first();

    expect($log)->not->toBeNull()
        ->and($log->mock_id)->toBe($mock->id)
        ->and($log->method)->toBe('GET')
        ->and($log->path)->toBe('api/users')
        ->and($log->was_matched)->toBeTrue()
        ->and($log->response_status)->toBe(200);
});

it('logs unmatched request', function () {
    $this->get('/api/nonexistent');

    $log = RequestLog::first();

    expect($log)->not->toBeNull()
        ->and($log->mock_id)->toBeNull()
        ->and($log->method)->toBe('GET')
        ->and($log->path)->toBe('api/nonexistent')
        ->and($log->was_matched)->toBeFalse()
        ->and($log->response_status)->toBe(404);
});

it('returns different status codes based on mock config', function () {
    Mock::factory()->create([
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/error'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
        'response_config' => [
            'type' => 'static',
            'status' => 500,
            'body' => json_encode(['error' => 'Server Error']),
        ],
    ]);

    $response = $this->get('/api/error');

    $response->assertStatus(500)
        ->assertJson(['error' => 'Server Error']);
});

it('handles POST requests', function () {
    Mock::factory()->create([
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'POST'],
        ],
        'response_config' => [
            'type' => 'static',
            'status' => 201,
            'body' => json_encode(['id' => 1]),
        ],
    ]);

    $response = $this->post('/api/users', ['name' => 'John']);

    $response->assertStatus(201)
        ->assertJson(['id' => 1]);
});

it('returns plain text response when content-type is not json', function () {
    Mock::factory()->create([
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/text'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
        'response_config' => [
            'type' => 'static',
            'status' => 200,
            'headers' => ['Content-Type' => 'text/plain'],
            'body' => 'Hello World',
        ],
    ]);

    $response = $this->get('/api/text');

    $response->assertStatus(200)
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('Hello World');
});
