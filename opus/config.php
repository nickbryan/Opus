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
     * Holds a cached version of the config files so
     * we don't include them more than once.
     *
     * @var array
     */
    private static $cache = array();

    /**
     * Allows accessing of config files via dot notation
     *
     * @param $path
     * @return mixed
     * @throws \Exception
     */
    public static function get($path)
    {
        // Break down path
        $breakdown = explode('.', $path);

        // Get file name from the start of the array and remove
        $file = array_shift($breakdown);

        if (array_key_exists($file, static::$cache) === false) {
            $filePath = Application::getPath('config') . DS . $file . ".php";

            if (file_exists($filePath) === false) {
                throw new \Exception("Config file does not exist ($file)");
            }

            static::$cache[$file] = require_once($filePath);
        }

        $result = self::$cache[$file];

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