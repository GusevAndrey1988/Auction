<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

interface UserRepository
{
    public function hasByNetwork(NetworkIdentity $identity): bool;
    public function hasByEmail(Email $email): bool;
    public function findByConfirmToken(string $token): ?User;
    public function add(User $user): void;

    /**
     * @throws \DomainException
     */
    public function get(Id $userId): User;

    /**
     * @throws \DomainException
     */
    public function getByEmail(Email $email): User;
}