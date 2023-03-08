<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Email;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversClass(Email::class)]
class EmailTest extends TestCase
{
    #[Test]
    public function success(): void
    {
        $email = new Email($value = 'email@app.test');

        self::assertEquals($value, $email->getValue());
    }

    #[Test]
    public function case(): void
    {
        $email = new Email('EmAil@app.test');

        self::assertEquals('email@app.test', $email->getValue());
    }

    #[Test]
    public function incorrect(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('not-email');
    }

    #[Test]
    public function empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('');
    }

    #[Test]
    public function equal(): void
    {
        $email = new Email('email@mail.com');

        self::assertTrue($email->isEqualTo(new Email('email@mail.com')));
        self::assertFalse($email->isEqualTo(new Email('another@mail.com')));
    }
}
