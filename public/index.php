<?php
declare(strict_types=1);
// start session
session_start();
// autoloading
use Monolog\Logger;
use TesteHibridoApp\Http\Controller\ClientController;
use TesteHibridoApp\Http\RouteManager;
use function DI\create;
use function DI\get;

require_once dirname(__DIR__) . '/vendor/autoload.php';
// DI, php-di
$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions([
    Logger::class => create(Logger::class)
        ->constructor(get('App')),
    'App' => 'hibrido'
]);
$container = $containerBuilder->build();
// routes
$manager = new RouteManager;
$manager($container);