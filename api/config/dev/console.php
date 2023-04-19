<?php

declare(strict_types=1);

use App\Console\FixturesLoadCommand;
use Doctrine\ORM\Tools\Console\Command as ORMCommand;
use Doctrine\Migrations\Tools\Console\Command as dmc;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

return [
    FixturesLoadCommand::class => static function (ContainerInterface $container) {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{fixture_paths:string[]} $config
         */
        $config = $container->get('config')['console'];

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        return new FixturesLoadCommand(
            $em,
            $config['fixture_paths']
        );
    },

    'config' => [
        'console' => [
            'commands' => [
                FixturesLoadCommand::class,

                ORMCommand\SchemaTool\DropCommand::class,

                dmc\DiffCommand::class,
                dmc\GenerateCommand::class,
            ],
            'fixture_paths' => [
                __DIR__ . '/../../src/Auth/Fixture',
            ],
        ],
    ],
];
