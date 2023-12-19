<?php

namespace Joemires\Superban\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Superban
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $request_count = 10, $ttl = 1, $banned_duration = 1): Response
    {
        return $next($request);
    }
}
