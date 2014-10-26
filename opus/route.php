<?php namespace Opus;

class Route {

    private $url;

    private $name;

    private $methods = array('GET', 'POST', 'PUT', 'DELETE');

    private $target;

    private $parameters;

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url)
    {
        // Make sure the url is a string
        $url = (string)$url;

        // The url needs to be suffixed with a forward slash
        if (substr($url, -1) !== '/') {
            $url .= '/';
        }

        $this->url = $url;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name) {
        $this->name = (string)$name;
    }

    public function getMethods() {
        return $this->methods;
    }

    public function setMethods($methods)
    {
        $this->methods = $methods;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setTarget($target)
    {
        $this->target = $target;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
}