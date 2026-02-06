<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class MockRequestNotMatched
{
    use Dispatchable;

    public function __construct(
        public Request $request,
    ) {}
}
