<?php

declare(strict_types=1);

use App\Services\Matchers\MethodMatcher;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->matcher = new MethodMatcher();
});

it('matches exact method', function () {
    $request = Request::create('/api/users', 'GET');
    $rule = ['matcher' => 'exact', 'value' => 'GET'];

    expect($this->matcher->matches($request, $rule))->toBeTrue();
});

it('matches exact method case insensitive', function () {
    $request = Request::create('/api/users', 'POST');
    $rule = ['matcher' => 'exact', 'value' => 'post'];

    expect($this->matcher->matches($request, $rule))->toBeTrue();
});

it('does not match different method', function () {
    $request = Request::create('/api/users', 'GET');
    $rule = ['matcher' => 'exact', 'value' => 'POST'];

    expect($this->matcher->matches($request, $rule))->toBeFalse();
});

it('matches any method', function () {
    $getRequest = Request::create('/api/users', 'GET');
    $postRequest = Request::create('/api/users', 'POST');
    $rule = ['matcher' => 'any'];

    expect($this->matcher->matches($getRequest, $rule))->toBeTrue()
        ->and($this->matcher->matches($postRequest, $rule))->toBeTrue();
});

it('returns correct specificity scores', function () {
    expect($this->matcher->specificityScore(['matcher' => 'exact']))->toBe(20)
        ->and($this->matcher->specificityScore(['matcher' => 'any']))->toBe(0);
});

it('returns method type', function () {
    expect($this->matcher->type())->toBe('method');
});
