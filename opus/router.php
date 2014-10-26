<?php namespace Opus;

class Router {
    private static $routes = array();

    private static $basePath = null;

    public static function setBasePath($basePath)
    {
        self::$basePath = (string)$basePath;
    }

    public static function register($url, $closure)
    {
        if (is_null($closure)) {
            throw new \Exception("Invalid closure passed to route.");
        }

        self::$routes[$url] = $closure;
    }

    public static function callRoute()
    {
        $url = Request::getURL();

        return static::$routes;
    }
}