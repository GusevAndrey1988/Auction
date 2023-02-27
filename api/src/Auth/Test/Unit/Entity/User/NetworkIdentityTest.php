<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\NetworkIdentity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversClass(NetworkIdentity::class)]
class NetworkIdentityTest extends TestCase
{
    #[Test]
    public function success(): void
    {
        $network = new NetworkIdentity($name = 'google', $identity = 'google-1');

        self::assertEquals($name, $network->getNetwork());
        self::assertEquals($identity, $network->getIdentity());
    }

    #[Test]
    public function emptyName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new NetworkIdentity('', 'google-1');
    }

    #[Test]
    public function emptyIdentity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new NetworkIdentity('google', '');
    }

    #[Test]
    public function equal(): void
    {
        $network = new NetworkIdentity($name = 'google', $identity = 'google-1');

        self::assertTrue($network->isEqualTo(new NetworkIdentity($name, $identity)));
        self::assertFalse($network->isEqualTo(new NetworkIdentity($name, 'google-2')));
        self::assertFalse($network->isEqualTo(new NetworkIdentity('vk', 'vk-1')));
    }
}
