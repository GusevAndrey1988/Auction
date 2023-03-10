<?php

declare(strict_types=1);

use App\Console;
use Doctrine\ORM\Tools\Console\Command as ORMCommand;

return [
    'config' => [
        'console' => [
            'commands' => [
                Console\HelloCommand::class,
                ORMCommand\ValidateSchemaCommand::class
            ],
        ],
    ],
];
