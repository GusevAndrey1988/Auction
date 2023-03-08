<?php

declare(strict_types=1);

namespace App\Auth\Services;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;

interface NewEmailConfirmTokenSender
{
    public function send(Email $email, Token $token): void;
}
