<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mockitect;

use App\Http\Controllers\Controller;
use App\Models\Mock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MockController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Mockitect/Mocks/Index', [
            'mocks' => Mock::withCount('requestLogs')
                ->orderByDesc('priority')
                ->paginate(10),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Mockitect/Mocks/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'integer|min:0',
            'is_active' => 'boolean',
            'match_rules' => 'required|array',
            'response_config' => 'required|array',
        ]);

        Mock::create($validated);

        return redirect()->route('mockitect.mocks.index')
            ->with('success', 'Mock created successfully');
    }

    public function show(Mock $mock): Response
    {
        return Inertia::render('Mockitect/Mocks/Show', [
            'mock' => $mock->load(['requestLogs' => function ($query) {
                $query->recent(50);
            }]),
        ]);
    }

    public function edit(Mock $mock): Response
    {
        return Inertia::render('Mockitect/Mocks/Edit', [
            'mock' => $mock,
        ]);
    }

    public function update(Request $request, Mock $mock): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'integer|min:0',
            'is_active' => 'boolean',
            'match_rules' => 'required|array',
            'response_config' => 'required|array',
        ]);

        $mock->update($validated);

        return redirect()->route('mockitect.mocks.index')
            ->with('success', 'Mock updated successfully');
    }

    public function destroy(Mock $mock): RedirectResponse
    {
        $mock->delete();

        return redirect()->route('mockitect.mocks.index')
            ->with('success', 'Mock deleted successfully');
    }
}
