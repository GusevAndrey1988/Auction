<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class Role
{
    public const USER = 'user';
    public const ADMIN = 'admin';

    public function __construct(private string $name)
    {
        Assert::oneOf($name, [
            self::USER,
            self::ADMIN
        ]);
    }

    public static function user(): self
    {
        return new self(self::USER);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
