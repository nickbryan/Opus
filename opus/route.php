<?php namespace Opus;

class Route {
    /**
     * A list of all currently supported methods.
     *
     * @var array
     */
    private $methods = array('GET', 'POST', 'PUT', 'DELETE');

    /**
     * @param $method
     * @return bool
     */
    protected function methodCheck($method)
    {
        if (in_array($method, $this->methods)) {
            return true;
        } else {
            return false;
        }
    }
}