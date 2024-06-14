<?php

declare(strict_types=1);

namespace Http\Client\Common\Plugin;

use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\RateLimiter\Exception\MaxWaitDurationExceededException;
use Symfony\Component\RateLimiter\Exception\ReserveNotSupportedException;
use Symfony\Component\RateLimiter\LimiterInterface;

final class ThrottlePlugin implements Plugin
{
    private LimiterInterface $rateLimiter;

    private int $tokens;

    private ?float $maxTime;

    /**
     * @param int $tokens the number of tokens required
     * @param float|null $maxTime maximum accepted waiting time in seconds
     */
    public function __construct(LimiterInterface $rateLimiter, int $tokens = 1, ?float $maxTime = null)
    {
        $this->rateLimiter = $rateLimiter;
        $this->tokens = $tokens;
        $this->maxTime = $maxTime;
    }

    /**
     * @throws MaxWaitDurationExceededException if $maxTime is set and the process needs to wait longer than its value
     * @throws ReserveNotSupportedException if this limiter implementation doesn't support reserving tokens
     * @throws InvalidArgumentException if $tokens is larger than the maximum burst size
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $this->rateLimiter->reserve($this->tokens, $this->maxTime)->wait();

        return $next($request);
    }
}
