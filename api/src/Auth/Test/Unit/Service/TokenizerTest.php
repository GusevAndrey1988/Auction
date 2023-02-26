<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use App\Auth\Services\Tokenizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversClass(Tokenizer::class)]
class TokenizerTest extends TestCase
{
    #[Test]
    public function success(): void
    {
        $interval = new \DateInterval('PT1H');
        $date = new \DateTimeImmutable('+1 day');

        $tokenizer = new Tokenizer($interval);

        $token = $tokenizer->generate($date);

        self::assertEquals($date->add($interval), $token->getExpires());
    }
}
