<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\MockRequestMatched;
use App\Events\MockRequestNotMatched;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogMockRequestListener
{
    /**
     * Handle the event.
     */
    public function handle(MockRequestMatched|MockRequestNotMatched $event): void
    {
        if ($event instanceof MockRequestMatched) {
            Log::info('Mock request matched', [
                'mock_id' => $event->mock->id,
                'mock_name' => $event->mock->name,
                'path' => $event->request->path(),
                'method' => $event->request->getMethod(),
                'response_status' => $event->response->getStatusCode(),
            ]);
        } else {
            Log::info('Mock request not matched', [
                'path' => $event->request->path(),
                'method' => $event->request->getMethod(),
            ]);
        }
    }
}
