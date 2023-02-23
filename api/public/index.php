<?php

declare(strict_types = 1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;

require_once __DIR__.'/../vendor/autoload.php';

$app = AppFactory::create();

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