<?php

declare(strict_types=1);

use App\Models\Mock;
use Illuminate\Support\Facades\Artisan;

it('imports mock from json file', function () {
    $json = json_encode([
        'name' => 'Imported Mock',
        'match_rules' => [
            ['type' => 'path', 'matcher' => 'exact', 'value' => 'api/users'],
            ['type' => 'method', 'matcher' => 'exact', 'value' => 'GET'],
        ],
        'response_config' => [
            'type' => 'static',
            'status' => 200,
            'body' => '{"users": []}',
        ],
    ]);

    $path = tempnam(sys_get_temp_dir(), 'mock');
    file_put_contents($path, $json);

    Artisan::call('mockitect:import', ['file' => $path]);
    $output = Artisan::output();

    expect($output)
        ->toContain('Imported: Imported Mock')
        ->toContain('Import complete: 1 imported, 0 failed');

    expect(Mock::where('name', 'Imported Mock')->exists())->toBeTrue();

    unlink($path);
});

it('imports multiple mocks from json array', function () {
    $json = json_encode([
        [
            'name' => 'Mock 1',
            'match_rules' => [['type' => 'path', 'matcher' => 'exact', 'value' => 'api/1']],
            'response_config' => ['type' => 'static', 'status' => 200, 'body' => '{}'],
        ],
        [
            'name' => 'Mock 2',
            'match_rules' => [['type' => 'path', 'matcher' => 'exact', 'value' => 'api/2']],
            'response_config' => ['type' => 'static', 'status' => 200, 'body' => '{}'],
        ],
    ]);

    $path = tempnam(sys_get_temp_dir(), 'mock');
    file_put_contents($path, $json);

    Artisan::call('mockitect:import', ['file' => $path]);
    $output = Artisan::output();

    expect($output)->toContain('Import complete: 2 imported, 0 failed');
    expect(Mock::count())->toBe(2);

    unlink($path);
});

it('fails when file does not exist', function () {
    Artisan::call('mockitect:import', ['file' => '/nonexistent/file.json']);
    $output = Artisan::output();

    expect($output)->toContain('File not found');
});

it('fails with invalid json', function () {
    $path = tempnam(sys_get_temp_dir(), 'mock');
    file_put_contents($path, 'invalid json');

    Artisan::call('mockitect:import', ['file' => $path]);
    $output = Artisan::output();

    expect($output)->toContain('Invalid JSON');

    unlink($path);
});

it('validates required fields', function () {
    $json = json_encode([
        'name' => 'Incomplete Mock',
        // missing match_rules and response_config
    ]);

    $path = tempnam(sys_get_temp_dir(), 'mock');
    file_put_contents($path, $json);

    Artisan::call('mockitect:import', ['file' => $path]);
    $output = Artisan::output();

    expect($output)->toContain('Validation failed');

    unlink($path);
});
