<?php

declare(strict_types=1);

namespace App\Services\Matchers;

use App\Contracts\RequestMatcherInterface;
use Illuminate\Http\Request;

class PathMatcher implements RequestMatcherInterface
{
    public function matches(Request $request, array $rule): bool
    {
        $matcher = $rule['matcher'] ?? 'exact';
        $value = $rule['value'] ?? '';
        $path = $request->path();

        return match ($matcher) {
            'exact' => $path === $value,
            'prefix' => str_starts_with($path, $value),
            'regex' => $this->matchRegex($path, $value),
            'wildcard' => $this->matchWildcard($path, $value),
            default => false,
        };
    }

    public function specificityScore(array $rule): int
    {
        $matcher = $rule['matcher'] ?? 'exact';

        return match ($matcher) {
            'exact' => 100,
            'regex' => 50,
            'wildcard' => 10,
            'prefix' => 20,
            default => 0,
        };
    }

    public function type(): string
    {
        return 'path';
    }

    private function matchRegex(string $path, string $pattern): bool
    {
        $result = preg_match('/^' . $pattern . '$/', $path);

        return $result === 1;
    }

    private function matchWildcard(string $path, string $pattern): bool
    {
        // Convert wildcard pattern to regex
        $regex = '/^' . str_replace(['*', '?'], ['.*', '.'], preg_quote($pattern, '/')) . '$/';
        $result = preg_match($regex, $path);

        return $result === 1;
    }
}
