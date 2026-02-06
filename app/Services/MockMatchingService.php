<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RequestMatcherInterface;
use App\Models\Mock;
use App\Services\Matchers\HeaderMatcher;
use App\Services\Matchers\MethodMatcher;
use App\Services\Matchers\PathMatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MockMatchingService
{
    /**
     * @var array<string, RequestMatcherInterface>
     */
    protected array $matchers = [];

    public function __construct()
    {
        $this->registerDefaultMatchers();
    }

    public function registerMatcher(RequestMatcherInterface $matcher): void
    {
        $this->matchers[$matcher->type()] = $matcher;
    }

    public function findMatch(Request $request): ?Mock
    {
        $mocks = Mock::active()
            ->orderedByPriority()
            ->get();

        if ($mocks->isEmpty()) {
            return null;
        }

        // Group mocks by explicit priority
        /** @var Collection<int, Mock> $mocks */
        $groupedByPriority = $mocks->groupBy('priority');

        // Process each priority group from highest to lowest
        foreach ($groupedByPriority->sortKeysDesc() as $priority => $priorityMocks) {
            /** @var Collection<int, Mock> $priorityMocks */
            $matches = $this->findMatchesInGroup($request, $priorityMocks);

            if ($matches->isNotEmpty()) {
                // Return the match with highest specificity score
                return $matches->first();
            }
        }

        return null;
    }

    /**
     * @param Collection<int, Mock> $mocks
     * @return Collection<int, Mock>
     */
    protected function findMatchesInGroup(Request $request, Collection $mocks): Collection
    {
        return $mocks
            ->filter(fn (Mock $mock) => $this->matchesAllRules($request, $mock->match_rules))
            ->sortByDesc(fn (Mock $mock) => $this->calculateSpecificityScore($mock->match_rules));
    }

    /**
     * @param array<int, array<string, mixed>> $rules
     */
    protected function matchesAllRules(Request $request, array $rules): bool
    {
        foreach ($rules as $rule) {
            $type = $rule['type'] ?? '';

            if (!isset($this->matchers[$type])) {
                return false;
            }

            if (!$this->matchers[$type]->matches($request, $rule)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate total specificity score for all match rules.
     *
     * @param array<int, array<string, mixed>> $rules
     */
    public function calculateSpecificityScore(array $rules): int
    {
        $score = 0;

        foreach ($rules as $rule) {
            $type = $rule['type'] ?? '';

            if (isset($this->matchers[$type])) {
                $score += $this->matchers[$type]->specificityScore($rule);
            }
        }

        return $score;
    }

    protected function registerDefaultMatchers(): void
    {
        $this->registerMatcher(new PathMatcher());
        $this->registerMatcher(new MethodMatcher());
        $this->registerMatcher(new HeaderMatcher());
    }
}
