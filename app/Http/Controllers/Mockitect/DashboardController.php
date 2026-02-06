<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mockitect;

use App\Http\Controllers\Controller;
use App\Models\Mock;
use App\Models\RequestLog;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Mockitect/Dashboard', [
            'stats' => [
                'totalMocks' => Mock::count(),
                'activeMocks' => Mock::active()->count(),
                'totalRequests' => RequestLog::count(),
                'matchedRequests' => RequestLog::matched()->count(),
            ],
            'recentMocks' => Mock::withCount('requestLogs')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
            'recentLogs' => RequestLog::with('mock')
                ->recent(10)
                ->get(),
        ]);
    }
}
