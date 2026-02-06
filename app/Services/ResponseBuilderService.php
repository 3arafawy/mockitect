<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Mock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ResponseBuilderService
{
    public function build(Mock $mock): Response|JsonResponse
    {
        $config = $mock->response_config;
        $type = $config['type'] ?? 'static';

        return match ($type) {
            'static' => $this->buildStaticResponse($config),
            default => $this->buildStaticResponse($config),
        };
    }

    /**
     * @param array<string, mixed> $config
     */
    private function buildStaticResponse(array $config): Response|JsonResponse
    {
        $status = $config['status'] ?? 200;
        $headers = $config['headers'] ?? [];
        $body = $config['body'] ?? '';

        // Determine if response should be JSON
        $contentType = $headers['Content-Type'] ?? '';
        $isJson = str_contains($contentType, 'json');

        if ($isJson && is_string($body)) {
            $decoded = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return response()->json($decoded, $status, $headers);
            }
        }

        return response($body, $status, $headers);
    }
}
