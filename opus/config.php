<?php namespace Opus;

/**
 * Class Config
 *
 * This class will handle all configuration stuff
 *
 * @package Opus
 */

class Config {

    /**
     * Holds an instance of the application class
     *
     * @var object
     */
    private $app;

    /**
     * Holds a cached version of the config files so
     * we don't include them more than once.
     *
     * @var array
     */
    private $cache = array();

    /**
     * Pass in an instance of app
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Allows accessing of configuration files via dot notation.
     *
     * @param $path
     * @param Application $app
     * @return mixed
     * @throws \Exception
     */
    public function get($path)
    {
        // Break down path
        $breakdown = explode('.', $path);

        // Get file name from the start of the array and remove
        $file = array_shift($breakdown);

        if (array_key_exists($file, $this->cache) === false) {
            $filePath = $this->app->getPath('config') . DS . $file . ".php";

            if (file_exists($filePath) === false) {
                throw new \Exception("Config file does not exist ($file)");
            }

            $this->cache[$file] = require_once $filePath;
        }

        $result = $this->cache[$file];

        if (count($breakdown) == 0) {
            return $result;
        }

        for ($i = 0; $i < count($breakdown); $i++) {
            if (array_key_exists($breakdown[$i], $result))
            {
                $result = $result[$breakdown[$i]];
            } else
            {
                throw new \Exception("config not found");
            }
        }

        return $result;
    }
}