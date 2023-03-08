<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\ChangeEmail;

use App\Auth\Entity\User\Email;
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
        $user = (new UserBuilder())->active()->build();

        $now = new \DateTimeImmutable();
        $token = new Token(Uuid::uuid4()->toString(), $now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, $new = new Email('new-email@app.test'));

        self::assertNotNull($user->getNewEmailToken());

        $user->confirmEmailChanging($token->getValue(), $now);

        self::assertNull($user->getNewEmailToken());
        self::assertNull($user->getNewEmail());
        self::assertEquals($new, $user->getEmail());
    }

    #[Test]
    public function invalidToken(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new \DateTimeImmutable();
        $token = new Token(Uuid::uuid4()->toString(), $now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, new Email('new-email@app.test'));

        $this->expectExceptionMessage('Token is invalid.');
        $user->confirmEmailChanging('invalid', $now);
    }

    #[Test]
    public function expiredToken(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new \DateTimeImmutable();
        $token = new Token(Uuid::uuid4()->toString(), $now);

        $user->requestEmailChanging($token, $now, new Email('new-email@app.test'));

        $this->expectExceptionMessage('Token is expired.');
        $user->confirmEmailChanging($token->getValue(), $now->modify('+1 day'));
    }

    #[Test]
    public function notRequested(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new \DateTimeImmutable();
        $token = new Token(Uuid::uuid4()->toString(), $now->modify('+1 day'));

        $this->expectExceptionMessage('Changing is not requested.');
        $user->confirmEmailChanging($token->getValue(), $now);
    }
}
