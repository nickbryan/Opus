<?php namespace Opus;

/**
 * Class Application
 *
 * @package Opus
 */

class Application extends Container {

    /**
     * Framework version
     *
     * @var string
     */
    const VERSION = '1.0';

    /**
     * Indicates if the application has been initialised
     *
     * @var bool
     */
    private $booted = false;

    /**
     * Holds a list of all path aliases
     *
     * @var array
     */
    private $paths = array();

    /**
     * Initialise the application
     *
     * @access private
     * @return bool
     */

    public function __construct()
    {
        $this->instance('config', new Config($this));
    }

    public function initialise()
    {
        if ($this->booted) {
            return true;
        }

        Session::start();

        $this->mapAlias();

        $router = new Router();
        require_once $this->getPath('app') . '/routes.php';

        $match = $router->match();

        if ($match && is_callable($match['target'])) {
            call_user_func_array($match['target'], $match['params']);
        } else {
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
            // Add in exception handling here
        }

        $this->booted = true;
    }

    /**
     * Sets path aliases
     *
     * @access private
     * @param $paths
     * @return void
     */
    public function setPaths($paths) {
        $this->paths = $paths;
    }

    /**
     * Gets a path from the paths array
     *
     * @param $path
     * @throws \Exception
     * @return string
     */
    public function getPath($path) {
        if (array_key_exists($path, $this->paths)) {
            return $this->paths[$path];
        } else {
            throw new \Exception("Path $path not found");
        }
    }

    private function mapAlias()
    {
        foreach ($this['config']->get('app.class_alias') as $class => $alias) {
            class_alias($class, $alias);
        }
    }
}