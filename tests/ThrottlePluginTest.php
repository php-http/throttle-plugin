<?php

declare(strict_types=1);

namespace Tests\Http\Client\Common\Plugin;

use Http\Client\Common\Plugin\ThrottlePlugin;
use Http\Client\Common\PluginClient;
use Http\Mock\Client;
use Nyholm\Psr7\Factory\HttplugFactory;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\RateLimiter\RateLimit;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

/**
 * @group time-sensitive
 */
class ThrottlePluginTest extends TestCase
{
    private Client $mockClient;
    private PluginClient $client;

    protected function setUp(): void
    {
        ClockMock::register(RateLimit::class);
        $this->mockClient = new Client(new HttplugFactory());
        $this->client = new PluginClient($this->mockClient, [
            new ThrottlePlugin(
                (new RateLimiterFactory(
                    ['id' => 'foo', 'policy' => 'fixed_window', 'limit' => 2, 'interval' => '3 seconds'],
                    new InMemoryStorage(),
                ))->create(),
            ),
        ]);
    }

    public function testNoThrottle(): void
    {
        $time = time();
        $this->client->sendRequest(new Request('GET', ''));
        $this->client->sendRequest(new Request('GET', ''));
        $this->assertEqualsWithDelta($time, time(), 1);
    }

    public function testThrottle(): void
    {
        $time = time();
        $this->client->sendRequest(new Request('GET', ''));
        $this->client->sendRequest(new Request('GET', ''));
        $this->client->sendRequest(new Request('GET', ''));
        $this->assertEqualsWithDelta($time, ($timeAfterThrottle = time()) - 3, 1);

        $this->client->sendRequest(new Request('GET', ''));
        $this->assertEqualsWithDelta($timeAfterThrottle, time(), 1);
    }
}
