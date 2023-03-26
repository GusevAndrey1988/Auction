<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use Webmozart\Assert\Assert;

#[Embeddable()]
class Token
{
    /**
     * @var string
     */
    #[Column(type: 'string', nullable: true)]
    private $value;

    /**
     * @var \DateTimeImmutable
     */
    #[Column(type: 'datetime_immutable', nullable: true)]
    private $expires;

    public function __construct(string $value, \DateTimeImmutable $expires)
    {
        Assert::uuid($value);
        $this->value = mb_strtolower($value);
        $this->expires = $expires;
    }

    public function validate(string $value, \DateTimeImmutable $date): void
    {
        if (!$this->isEqualTo($value)) {
            throw new \DomainException('Token is invalid.');
        }

        if ($this->isExpiredTo($date)) {
            throw new \DomainException('Token is expired.');
        }
    }

    private function isEqualTo(string $value): bool
    {
        return $this->value === $value;
    }

    public function isExpiredTo(\DateTimeImmutable $date): bool
    {
        return $this->expires <= $date;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getExpires(): \DateTimeImmutable
    {
        return $this->expires;
    }

    public function isEmpty(): bool
    {
        return empty($this->value);
    }
}
