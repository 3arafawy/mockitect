<?php

declare(strict_types=1);

use App\Services\Matchers\HeaderMatcher;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->matcher = new HeaderMatcher();
});

it('matches exact header value', function () {
    $request = Request::create('/api/users', 'GET');
    $request->headers->set('X-Api-Key', 'secret123');
    $rule = ['matcher' => 'exact', 'name' => 'X-Api-Key', 'value' => 'secret123'];

    expect($this->matcher->matches($request, $rule))->toBeTrue();
});

it('does not match wrong header value', function () {
    $request = Request::create('/api/users', 'GET');
    $request->headers->set('X-Api-Key', 'wrong');
    $rule = ['matcher' => 'exact', 'name' => 'X-Api-Key', 'value' => 'secret123'];

    expect($this->matcher->matches($request, $rule))->toBeFalse();
});

it('does not match missing header', function () {
    $request = Request::create('/api/users', 'GET');
    $rule = ['matcher' => 'exact', 'name' => 'X-Api-Key', 'value' => 'secret123'];

    expect($this->matcher->matches($request, $rule))->toBeFalse();
});

it('matches header containing value', function () {
    $request = Request::create('/api/users', 'GET');
    $request->headers->set('Accept', 'application/json, text/html');
    $rule = ['matcher' => 'contains', 'name' => 'Accept', 'value' => 'json'];

    expect($this->matcher->matches($request, $rule))->toBeTrue();
});

it('matches header with regex', function () {
    $request = Request::create('/api/users', 'GET');
    $request->headers->set('User-Agent', 'Mozilla/5.0');
    $rule = ['matcher' => 'regex', 'name' => 'User-Agent', 'value' => 'Mozilla.*'];

    expect($this->matcher->matches($request, $rule))->toBeTrue();
});

it('matches header exists', function () {
    $request = Request::create('/api/users', 'GET');
    $request->headers->set('Authorization', 'Bearer token123');
    $rule = ['matcher' => 'exists', 'name' => 'Authorization'];

    expect($this->matcher->matches($request, $rule))->toBeTrue();
});

it('does not match non-existent header with exists matcher', function () {
    $request = Request::create('/api/users', 'GET');
    $rule = ['matcher' => 'exists', 'name' => 'Authorization'];

    expect($this->matcher->matches($request, $rule))->toBeFalse();
});

it('returns correct specificity scores', function () {
    expect($this->matcher->specificityScore(['matcher' => 'exact']))->toBe(10)
        ->and($this->matcher->specificityScore(['matcher' => 'contains']))->toBe(8)
        ->and($this->matcher->specificityScore(['matcher' => 'regex']))->toBe(7)
        ->and($this->matcher->specificityScore(['matcher' => 'exists']))->toBe(5);
});

it('returns header type', function () {
    expect($this->matcher->type())->toBe('header');
});
