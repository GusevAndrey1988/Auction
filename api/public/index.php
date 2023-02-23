<?php

declare(strict_types = 1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;

http_response_code(500);

require_once __DIR__.'/../vendor/autoload.php';

$builder = new \DI\ContainerBuilder();

$builder->addDefinitions([
    'config' => [
        'debug' => (bool)getenv('APP_DEBUG'),
    ]
]);

$container = $builder->build();

$app = AppFactory::createFromContainer($container);

$app->addErrorMiddleware($container->get('config')['debug'], true, true);

$app->get(
    '/',
    function (
        ServerRequestInterface $request,
        ResponseInterface $response,
        mixed $args
    ): ResponseInterface {
        $response->getBody()->write('{}');
        return $response->withHeader('Content-type', 'application/json');
    }
);

$app->run();