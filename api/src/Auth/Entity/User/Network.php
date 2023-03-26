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
    private string $network;

    #[Column(type: 'string')]
    private string $identity;

    public function __construct(string $name, string $identity)
    {
        Assert::notEmpty($name);
        $this->network = mb_strtolower($name);

        Assert::notEmpty($identity);
        $this->identity = mb_strtolower($identity);
    }

    public function isEqualTo(self $network): bool
    {
        return
            $this->getNetwork() === $network->getNetwork()
            && $this->getIdentity() === $network->getIdentity();
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }
}
