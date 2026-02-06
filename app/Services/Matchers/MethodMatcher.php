<?php

declare(strict_types=1);

namespace App\Services\Matchers;

use App\Contracts\RequestMatcherInterface;
use Illuminate\Http\Request;

class MethodMatcher implements RequestMatcherInterface
{
    public function matches(Request $request, array $rule): bool
    {
        $matcher = $rule['matcher'] ?? 'exact';
        $value = $rule['value'] ?? '';
        $method = $request->getMethod();

        return match ($matcher) {
            'exact' => strtoupper($method) === strtoupper($value),
            'any' => true,
            default => false,
        };
    }

    public function specificityScore(array $rule): int
    {
        $matcher = $rule['matcher'] ?? 'exact';

        return match ($matcher) {
            'exact' => 20,
            'any' => 0,
            default => 0,
        };
    }

    public function type(): string
    {
        return 'method';
    }
}
