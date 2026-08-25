<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Services\VisitorTrackerService;

class TrackVisitorMiddleware {
    public function handle(Request $request): ?Response {
        // Track live visitors without logging sensitive data
        VisitorTrackerService::recordVisit($request);
        return null;
    }
}
