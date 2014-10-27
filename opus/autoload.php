<?php namespace Opus;


class AutoLoad {
    private $namespace;
    private $includePath;
    private $fileExtension = '.php';
    private $namespaceSeparator = "\\";

    public function __construct($namespace = null, $includePath = null)
    {
        $this->namespace = $namespace;
        $this->includePath = $includePath;
    }

    public function loadClass($className)
    {
        $classNameSpace = substr($className, 0, strlen($this->namespace . $this->namespaceSeparator));

        if (is_null($this->namespace) || $this->namespace . $this->namespaceSeparator === $classNameSpace) {
            $fileName = '';
            $namespace = '';
            $namespaceClass = strripos($className, $this->namespaceSeparator);

            if ($namespaceClass !== false) {
                $namespace = substr($className, 0, $namespaceClass);
                $className = substr($className, $namespaceClass + 1);
                $fileName = str_replace($this->namespaceSeparator, DS, $namespace) . DS;
            }
            $fileName .= str_replace('_', DS, $className) . $this->fileExtension;

            if (isnull($this->includePath) === false) {
                require $this->includePath . DS . $fileName;
            } else {
                require '' . $fileName;
            }
        }
    }

    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    public function unRegister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
    }

    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    public function setNamespaceSeparator($namespaceSeparator)
    {
        $this->namespaceSeparator = $namespaceSeparator;
    }

    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }

} 