<?php

// Admin routes loaded first via routes/mockitect.php
// Catch-all route for mock requests - MUST be last
Route::any('{path}', [App\Http\Controllers\MockRequestHandler::class, 'handle'])
    ->where('path', '.*')
    ->name('mockitect.catch-all');
