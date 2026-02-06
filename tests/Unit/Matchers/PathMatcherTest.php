<?php

declare(strict_types=1);

use App\Services\Matchers\PathMatcher;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->matcher = new PathMatcher();
});

it('matches exact path', function () {
    $request = Request::create('/api/users', 'GET');
    $rule = ['matcher' => 'exact', 'value' => '/api/users'];

    expect($this->matcher->matches($request, $rule))->toBeTrue();
});

it('does not match different exact path', function () {
    $request = Request::create('/api/users', 'GET');
    $rule = ['matcher' => 'exact', 'value' => '/api/posts'];

    expect($this->matcher->matches($request, $rule))->toBeFalse();
});

it('matches prefix path', function () {
    $request = Request::create('/api/users/123', 'GET');
    $rule = ['matcher' => 'prefix', 'value' => '/api/users'];

    expect($this->matcher->matches($request, $rule))->toBeTrue();
});

it('does not match wrong prefix', function () {
    $request = Request::create('/api/posts', 'GET');
    $rule = ['matcher' => 'prefix', 'value' => '/api/users'];

    expect($this->matcher->matches($request, $rule))->toBeFalse();
});

it('matches regex pattern', function () {
    $request = Request::create('/api/users/123', 'GET');
    $rule = ['matcher' => 'regex', 'value' => 'api/users/\d+'];

    expect($this->matcher->matches($request, $rule))->toBeTrue();
});

it('does not match wrong regex', function () {
    $request = Request::create('/api/users/abc', 'GET');
    $rule = ['matcher' => 'regex', 'value' => 'api/users/\d+'];

    expect($this->matcher->matches($request, $rule))->toBeFalse();
});

it('matches wildcard pattern', function () {
    $request = Request::create('/api/users/123', 'GET');
    $rule = ['matcher' => 'wildcard', 'value' => '/api/users/*'];

    expect($this->matcher->matches($request, $rule))->toBeTrue();
});

it('matches wildcard with single char', function () {
    $request = Request::create('/api/users/1', 'GET');
    $rule = ['matcher' => 'wildcard', 'value' => '/api/users/?'];

    expect($this->matcher->matches($request, $rule))->toBeTrue();
});

it('returns correct specificity scores', function () {
    expect($this->matcher->specificityScore(['matcher' => 'exact']))->toBe(100)
        ->and($this->matcher->specificityScore(['matcher' => 'regex']))->toBe(50)
        ->and($this->matcher->specificityScore(['matcher' => 'wildcard']))->toBe(10)
        ->and($this->matcher->specificityScore(['matcher' => 'prefix']))->toBe(20);
});

it('returns path type', function () {
    expect($this->matcher->type())->toBe('path');
});
