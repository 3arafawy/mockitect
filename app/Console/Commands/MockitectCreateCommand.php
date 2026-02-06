<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Mock;
use Illuminate\Console\Command;

class MockitectCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mockitect:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new mock interactively';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Create a new mock');
        $this->newLine();

        $name = $this->ask('Mock name');
        $description = $this->ask('Description (optional)');
        $priority = (int) $this->ask('Priority (0-100, default: 0)', '0');

        $this->info('Configure match rules:');
        $rules = $this->collectRules();

        $this->info('Configure response:');
        $responseConfig = $this->collectResponseConfig();

        $isActive = $this->confirm('Activate this mock?', true);

        $mock = Mock::create([
            'name' => $name,
            'description' => $description,
            'priority' => $priority,
            'match_rules' => $rules,
            'response_config' => $responseConfig,
            'is_active' => $isActive,
        ]);

        $this->newLine();
        $this->info("Mock created successfully! ID: {$mock->id}");

        return self::SUCCESS;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function collectRules(): array
    {
        $rules = [];

        // Path rule
        $pathMatcher = $this->choice(
            'Path matcher type',
            ['exact', 'prefix', 'regex', 'wildcard'],
            'exact'
        );
        $pathValue = $this->ask('Path value (e.g., api/users)');
        $rules[] = [
            'type' => 'path',
            'matcher' => $pathMatcher,
            'value' => $pathValue,
        ];

        // Method rule
        $method = $this->choice(
            'HTTP Method',
            ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'any'],
            'GET'
        );
        $rules[] = [
            'type' => 'method',
            'matcher' => $method === 'any' ? 'any' : 'exact',
            'value' => $method === 'any' ? '' : $method,
        ];

        // Optional header rule
        if ($this->confirm('Add header matching?', false)) {
            $headerName = $this->ask('Header name');
            $headerMatcher = $this->choice(
                'Header matcher type',
                ['exists', 'exact', 'contains', 'regex'],
                'exists'
            );
            $headerValue = $headerMatcher !== 'exists'
                ? $this->ask('Header value')
                : '';

            $rules[] = [
                'type' => 'header',
                'matcher' => $headerMatcher,
                'name' => $headerName,
                'value' => $headerValue,
            ];
        }

        return $rules;
    }

    /**
     * @return array<string, mixed>
     */
    private function collectResponseConfig(): array
    {
        $type = $this->choice(
            'Response type',
            ['static'],
            'static'
        );

        $status = (int) $this->ask('Status code', '200');
        $contentType = $this->choice(
            'Content-Type',
            ['application/json', 'text/plain', 'text/html'],
            'application/json'
        );

        $body = $this->ask('Response body');

        return [
            'type' => $type,
            'status' => $status,
            'headers' => ['Content-Type' => $contentType],
            'body' => $body,
        ];
    }
}
