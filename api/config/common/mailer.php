<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;

return [
    MailerInterface::class => static function(ContainerInterface $container): MailerInterface {
        /** @psalm-suppress MixedArrayAccess */
        $config = $container->get('config')['mailer'];

        $transport = Transport::fromDsn($config['dsn']);

        return new Mailer($transport);
    },

    'config' => [
        'mailer' => [
            'dsn' => getenv('MAILER_DSN'),
            'from' => getenv('MAILER_FROM_EMAIL'),
        ],
    ],
];
