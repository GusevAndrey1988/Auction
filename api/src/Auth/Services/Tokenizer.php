<?php

declare(strict_types=1);

namespace App\Auth\Services;

use App\Auth\Entity\User\Token;
use Ramsey\Uuid\Uuid;

class Tokenizer
{
    public function __construct(private \DateInterval $interval)
    {
    }

    public function generate(\DateTimeImmutable $date): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $date->add($this->interval)
        );
    }
}
