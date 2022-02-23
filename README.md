# Throttle Plugin

PHP-HTTP plugin for throttling/rate limiting with the [symfony/rate-limiter](https://symfony.com/doc/current/rate_limiter.html)

> Warning: Plugin currently utilizes usleep() and hence is blocking whole process while waiting

## Install

Via [Composer](https://getcomposer.org/doc/00-intro.md)

```bash
composer require php-http/throttle-plugin
```

## Usage

```php
new \Http\Client\Common\Plugin\ThrottlePluginn(
    (new \Symfony\Component\RateLimiter\RateLimiterFactory(
        ['id' => 'foo', 'policy' => 'fixed_window', 'limit' => 2, 'interval' => '3 seconds'],
        new \Symfony\Component\RateLimiter\Storage\InMemoryStorage(),
    ))->create(),
);
```

## Licensing

MIT license. Please see [License File](LICENSE) for more information.
