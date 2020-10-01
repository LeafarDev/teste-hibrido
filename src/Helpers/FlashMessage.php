<?php

namespace TesteHibridoApp\Helpers;

class FlashMessage
{
    public static function get($key)
    {
        if (!isset($_SESSION[$key])) {
            return null;
        }
        $data = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $data;
    }

    public static function set($key, $data)
    {
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = array();
        }
        $_SESSION[$key] = $data;
    }
}