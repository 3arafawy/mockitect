<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Http\Request;

interface RequestMatcherInterface
{
    /**
     * Check if the request matches the given rule.
     *
     * @param Request $request The incoming HTTP request
     * @param array<string, mixed> $rule The rule configuration
     * @return bool True if the request matches
     */
    public function matches(Request $request, array $rule): bool;

    /**
     * Calculate specificity score for this rule.
     * Higher scores indicate more specific matches.
     *
     * @param array<string, mixed> $rule The rule configuration
     * @return int The specificity score
     */
    public function specificityScore(array $rule): int;

    /**
     * Get the matcher type identifier.
     *
     * @return string The type (e.g., 'path', 'method', 'header')
     */
    public function type(): string;
}
