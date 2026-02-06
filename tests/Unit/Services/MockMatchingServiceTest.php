<?php

declare(strict_types=1);

use App\Models\Mock;
use App\Services\MockMatchingService;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->service = new MockMatchingService();
});

it('finds no match when no mocks exist', function () {
    $request = Request::create('/api/users', 'GET');

    $result = $this->service->findMatch($request);

    expect($result)->toBeNull();
});

it('finds exact match', function () {
    $mock = Mock::factory()->create([
        'name' => 'Get Users',
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
        'is_active' => true,
    ]);

    $request = Request::create('/api/users', 'GET');
    $result = $this->service->findMatch($request);

    expect($result)->not->toBeNull()
        ->and($result->id)->toBe($mock->id);
});

it('does not match inactive mock', function () {
    Mock::factory()->create([
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
        'is_active' => false,
    ]);

    $request = Request::create('/api/users', 'GET');
    $result = $this->service->findMatch($request);

    expect($result)->toBeNull();
});

it('does not match when method differs', function () {
    Mock::factory()->create([
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'POST'],
        ],
    ]);

    $request = Request::create('/api/users', 'GET');
    $result = $this->service->findMatch($request);

    expect($result)->toBeNull();
});

it('does not match when path differs', function () {
    Mock::factory()->create([
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/posts'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
    ]);

    $request = Request::create('/api/users', 'GET');
    $result = $this->service->findMatch($request);

    expect($result)->toBeNull();
});

it('matches with wildcard path', function () {
    $mock = Mock::factory()->create([
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'wildcard', 'value' => 'api/users/*'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
    ]);

    $request = Request::create('/api/users/123', 'GET');
    $result = $this->service->findMatch($request);

    expect($result)->not->toBeNull()
        ->and($result->id)->toBe($mock->id);
});

it('matches with prefix path', function () {
    $mock = Mock::factory()->create([
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'prefix', 'value' => 'api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
    ]);

    $request = Request::create('/api/users/123/profile', 'GET');
    $result = $this->service->findMatch($request);

    expect($result)->not->toBeNull()
        ->and($result->id)->toBe($mock->id);
});

it('respects priority order', function () {
    $lowPriorityMock = Mock::factory()->create([
        'name' => 'Low Priority',
        'priority' => 5,
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
    ]);

    $highPriorityMock = Mock::factory()->create([
        'name' => 'High Priority',
        'priority' => 10,
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
    ]);

    $request = Request::create('/api/users', 'GET');
    $result = $this->service->findMatch($request);

    expect($result)->not->toBeNull()
        ->and($result->id)->toBe($highPriorityMock->id);
});

it('uses specificity when priority is equal', function () {
    // Both have priority 5, but the second has more specific match (exact path)
    Mock::factory()->create([
        'name' => 'Wildcard Match',
        'priority' => 5,
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'wildcard', 'value' => 'api/*'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
    ]);

    $exactMock = Mock::factory()->create([
        'name' => 'Exact Match',
        'priority' => 5,
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
    ]);

    $request = Request::create('/api/users', 'GET');
    $result = $this->service->findMatch($request);

    expect($result)->not->toBeNull()
        ->and($result->id)->toBe($exactMock->id);
});

it('matches with header', function () {
    $mock = Mock::factory()->create([
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
            ['type' => 'header', 'matcher' => 'exists', 'name' => 'Authorization'],
        ],
    ]);

    $request = Request::create('/api/users', 'GET');
    $request->headers->set('Authorization', 'Bearer token');
    $result = $this->service->findMatch($request);

    expect($result)->not->toBeNull()
        ->and($result->id)->toBe($mock->id);
});

it('does not match when required header is missing', function () {
    Mock::factory()->create([
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
            ['type' => 'header', 'matcher' => 'exists', 'name' => 'Authorization'],
        ],
    ]);

    $request = Request::create('/api/users', 'GET');
    $result = $this->service->findMatch($request);

    expect($result)->toBeNull();
});

it('calculates specificity score correctly', function () {
    $rules = [
        ['type' => 'path', 'matcher' => 'exact'],
        ['type' => 'method', 'matcher' => 'exact'],
        ['type' => 'header', 'matcher' => 'exact'],
    ];

    $score = $this->service->calculateSpecificityScore($rules);

    expect($score)->toBe(130); // 100 + 20 + 10
});
