<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mockitect;

use App\Http\Controllers\Controller;
use App\Models\RequestLog;
use Inertia\Inertia;
use Inertia\Response;

class RequestLogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Mockitect/RequestLogs/Index', [
            'logs' => RequestLog::with('mock')
                ->orderByDesc('created_at')
                ->paginate(25),
        ]);
    }

    public function show(RequestLog $log): Response
    {
        return Inertia::render('Mockitect/RequestLogs/Show', [
            'log' => $log->load('mock'),
        ]);
    }
}
