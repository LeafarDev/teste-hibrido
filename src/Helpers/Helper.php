<?php


namespace TesteHibridoApp\Helpers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

if (!function_exists('view')) {
    function view($view = null, $data = [])
    {
        $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
        $rootDir = str_replace("/vendor", "", dirname($reflection->getFileName(), 2));
        $loader = new FilesystemLoader($rootDir . '/resources/views');
        $twig = new Environment($loader);
        return $twig->render($view, $data);

    }
}