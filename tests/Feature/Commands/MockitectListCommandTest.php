<?php

declare(strict_types=1);

use App\Models\Mock;
use Illuminate\Support\Facades\Artisan;

it('lists all mocks', function () {
    Mock::factory()->count(3)->create();

    Artisan::call('mockitect:list');
    $output = Artisan::output();

    expect($output)->toContain('Total: 3 mocks');
});

it('lists only active mocks with --active flag', function () {
    Mock::factory()->count(2)->active()->create();
    Mock::factory()->count(3)->inactive()->create();

    Artisan::call('mockitect:list', ['--active' => true]);
    $output = Artisan::output();

    expect($output)->toContain('Total: 2 mocks');
});

it('lists only inactive mocks with --inactive flag', function () {
    Mock::factory()->count(2)->active()->create();
    Mock::factory()->count(1)->inactive()->create();

    Artisan::call('mockitect:list', ['--inactive' => true]);
    $output = Artisan::output();

    expect($output)->toContain('Total: 1 mocks');
});

it('shows message when no mocks exist', function () {
    Artisan::call('mockitect:list');
    $output = Artisan::output();

    expect($output)->toContain('No mocks found');
});

it('displays mock details in table', function () {
    Mock::factory()->create([
        'name' => 'Test Mock',
        'priority' => 10,
        'is_active' => true,
    ]);

    Artisan::call('mockitect:list');
    $output = Artisan::output();

    expect($output)
        ->toContain('Test Mock')
        ->toContain('10')
        ->toContain('Active');
});
