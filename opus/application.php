<?php namespace Opus;

/**
 * Class Application
 *
 * We only want this to be instantiated once so lets make it a singleton to be safe
 *
 * @package Opus
 */

class Application {

    /**
     * Framework version
     *
     * @var string
     */
    const VERSION = '1.0';

    /**
     * Singleton instance of the class
     *
     * @var object
     */
    private static $instance;

    /**
     * Indicates if the application has been initialised
     *
     * @var bool
     */
    private static $booted = false;

    /**
     * Holds a list of all path aliases
     *
     * @var array
     */
    private static $paths = array();

    /**
     * Returns the instance of the class ensuring
     * it is only ever instantiated once
     *
     * @return object
     */
    public static function getInstance()
    {
        if (isset(static::$instance) === false) {
            static::$instance = new Application();
        }

        return static::$instance;
    }

    /**
     * This will prevent the class from being instantiated
     *
     * @access private
     */
    private function __construct()
    {

    }

    /**
     * This will stop the class from being cloned
     *
     * @access private
     */
    private function __clone()
    {

    }

    /**
     * Initialise the application
     *
     * @access private
     * @return bool
     */
    public static function initialise()
    {
        if (static::$booted) {
            return true;
        }

        Session::start();

        /** tests */
        Router::register('/', function() {
            return "This is the index page";
        });

        Router::register('about', function() {
            return "This is the about page!";
        });
        var_dump(Router::callRoute());

        static::$booted = true;
    }

    /**
     * Sets path aliases
     *
     * @access private
     * @param $paths
     * @return void
     */
    public static function setPaths($paths) {
        static::$paths = $paths;
    }

    /**
     * Gets a path from the paths array
     *
     * @param $path
     * @throws \Exception
     * @return string
     */
    public static function getPath($path) {
        if (array_key_exists($path, static::$paths)) {
            return static::$paths[$path];
        } else {
            throw new \Exception("Path $path not found");
        }
    }
}