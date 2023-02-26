<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class Status
{
    public const WAIT = 'wait';
    public const ACTIVE = 'active';

    public function __construct(private string $name)
    {
        Assert::oneOf($this->name, [
            self::WAIT,
            self::ACTIVE
        ]);
    }

    public static function wait(): self
    {
        return new self(self::WAIT);
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public function isWait(): bool
    {
        return $this->name === self::WAIT;
    }

    public function isActive(): bool
    {
        return $this->name === self::ACTIVE;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
