<?php

declare(strict_types=1);

use DI\Container;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Migrations\DependencyFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Tools\Console\Command as dmc;
use Doctrine\ORM\Tools\Console\Command as ORMCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

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
                \App\Auth\Entity\User\EmailType::NAME => \App\Auth\Entity\User\EmailType::class,
                \App\Auth\Entity\User\RoleType::NAME => \App\Auth\Entity\User\RoleType::class,
                \App\Auth\Entity\User\StatusType::NAME => \App\Auth\Entity\User\StatusType::class,
            ],
        ],
    ],

    'emp' => function (Container $container): EntityManagerProvider {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        return new SingleManagerProvider($entityManager);
    },

    'df' => function (Container $container): DependencyFactory {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);

        $connection = $entityManager->getConnection();

        $configuration = new Configuration($connection);
        $configuration->addMigrationsDirectory('App\Data\Migration', __DIR__ . '/../../src/Data/Migration');
        $configuration->setAllOrNothing(true);
        $storageConfiguration = new TableMetadataStorageConfiguration();
        $storageConfiguration->setTableName('migrations');
        $configuration->setMetadataStorageConfiguration($storageConfiguration);

        // return DependencyFactory::fromConnection(
        //     new ExistingConfiguration($configuration),
        //     new ExistingConnection($connection)
        // );

        return DependencyFactory::fromEntityManager(
            new ExistingConfiguration($configuration),
            new ExistingEntityManager($entityManager)
        );
    },

    dmc\ExecuteCommand::class => function (Container $container): Command {
        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = $container->get('df');
        return new dmc\ExecuteCommand($dependencyFactory);
    },

    dmc\MigrateCommand::class => function (Container $container): Command {
        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = $container->get('df');
        return new dmc\MigrateCommand($dependencyFactory);
    },

    dmc\LatestCommand::class => function (Container $container): Command {
        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = $container->get('df');
        return new dmc\LatestCommand($dependencyFactory);
    },

    dmc\StatusCommand::class => function (Container $container): Command {
        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = $container->get('df');
        return new dmc\StatusCommand($dependencyFactory);
    },

    dmc\UpToDateCommand::class => function (Container $container): Command {
        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = $container->get('df');
        return new dmc\UpToDateCommand($dependencyFactory);
    },

    dmc\DiffCommand::class => function (Container $container): Command {
        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = $container->get('df');
        return new dmc\DiffCommand($dependencyFactory);
    },

    dmc\GenerateCommand::class => function (Container $container): Command {
        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = $container->get('df');
        return new dmc\GenerateCommand($dependencyFactory);
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
