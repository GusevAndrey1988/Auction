<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Id;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversClass(Id::class)]
class IdTest extends TestCase
{
    #[Test]
    public function success(): void
    {
        $id = new Id($value = Uuid::uuid4()->toString());

        self::assertEquals($value, $id->getValue());
    }

    #[Test]
    public function case(): void
    {
        $value = Uuid::uuid4()->toString();

        $id = new Id(mb_strtoupper($value));

        self::assertEquals($value, $id->getValue());
    }

    #[Test]
    public function generate(): void
    {
        $id = Id::generate();

        self::assertNotEmpty($id->getValue());
    }

    #[Test]
    public function incorrect(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Id('12345');
    }

    #[Test]
    public function empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Id('');
    }
}
