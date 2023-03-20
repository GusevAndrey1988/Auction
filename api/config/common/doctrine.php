<?php

declare(strict_types=1);

use DI\Container;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Doctrine\ORM\Tools\Console\Command as ORMCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Symfony\Component\Console\Command\Command;

return [
    EntityManagerInterface::class => function (Container $container): EntityManagerInterface {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{
         *     metadata_dirs:array<array-key,string>,
         *     dev_mode:bool,
         *     proxy_dir:string,
         *     cache_dir:?string,
         *     types:array<string,class-string<\Doctrine\DBAL\Types\Type>>,
         *     connection:array
         * } $settings
         */
        $settings = $container->get('config')['doctrine'];

        $config = ORMSetup::createAttributeMetadataConfiguration(
            $settings['metadata_dirs'],
            $settings['dev_mode'],
            $settings['proxy_dir'],
            $settings['cache_dir'] ? new FilesystemAdapter('', 0, $settings['cache_dir']) : new ArrayAdapter()
        );

        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        /** @psalm-suppress MixedArgumentTypeCoercion */
        $connection = DriverManager::getConnection($settings['connection']);

        foreach ($settings['types'] as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, $class);
            }
        }

        return new EntityManager($connection, $config);
    },

    'config' => [
        'doctrine' => [
            'dev_mode' => false,
            'cache_dir' => __DIR__ . '/../../var/cache/doctrine/cache',
            'proxy_dir' => __DIR__ . '/../../var/cache/doctrine/proxy',
            'connection' => [
                'driver' => 'pdo_pgsql',
                'host' => getenv('DB_HOST'),
                'user' => getenv('DB_USER'),
                'password' => getenv('DB_PASSWORD'),
                'dbname' => getenv('DB_NAME'),
                'charset' => 'utf-8',
            ],
            'metadata_dirs' => [
                __DIR__ . '/../../src/Auth/Entity',
            ],
            'types' => [
                \App\Auth\Entity\User\IdType::NAME => \App\Auth\Entity\User\IdType::class,
            ],
        ],
    ],

    'emp' => function (Container $container): EntityManagerProvider {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        return new SingleManagerProvider($entityManager);
    },

    ORMCommand\ValidateSchemaCommand::class => function (Container $container): Command {
        /** @var EntityManagerProvider $entityManagerProvider */
        $entityManagerProvider = $container->get('emp');
        return new ORMCommand\ValidateSchemaCommand($entityManagerProvider);
    },

    ORMCommand\SchemaTool\DropCommand::class => function (Container $container): Command {
        /** @var EntityManagerProvider $entityManagerProvider */
        $entityManagerProvider = $container->get('emp');
        return new ORMCommand\SchemaTool\DropCommand($entityManagerProvider);
    },

    ORMCommand\SchemaTool\CreateCommand::class => function (Container $container): Command {
        /** @var EntityManagerProvider $entityManagerProvider */
        $entityManagerProvider = $container->get('emp');
        return new ORMCommand\SchemaTool\CreateCommand($entityManagerProvider);
    },
];
