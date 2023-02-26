<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use App\Auth\Services\PasswordHasher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversClass(PasswordHasher::class)]
class PasswordHasherTest extends TestCase
{
    #[Test]
    public function hash(): void
    {
        $hasher = new PasswordHasher(16);
        $hash = $hasher->hash($password = 'new-password');

        self::assertNotEmpty($hash);
        self::assertNotEmpty($password, $hash);
    }

    #[Test]
    public function hashEmpty(): void
    {
        $hasher = new PasswordHasher(16);

        $this->expectException(\InvalidArgumentException::class);
        $hasher->hash('');
    }

    #[Test]
    public function validate(): void
    {
        $hasher = new PasswordHasher(16);
        $hash = $hasher->hash($password = 'new-password');

        self::assertTrue($hasher->validate($password, $hash));
        self::assertFalse($hasher->validate('wrong-password', $hash));
    }
}
