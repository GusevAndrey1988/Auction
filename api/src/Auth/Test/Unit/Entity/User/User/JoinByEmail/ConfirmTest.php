<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\JoinByEmail;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
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
class ConfirmTest extends TestCase
{
    #[Test]
    public function success(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->build();

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertNull($user->getJoinConfirmToken());
    }

    #[Test]
    public function wrong(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Token is invalid.');

        $user->confirmJoin(
            Uuid::uuid4()->toString(),
            $token->getExpires()->modify('-1 day')
        );
    }

    #[Test]
    public function expired(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Token is expired.');

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('+1 day')
        );
    }

    #[Test]
    public function already(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->active()
            ->build();

        $this->expectExceptionMessage('Confirmation is not required.');

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );
    }

    private function createToken(): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            new \DateTimeImmutable('+1 day')
        );
    }
}
