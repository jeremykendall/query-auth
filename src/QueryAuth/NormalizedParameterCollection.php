<?php

namespace QueryAuth;

class NormalizedParameterCollection implements \IteratorAggregate, \ArrayAccess, \Countable
{
    private $container;

    public function __construct(array $data = array())
    {
        $this->container = $data;
    }

    /**
     * Dumps collection to normalized parameter string
     *
     * @return string Normalized, rawurlencoded paramter string
     */
    public function normalize()
    {
        uksort($this->container, 'strcmp');

        $normalized = '';

        foreach ($this->container as $key => $value) {
            if ($key == 'signature') {
                continue;
            }

            $normalized .= rawurlencode($key) . '=' . rawurlencode($value) . '&';
        }

        return substr($normalized, 0, -1);
    }

    public function add($key, $value)
    {
        $this->container[$key] = $value;
    }

    public function setFromArray(array $data)
    {
        $this->container = $data;
    }

    public function toArray()
    {
        return $this->container;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($this->container[$offset])) {
            return $this->container[$offset];
        }
    }

    public function count()
    {
        return count($this->container);
    }
}
