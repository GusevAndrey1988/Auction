<?php

declare(strict_types=1);

namespace App\Auth\Services;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email as SymfonyEmail;

class JoinConfirmationSender
{
    /**
     * @param string[] $from
     */
    public function __construct(
        private MailerInterface $mailer,
        private array $from
    ) {
    }

    public function send(Email $email, Token $token): void
    {
        $addresses = [];
        foreach ($this->from as $fromEmail => $name) {
            $addresses[] = new Address((string)$fromEmail, $name);
        }

        $message = (new SymfonyEmail())
            ->subject('Join Confirmation')
            ->from(...$addresses)
            ->to($email->getValue())
            ->html('<a href="/join/confirm?' . http_build_query([
                'token' => $token->getValue(),
            ]) . '">Join</a>');

        $this->mailer->send($message);
    }
}
