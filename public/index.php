<?php
require __DIR__ . '/../bootstrap.php';

use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => [
        __DIR__ . '/../views',
    ],
]);

$app->register(new Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => [
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/../var/data.sqlite',
    ]
]);

require __DIR__ . '/../src/config.php';

Request::enableHttpMethodParameterOverride();
$app->run();
