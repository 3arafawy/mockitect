<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\MockRequestMatched;
use App\Events\MockRequestNotMatched;
use App\Models\RequestLog;
use App\Services\MockMatchingService;
use App\Services\ResponseBuilderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;

class MockRequestHandler extends Controller
{
    public function __construct(
        private MockMatchingService $matchingService,
        private ResponseBuilderService $responseBuilder,
    ) {}

    public function handle(Request $request): Response|JsonResponse
    {
        $startTime = microtime(true);

        $mock = $this->matchingService->findMatch($request);

        if ($mock === null) {
            $response = response()->json(['error' => 'No matching mock found'], 404);
            $this->logRequest($request, $response, $startTime, null, false);
            Event::dispatch(new MockRequestNotMatched($request));

            return $response;
        }

        $response = $this->responseBuilder->build($mock);
        $this->logRequest($request, $response, $startTime, $mock->id, true);
        Event::dispatch(new MockRequestMatched($request, $mock, $response));

        return $response;
    }

    private function logRequest(
        Request $request,
        Response|JsonResponse $response,
        float $startTime,
        ?int $mockId,
        bool $wasMatched,
    ): void {
        $endTime = microtime(true);
        $responseTimeMs = (int) (($endTime - $startTime) * 1000);

        RequestLog::create([
            'mock_id' => $mockId,
            'method' => $request->getMethod(),
            'path' => $request->path(),
            'headers' => $request->headers->all(),
            'query_params' => $request->query->all(),
            'body' => $request->getContent() ?: null,
            'response_status' => $response->getStatusCode(),
            'response_headers' => $response->headers->all(),
            'response_body' => $response->getContent() ?: null,
            'response_time_ms' => $responseTimeMs,
            'was_matched' => $wasMatched,
        ]);
    }
}
