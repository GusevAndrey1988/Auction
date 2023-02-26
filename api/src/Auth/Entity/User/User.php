<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

class User
{
    public function __construct(
        private Id $id,
        private \DateTimeImmutable $date,
        private Email $email,
        private string $passwordHash,
        private ?Token $token
    ) {
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
        return $this->token;
    }
}
