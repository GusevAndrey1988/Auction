<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversClass(Status::class)]
class StatusTest extends TestCase
{
    #[Test]
    public function success(): void
    {
        $status = new Status($name = Status::WAIT);

        self::assertEquals($name, $status->getName());
    }

    #[Test]
    public function incorrect(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Status('none');
    }

    #[Test]
    public function wait(): void
    {
        $status = Status::wait();

        self::assertTrue($status->isWait());
        self::assertFalse($status->isActive());
    }

    #[Test]
    public function active(): void
    {
        $status = Status::active();

        self::assertFalse($status->isWait());
        self::assertTrue($status->isActive());
    }
}
