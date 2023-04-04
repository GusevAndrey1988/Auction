<?php

declare(strict_types=1);

use App\Console;
use Doctrine\ORM\Tools\Console\Command as ORMCommand;
use Doctrine\Migrations\Tools\Console\Command as dmc;

return [
    'config' => [
        'console' => [
            'commands' => [
                Console\HelloCommand::class,
                ORMCommand\ValidateSchemaCommand::class,

                dmc\ExecuteCommand::class,
                dmc\MigrateCommand::class,
                dmc\LatestCommand::class,
                dmc\StatusCommand::class,
                dmc\UpToDateCommand::class,
            ],
        ],
    ],
];
