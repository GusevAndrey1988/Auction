<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

class User
{
    private Status $status;

    public function __construct(
        private Id $id,
        private \DateTimeImmutable $date,
        private Email $email,
        private string $passwordHash,
        private ?Token $joinConfirmationToken
    ) {
        $this->status = Status::wait();
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

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getJoinConfirmToken(): ?Token
    {
        return $this->joinConfirmationToken;
    }
}
