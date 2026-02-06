<?php

declare(strict_types=1);

use App\Models\Mock;
use App\Models\RequestLog;

it('handles complete mock flow', function () {
    // 1. Create mock
    $mock = Mock::factory()->create([
        'name' => 'Get Users',
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
        'response_config' => [
            'type' => 'static',
            'status' => 200,
            'body' => '{"users": []}',
        ],
        'is_active' => true,
    ]);

    // 2. Make request
    $response = $this->get('/api/users');

    // 3. Verify response
    expect($response->status())->toBe(200)
        ->and($response->json())->toBe(['users' => []]);

    // 4. Verify logging
    expect(RequestLog::count())->toBe(1)
        ->and(RequestLog::first()->mock_id)->toBe($mock->id);
});

it('can create mock via UI endpoint', function () {
    $mockData = [
        'name' => 'Test Mock UI',
        'description' => 'Created via UI',
        'priority' => 10,
        'is_active' => true,
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/test'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'POST'],
        ],
        'response_config' => [
            'type' => 'static',
            'status' => 201,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => '{"created": true}',
        ],
    ];

    $response = $this->post('/__mockitect/mocks', $mockData);

    $response->assertRedirect();
    
    expect(Mock::where('name', 'Test Mock UI')->exists())->toBeTrue();
});

it('can access admin dashboard', function () {
    $response = $this->get('/__mockitect');

    $response->assertOk();
});

it('can list mocks via CLI command', function () {
    Mock::factory()->count(3)->create();

    \Illuminate\Support\Facades\Artisan::call('mockitect:list');
    $output = \Illuminate\Support\Facades\Artisan::output();

    expect($output)->toContain('Total: 3 mocks');
});

it('matches requests with priority resolution', function () {
    // Low priority mock
    Mock::factory()->create([
        'name' => 'Low Priority',
        'priority' => 5,
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'prefix', 'value' => 'api'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
        'response_config' => [
            'type' => 'static',
            'status' => 200,
            'body' => '{"priority": "low"}',
        ],
    ]);

    // High priority mock
    Mock::factory()->create([
        'name' => 'High Priority',
        'priority' => 100,
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
        'response_config' => [
            'type' => 'static',
            'status' => 200,
            'body' => '{"priority": "high"}',
        ],
    ]);

    $response = $this->get('/api/users');

    expect($response->status())->toBe(200)
        ->and($response->json())->toBe(['priority' => 'high']);
});

it('logs unmatched requests', function () {
    $response = $this->get('/api/nonexistent');

    $response->assertStatus(404);

    $log = RequestLog::first();
    
    expect($log)->not->toBeNull()
        ->and($log->was_matched)->toBeFalse()
        ->and($log->mock_id)->toBeNull();
});
