<?php

declare(strict_types=1);

use Doctrine\ORM\Tools\Console\Command as ORMCommand;
use Doctrine\Migrations\Tools\Console\Command as dmc;

return [
    'config' => [
        'console' => [
            'commands' => [
                ORMCommand\SchemaTool\DropCommand::class,

                dmc\DiffCommand::class,
                dmc\GenerateCommand::class,
            ],
        ],
    ],
];
