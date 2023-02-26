<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\Token;

use App\Auth\Entity\User\Token;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversClass(Token::class)]
class CreateTest extends TestCase
{
    #[Test]
    public function success(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            $expires = new \DateTimeImmutable()
        );

        self::assertEquals($value, $token->getValue());
        self::assertEquals($expires, $token->getExpires());
    }

    #[Test]
    public function case(): void
    {
        $value = Uuid::uuid4()->toString();

        $token = new Token(mb_strtoupper($value), new \DateTimeImmutable());

        self::assertEquals($value, $token->getValue());
    }

    #[Test]
    public function incorrect(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Token('12345', new \DateTimeImmutable());
    }

    #[Test]
    public function empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Token('', new \DateTimeImmutable());
    }
}
