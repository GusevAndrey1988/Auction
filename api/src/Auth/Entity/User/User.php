<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use ArrayObject;

class User
{
    private ?string $passwordHash = null;
    private ?Token $joinConfirmationToken = null;
    private \ArrayObject $networks;

    private function __construct(
        private Id $id,
        private \DateTimeImmutable $date,
        private Email $email,
        private Status $status
    ) {
        $this->networks = new ArrayObject();
    }

    public static function joinByNetwork(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        NetworkIdentity $identity
    ): self {
        $user = new User($id, $date, $email, Status::active());
        $user->networks->append($identity);
        return $user;
    }

    public static function requestJoinByEmail(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    ): self {
        $user = new self($id, $date, $email, Status::wait());
        $user->passwordHash = $passwordHash;
        $user->joinConfirmationToken = $token;
        return $user;
    }

    public function attachNetwork(NetworkIdentity $identity): void
    {
        /** @var NetworkIdentity $existing */
        foreach ($this->networks as $existing) {
            if ($existing->isEqualTo($identity)) {
                throw new \DomainException('Network is already attached.');
            }
        }
        $this->networks->append($identity);
    }

    /**
     * @return array<int, NetworkIdentity>
     */
    public function getNetworks(): array
    {
        /** @var array<int, NetworkIdentity> */
        return $this->networks->getArrayCopy();
    }

    public function confirmJoin(string $token, \DateTimeImmutable $date): void
    {
        if ($this->joinConfirmationToken === null) {
            throw new \DomainException('Confirmation is not required.');
        }
        $this->joinConfirmationToken->validate($token, $date);
        $this->status = Status::active();
        $this->joinConfirmationToken = null;
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getJoinConfirmToken(): ?Token
    {
        return $this->joinConfirmationToken;
    }
}
