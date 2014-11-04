<?php namespace Opus;

use ArrayAccess;

class Container implements ArrayAccess {
    protected $instances = array();

    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->instances[] = $value;
        } else {
            $this->instances[$offset] = $value;
        }
    }

    public function offsetGet($offset)
    {
        if (isset($this->instances[$offset])) {
            return $this->instances[$offset];
        } else {
            return null;
        }
    }

    public function offsetExists($offset)
    {

    }

    public function offsetUnset($offset)
    {

    }

    public function __get($key)
    {
        return $this[$key];
    }

    public function __set($key, $value)
    {
        $this[$key] = $value;
    }
}