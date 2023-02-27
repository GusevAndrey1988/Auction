<?php

declare(strict_types=1);

namespace App\Auth\Command\AttachNetwork;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\NetworkIdentity;
use App\Auth\Entity\User\UserRepository;
use App\Flusher;

class Handler
{
    public function __construct(
        private UserRepository $users,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $command): void
    {
        $identity = new NetworkIdentity($command->network, $command->identity);

        if ($this->users->hasByNetwork($identity)) {
            throw new \DomainException('User with this network already exists.');
        }

        $user = $this->users->get(new Id($command->id));

        $user->attachNetwork($identity);

        $this->flusher->flush();
    }
}
