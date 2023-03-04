<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\ResetPassword;

use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversClass(User::class)]
class ResetTest extends TestCase
{
    #[Test]
    public function success(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getPasswordResetToken());

        $user->resetPassword($token->getValue(), $now, $hash = 'hash');

        self::assertNull($user->getPasswordResetToken());
        self::assertEquals($hash, $user->getPasswordHash());
    }

    #[Test]
    public function invalidToken(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Token is invalid.');
        $user->resetPassword(Uuid::uuid4()->toString(), $now, 'hash');
    }

    #[Test]
    public function expiredToken(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Token is expired.');
        $user->resetPassword($token->getValue(), $now->modify('+1 day'), 'hash');
    }

    #[Test]
    public function notRequested(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new \DateTimeImmutable();

        $this->expectExceptionMessage('Resetting is not requested.');
        $user->resetPassword(Uuid::uuid4()->toString(), $now->modify('+1 day'), 'hash');
    }

    private function createToken(\DateTimeImmutable $date): Token
    {
        return new Token(Uuid::uuid4()->toString(), $date);
    }
}
