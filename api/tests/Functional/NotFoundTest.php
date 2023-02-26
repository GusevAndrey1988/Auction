<?php

declare(strict_types=1);

namespace Test\Functional;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversNothing]
class NotFoundTest extends WebTestCase
{
    #[Test]
    public function notFound(): void
    {
        $response = $this->app()->handle(self::json('GET', '/not-found'));

        self::assertEquals(404, $response->getStatusCode());
    }
}
