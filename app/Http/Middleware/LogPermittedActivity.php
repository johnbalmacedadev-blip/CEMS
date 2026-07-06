<?php

namespace App\Http\Middleware;

use App\Support\ActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogPermittedActivity
{
    /**
     * Record permitted page activity after a successful response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check() && $this->isSuccessfulResponse($response)) {
            ActivityLogger::logPermittedRequest($request);
        }

        return $response;
    }

    protected function isSuccessfulResponse(Response $response): bool
    {
        $status = $response->getStatusCode();

        return $status >= 200 && $status < 400;
    }
}
