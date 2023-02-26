<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\Token;

use App\Auth\Entity\User\Token;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversClass(Token::class)]
class ValidateTest extends TestCase
{
    #[Test]
    #[DoesNotPerformAssertions]
    public function success(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            $expires = new \DateTimeImmutable()
        );

        $token->validate($value, $expires->modify('-1 secs'));
    }

    #[Test]
    public function wrong(): void
    {
        $token = new Token(
            Uuid::uuid4()->toString(),
            $expires = new \DateTimeImmutable()
        );

        $this->expectExceptionMessage('Token is invalid.');
        $token->validate(Uuid::uuid4()->toString(), $expires->modify('-1 secs'));
    }

    #[Test]
    public function expired(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            $expires = new \DateTimeImmutable()
        );

        $this->expectExceptionMessage('Token is expired.');
        $token->validate($value, $expires->modify('+1 secs'));
    }
}
