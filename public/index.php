<?php
declare(strict_types=1);

// autoloading
use TesteHibridoApp\Http\Controller\ClientController;
use TesteHibridoApp\Http\RouteManager;

require_once dirname(__DIR__) . '/vendor/autoload.php';
// DI, php-di
$containerBuilder = new \DI\ContainerBuilder();
$container = $containerBuilder->build();
// routes
$manager = new RouteManager;
$manager($container);