<?php

declare(strict_types=1);

namespace App\Auth\Command\ResetPassword\Reset;

use App\Auth\Entity\User\UserRepository;
use App\Auth\Services\PasswordHasher;
use App\Flusher;

class Handler
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $hasher,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $command): void
    {
        if (!$user = $this->users->findByPasswordResetToken($command->token)) {
            throw new \DomainException('Token is not found.');
        }

        $uesr->resetPassword(
            $command->token,
            new \DateTimeImmutable(),
            $this->hasher->hash($command->password)
        );

        $this->flusher->flush();
    }
}
