<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Network;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversClass(Network::class)]
class NetworkTest extends TestCase
{
    #[Test]
    public function success(): void
    {
        $network = new Network($name = 'google', $identity = 'google-1');

        self::assertEquals($name, $network->getName());
        self::assertEquals($identity, $network->getIdentity());
    }

    #[Test]
    public function emptyName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Network('', 'google-1');
    }

    #[Test]
    public function emptyIdentity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Network('google', '');
    }

    #[Test]
    public function equal(): void
    {
        $network = new Network($name = 'google', $identity = 'google-1');

        self::assertTrue($network->isEqualTo(new Network($name, $identity)));
        self::assertFalse($network->isEqualTo(new Network($name, 'google-2')));
        self::assertFalse($network->isEqualTo(new Network('vk', 'vk-1')));
    }
}
