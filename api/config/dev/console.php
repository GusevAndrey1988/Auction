<?php

declare(strict_types=1);

use Doctrine\ORM\Tools\Console\Command as ORMCommand;

return [
    'config' => [
        'console' => [
            'commands' => [
                ORMCommand\SchemaTool\DropCommand::class,
            ],
        ],
    ],
];
