<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Mock;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class MockitectListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mockitect:list
                            {--active : Show only active mocks}
                            {--inactive : Show only inactive mocks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all mocks';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        /** @var Collection<int, Mock> $mocks */
        $mocks = $this->getMocks();

        if ($mocks->isEmpty()) {
            $this->warn('No mocks found.');
            return self::SUCCESS;
        }

        $headers = ['ID', 'Name', 'Priority', 'Status', 'Rules', 'Created'];
        $rows = $mocks->map(fn (Mock $mock) => [
            $mock->id,
            $mock->name,
            $mock->priority,
            $mock->is_active ? '<fg=green>Active</>' : '<fg=red>Inactive</>',
            count($mock->match_rules) . ' rules',
            $mock->created_at?->diffForHumans() ?? 'N/A',
        ])->toArray();

        $this->table($headers, $rows);
        $this->info("Total: {$mocks->count()} mocks");

        return self::SUCCESS;
    }

    /**
     * @return Collection<int, Mock>
     */
    private function getMocks(): Collection
    {
        $query = Mock::query();

        if ($this->option('active')) {
            $query->active();
        } elseif ($this->option('inactive')) {
            $query->where('is_active', false);
        }

        return $query->orderByDesc('priority')->get();
    }
}
