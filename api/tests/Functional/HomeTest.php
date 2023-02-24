<?php

declare(strict_types=1);

namespace Test\Functional;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversNothing]
class HomeTest extends WebTestCase
{
    #[Test]
    public function method(): void
    {
        $response = $this->app()->handle(self::json('POST', '/'));

        self::assertEquals(405, $response->getStatusCode());
    }

    #[Test]
    public function success(): void
    {
        $response = $this->app()->handle(self::json('GET', '/'));

        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals('{}', (string)$response->getBody());
        self::assertEquals(200, $response->getStatusCode());
    }
}
