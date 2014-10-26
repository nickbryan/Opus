<?php namespace Opus;

/**
 * Class Request
 *
 *
 *
 * @package Opus
 */
class Request {

    /**
     * Get the base url (http://opus.dev)
     *
     * @return string
     */
    public static function getBaseUrl()
    {
        return "http://" . $_SERVER['HTTP_HOST'];
    }

    /**
     * Get the full url including query string (http://opus.dev/test?var=val)
     *
     * @return string
     */
    public static function getFullUrl()
    {
        return trim(self::getBaseUrl() . $_SERVER['REQUEST_URI'], '/');
    }

    /**
     * Get the full url without the query string (http://opus.dev/test)
     *
     * @return string
     */
    public static function getURL()
    {
        if (strstr(self::getFullUrl(), '?')) {
            return substr(self::getFullUrl(), 0, strpos(self::getFullUrl(), '?'));
        }

        return self::getFullUrl();
    }
}