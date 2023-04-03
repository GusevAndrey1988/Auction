<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use Webmozart\Assert\Assert;

#[Embeddable()]
class Network
{
    #[Column(type: 'string')]
    private string $name;

    #[Column(type: 'string')]
    private string $identity;

    public function __construct(string $name, string $identity)
    {
        Assert::notEmpty($name);
        $this->name = mb_strtolower($name);

        Assert::notEmpty($identity);
        $this->identity = mb_strtolower($identity);
    }

    public function isEqualTo(self $network): bool
    {
        return
            $this->getName() === $network->getName()
            && $this->getIdentity() === $network->getIdentity();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }
}
