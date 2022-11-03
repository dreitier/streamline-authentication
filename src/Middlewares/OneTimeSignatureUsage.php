<?php

namespace Dreitier\Streamline\Authentication\Middlewares;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\Cache;

/**
 * With this middleware we can create signatures with a one-time usage only.
 * This is required to prevent replay attacks during auto-login. Using SESSION_DOMAIN like ".domain.tld" would require additional effort to secure cross-subdomain/tenant access.
 *
 * @see https://darkghosthunter.medium.com/laravel-combining-signed-routes-with-temporal-tokens-c5177769b432
 */
class OneTimeSignatureUsage
{
    /**
     * Cache manager
     *
     * @var \Illuminate\Cache\CacheManager
     */
    protected $cache;

    /**
     * Create a new ValidateSignature instance.
     *
     * @param  \Illuminate\Cache\CacheManager  $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Check if the signature has been already consumed.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return \Illuminate\Contracts\Routing\ResponseFactory|mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $isValid = $request->hasValidSignature($absolute = true);

        if ($this->signatureAlreadyConsumed($request) || ! $isValid) {
            throw new InvalidSignatureException;
        }

        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        if ($response->isSuccessful()) {
            $this->consumeSignature($request);
        }

        return $response;
    }

    protected function signatureAlreadyConsumed(Request $request)
    {
        $r = Cache::has($this->cacheKey($request));

        return $r;
    }

    /**
     * Consumes the signature, marking it as unavailable
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function consumeSignature(Request $request)
    {
        $ttl = Carbon::createFromTimestamp($request->query('expires'));

        Cache::put($this->cacheKey($request), null, $ttl);
    }

    /**
     * Return the cache Key to check
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function cacheKey(Request $request)
    {
        return 'consumable|'.$request->query('signature');
    }
}
