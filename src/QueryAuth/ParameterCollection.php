<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth;

/**
 * Collection class
 */
class ParameterCollection implements \IteratorAggregate, \ArrayAccess, \Countable
{
    /**
     * @var array Holds parameters
     */
    private $container;

    /**
     * Public constructor
     *
     * @param array $data Data to hold in collection
     */
    public function __construct(array $data = array())
    {
        $this->container = $data;
    }

    /**
     * Dumps collection to normalized parameter string
     *
     * @return string Normalized, rawurlencoded parameter string
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

    /**
     * Adds value
     *
     * @param mixed $key   Key
     * @param mixed $value Value
     */
    public function add($key, $value)
    {
        $this->container[$key] = $value;
    }

    /**
     * Sets collection data from array
     *
     * @param array $data Array of data
     */
    public function setFromArray(array $data)
    {
        $this->container = $data;
    }

    /**
     * Dumps collection contents to array
     *
     * @return array Collection data
     */
    public function toArray()
    {
        return $this->container;
    }

    /**
     * Gets iterator
     *
     * @return ArrayIterator iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this);
    }

    /**
     * Sets offset
     *
     * @param mixed $offset offset
     * @param mixed $value  value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Checks to see if offset exists
     *
     * @param mixed $offset Offset
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Unsets offset
     *
     * @param mixed $offset Offset
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets offset
     *
     * @param mixed $offset Offset
     */
    public function offsetGet($offset)
    {
        if (isset($this->container[$offset])) {
            return $this->container[$offset];
        }
    }

    /**
     * Counts elements in collection
     *
     * @return int Number of elements in collection
     */
    public function count()
    {
        return count($this->container);
    }
}
