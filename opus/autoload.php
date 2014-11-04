<?php namespace Opus;

/**
 * Class AutoLoad
 *
 * A PSR04 autoloader class.
 *
 * TODO: improve comments based on https://github.com/keradus/Psr4Autoloader/blob/master/src/Psr4Autoloader.php
 * and https://gist.github.com/jwage/221634
 * TODO: improve variable names and add namespace seperator methods
 * TODO: sort out the while loop
 *
 * @package Opus
 */

class AutoLoad {

    /**
     * Associative array that holds the namespace as
     * the key and the base directories (as an array)
     * as the value.
     *
     * @var array
     */
    private $namespaces = array();

    /**
     * Holds the file extension of the classes
     * to be loaded.
     *
     * @var string
     */
    private $fileExtension = '.php';

    /**
     * Sets the file extension to be used by
     * the class loader.
     *
     * @param $fileExtension
     * @return void
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
    }

    /**
     * Returns the file extension that will be
     * used by the class loader.
     *
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Register the autoloader using SPL.
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Unregister the autholoader using SPL.
     *
     * @return void
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Maps the key (namespace) and values (base directories)
     * to the $namespaces array.
     *
     * @param $namespace
     * @param $baseDirectory
     * @param bool $prepend
     * @return $this
     */
    public function addNamespace($namespace, $baseDirectory, $prepend = false)
    {
        $namespace = trim($namespace, '\\') . '\\';

        $baseDirectory = rtrim(rtrim($baseDirectory, '/'), DS) . '/';

        if (isset($this->namespaces[$namespace]) === false) {
            $this->namespaces[$namespace] = array();
        }

        if ($prepend) {
            array_unshift($this->namespaces[$namespace], $baseDirectory);
        } else {
            array_push($this->namespaces[$namespace], $baseDirectory);
        }

        return $this;
    }

    /**
     * Loads the class file for a given class name.
     * Used by spl_autoload_register.
     *
     * @param $className
     * @return bool|string
     */
    public function loadClass($className)
    {
        $namespace = $className;

        while (false !== $pos = strrpos($namespace, '\\')) {
            $namespace = substr($className, 0, $pos +1);

            $relativeClass = substr($className, $pos + 1);

            $mappedFile = $this->loadMappedFile($namespace, $relativeClass);
            if ($mappedFile) {
                return $mappedFile;
            }

            $namespace = rtrim($namespace, '\\');
        }

        return false;
    }

    /**
     * Load the mapped file for a namespace and relative class.
     *
     * @param $namespace
     * @param $relativeClass
     * @return bool|string
     */
    private function loadMappedFile($namespace, $relativeClass)
    {
        if (isset($this->namespaces[$namespace]) === false) {
            return false;
        }

        foreach ($this->namespaces[$namespace] as $baseDirectory) {
            $file = $baseDirectory . str_replace('\\', '/', $relativeClass) . $this->fileExtension;

            if ($this->requireFile($file)) {
                return $file;
            }
        }

        return false;
    }

    /**
     * If a file exists, return it.
     *
     * @return void
     */
    private function requireFile($file)
    {
        if (is_file($file)) {
            require $file;

            return true;
        }

        return false;
    }
}