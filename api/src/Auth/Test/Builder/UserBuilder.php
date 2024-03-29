<?php

declare(strict_types=1);

namespace App\Auth\Test\Builder;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Network;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use Ramsey\Uuid\Uuid;

class UserBuilder
{
    private Id $id;
    private Email $email;
    private string $hash;
    private \DateTimeImmutable $date;
    private Token $joinConfirmationToken;
    private bool $active = false;
    private ?Network $networkIdentity = null;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->email = new Email('mail@example.com');
        $this->hash = 'hash';
        $this->date = new \DateTimeImmutable();
        $this->joinConfirmationToken = new Token(Uuid::uuid4()->toString(), $this->date->modify('+1 day'));
    }

    public function withJoinConfirmToken(Token $token): self
    {
        $clone = clone $this;
        $clone->joinConfirmationToken = $token;
        return $clone;
    }

    public function withEmail(Email $email): self
    {
        $clone = clone $this;
        $clone->email = $email;
        return $clone;
    }

    public function active(): self
    {
        $clone = clone $this;
        $clone->active = true;
        return $clone;
    }

    public function build(): User
    {
        if ($this->networkIdentity !== null) {
            return User::joinByNetwork(
                $this->id,
                $this->date,
                $this->email,
                $this->networkIdentity
            );
        }

        $user = User::requestJoinByEmail(
            $this->id,
            $this->date,
            $this->email,
            $this->hash,
            $this->joinConfirmationToken
        );

        if ($this->active) {
            $user->confirmJoin(
                $this->joinConfirmationToken->getValue(),
                $this->joinConfirmationToken->getExpires()->modify('-1 day')
            );
        }

        return $user;
    }

    public function viaNetwork(Network $identity = null): self
    {
        $clone = clone $this;
        $clone->networkIdentity = $identity ?? new Network('vk', '0000001');
        return $clone;
    }
}
