<?php

namespace Joemires\Superban\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class Superban
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $request_count = 10, $request_duration = 1, $banned_duration = 1): Response
    {
        $id = $request->user()?->id ?: $request->ip();

        $ttl_key = $request->path() . ':' . $id;

        $driver = config('superban.driver', config('cache.default'));

        $cache = Cache::store($driver);

        $banned_ttl = $cache->get($ttl_key . ':banned');

        if($banned_ttl) {
            abort(429, 'Too Many Request...', [
                'X-RateLimit-Limit' => $request_count,
                'X-RateLimit-Remaining' => 0,
                'X-RateLimit-Reset' => $banned_ttl->timestamp,
                'Retry-After' => $banned_ttl->diffInSeconds(now()) . ' Seconds'
            ]);
        }

        $limiter = app(RateLimiter::class, ['cache' => $cache]);

        $executed = $limiter->attempt(key: $ttl_key, maxAttempts: $request_count, decaySeconds: $request_duration * 60, callback: fn () => null);

        if ($limiter->tooManyAttempts(key: $ttl_key, maxAttempts: $request_count)) {
            $banned_ttl = now()->addMinutes($banned_duration);

            $cache->add($ttl_key . ':banned', $banned_ttl, $banned_ttl);

            abort(429, 'Too Many Request...', [
                'X-RateLimit-Limit' => $request_count,
                'X-RateLimit-Remaining' => 0,
                'X-RateLimit-Reset' => $banned_ttl->timestamp,
                'Retry-After' => $banned_ttl->diffInSeconds(now()) . ' Seconds'
            ]);
        }

        $response = $next($request);

        $response->headers->set('X-RateLimit-Limit', $request_count);
        $response->headers->set('X-RateLimit-Remaining', $request_count - Cache::get($ttl_key));

        return $response;
    }
}
