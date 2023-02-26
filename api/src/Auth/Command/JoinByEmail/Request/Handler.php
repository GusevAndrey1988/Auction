<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinByEmail\Request;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Services\JoinConfirmationSender;
use App\Auth\Services\PasswordHasher;
use App\Auth\Services\Tokenizer;
use App\Flusher;

class Handler
{
    public function __construct(
        private UserRepository $users,
        private PasswordHasher $hasher,
        private Tokenizer $tokenizer,
        private Flusher $flusher,
        private JoinConfirmationSender $sender
    ) {
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User already exists.');
        }

        $now = new \DateTimeImmutable();
        $token = $this->tokenizer->generate($now);

        $user = new User(
            Id::generate(),
            $now,
            $email,
            $this->hasher->hash($command->password),
            $token
        );

        $this->users->add($user);

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}
