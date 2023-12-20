<?php

namespace Joemires\Superban\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Joemires\Superban\Exceptions\InvalidIdentifierException;

class Superban
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $request_count = 10, int $request_duration = 1, int $banned_duration = 1): Response
    {
        throw_unless(in_array(config('superban.identifier'), ['ip', 'fingerprint']), InvalidIdentifierException::class, 'Invalid identifer, please check and try again');

        $id = $request->user()?->id ?: (config('superban.identifier') == 'ip' ? $request->ip() : $request->fingerprint());

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

        $response->headers->set('X-RateLimit-Limit', (string) $request_count);
        $response->headers->set('X-RateLimit-Remaining', (string) ($request_count - Cache::get($ttl_key)));

        return $response;
    }
}
