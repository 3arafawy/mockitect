<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Mock;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MockRequestMatched
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public Mock $mock,
        public Response|JsonResponse $response,
    ) {}
}
