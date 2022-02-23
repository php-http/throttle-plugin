<?php

declare(strict_types=1);

namespace Http\Client\Common\Plugin;

use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\RateLimiter\LimiterInterface;

final class ThrottlePlugin implements Plugin
{
    private LimiterInterface $rateLimiter;

    public function __construct(LimiterInterface $rateLimiter)
    {
        $this->rateLimiter = $rateLimiter;
    }

    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $this->rateLimiter->reserve()->wait();

        return $next($request);
    }
}
