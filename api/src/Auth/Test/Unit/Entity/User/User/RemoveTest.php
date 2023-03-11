<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\User;
use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversClass(User::class)]
class RemoveTest extends TestCase
{
    #[Test]
    #[DoesNotPerformAssertions]
    public function success(): void
    {
        $user = (new UserBuilder())->build();

        $user->remove();
    }

    #[Test]
    public function active(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $this->expectExceptionMessage('Unable to remove active user.');

        $user->remove();
    }
}
