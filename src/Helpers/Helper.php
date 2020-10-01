<?php


namespace TesteHibridoApp\Helpers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

if (!function_exists('rootDir')) {
    function rootDir($view = null, $data = [])
    {
        $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
        return str_replace("/vendor", "", dirname($reflection->getFileName(), 2));
    }
}

if (!function_exists('view')) {
    function view($view = null, $data = [])
    {
        $rootDir = rootDir();
        $loader = new FilesystemLoader($rootDir . '/resources/views');
        $twig = new Environment($loader);
        return $twig->render($view, $data);
    }
}

if (!function_exists('getEntityManager')) {
    function getEntityManager()
    {
        return require_once rootDir() . "/config/orm.php";
    }
}
if (!function_exists('sanitize')) {
    function sanitize($value)
    {
        return preg_replace('/[^\d]/', '', $value);
    }
}


if (!function_exists('validateCpf')) {
    function validateCpf($cpf)
    {
        $cpf = sanitize($cpf);
        if (mb_strlen($cpf) != 11 || preg_match("/^{$cpf[0]}{11}$/", $cpf)) {
            return false;
        }

        for (
            $s = 10, $n = 0, $i = 0; $s >= 2; $n += $cpf[$i++] * $s--
        ) {
        }

        if ($cpf[9] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        for (
            $s = 11, $n = 0, $i = 0; $s >= 2; $n += $cpf[$i++] * $s--
        ) {
        }

        if ($cpf[10] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        return true;
    }
}
