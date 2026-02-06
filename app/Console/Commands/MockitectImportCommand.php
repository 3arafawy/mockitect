<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Mock;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Validator;

class MockitectImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mockitect:import
                            {file : Path to JSON file}
                            {--force : Overwrite existing mocks with same name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import mocks from JSON file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return self::FAILURE;
        }

        $content = file_get_contents($filePath);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON: ' . json_last_error_msg());
            return self::FAILURE;
        }

        // Support both single mock and array of mocks
        $mocks = isset($data[0]) ? $data : [$data];
        $imported = 0;
        $failed = 0;

        foreach ($mocks as $mockData) {
            if (!$this->validateMockData($mockData)) {
                $failed++;
                continue;
            }

            try {
                $mock = Mock::create([
                    'name' => $mockData['name'],
                    'description' => $mockData['description'] ?? null,
                    'priority' => $mockData['priority'] ?? 0,
                    'match_rules' => $mockData['match_rules'],
                    'response_config' => $mockData['response_config'],
                    'is_active' => $mockData['is_active'] ?? true,
                ]);

                $this->info("Imported: {$mock->name} (ID: {$mock->id})");
                $imported++;
            } catch (\Exception $e) {
                $this->error("Failed to import '{$mockData['name']}': {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Import complete: {$imported} imported, {$failed} failed");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function validateMockData(array $data): bool
    {
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'match_rules' => 'required|array',
            'response_config' => 'required|array',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed: ' . $validator->errors()->first());
            return false;
        }

        return true;
    }
}
