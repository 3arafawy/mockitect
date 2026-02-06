<?php

declare(strict_types=1);

namespace App\Services\Matchers;

use App\Contracts\RequestMatcherInterface;
use Illuminate\Http\Request;

class HeaderMatcher implements RequestMatcherInterface
{
    public function matches(Request $request, array $rule): bool
    {
        $matcher = $rule['matcher'] ?? 'exact';
        $headerName = $rule['name'] ?? '';
        $value = $rule['value'] ?? '';

        if (!$request->hasHeader($headerName)) {
            return false;
        }

        $headerValue = $request->header($headerName);

        return match ($matcher) {
            'exact' => $headerValue === $value,
            'contains' => str_contains($headerValue, $value),
            'regex' => $this->matchRegex($headerValue, $value),
            'exists' => true,
            default => false,
        };
    }

    public function specificityScore(array $rule): int
    {
        $matcher = $rule['matcher'] ?? 'exact';

        return match ($matcher) {
            'exact' => 10,
            'contains' => 8,
            'regex' => 7,
            'exists' => 5,
            default => 0,
        };
    }

    public function type(): string
    {
        return 'header';
    }

    private function matchRegex(string $value, string $pattern): bool
    {
        $result = preg_match('/' . $pattern . '/', $value);

        return $result === 1;
    }
}
