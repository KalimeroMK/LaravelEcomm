<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for the application.
     *
     * When behind Cloudflare (or similar), use '*' so X-Forwarded-Host, -Proto, etc.
     * are used and request()->getHost() matches the original domain (e.g. e-comm.mk).
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO;

    public function __construct()
    {
        $trusted = config('app.trusted_proxies');
        $this->proxies = $trusted !== null && $trusted !== '' ? $trusted : null;
    }
}
