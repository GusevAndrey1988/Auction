<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\User;
use App\Auth\Services\PasswordHasher;
use App\Auth\Test\Builder\UserBuilder;
use phpDocumentor\Reflection\Types\Boolean;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversClass(User::class)]
class ChangePasswordTest extends TestCase
{
    #[Test]
    public function success(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $hasher = $this->createHasher(true, $hash = 'new-hash');

        $user->changePassword(
            'old-password',
            'new-password',
            $hasher
        );

        self::assertEquals($hash, $user->getPasswordHash());
    }

    #[Test]
    public function wrongCurrent(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $hasher = $this->createHasher(false, 'new-hash');

        $this->expectExceptionMessage('Incorrect current password.');
        $user->changePassword(
            'wrong-old-password',
            'new-password',
            $hasher
        );
    }

    public function byNetwork(): void
    {
        $user = (new UserBuilder())
            ->viaNetwork()
            ->build();

        $hasher = $this->createHasher(false, 'new-hash');

        $this->expectExceptionMessage('User does not have an old password.');
        $user->changePassword(
            'any-old-password',
            'new-password',
            $hasher
        );
    }

    private function createHasher(bool $valid, string $hash): PasswordHasher
    {
        $hasher = $this->createStub(PasswordHasher::class);
        $hasher->method('validate')->willReturn($valid);
        $hasher->method('hash')->willReturn($hash);
        return $hasher;
    }
}
