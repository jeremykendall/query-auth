<?php

namespace QueryAuth\Request;

interface OutgoingRequestInterface
{
    /**
     * Adds parameter to request
     *
     * @param mixed $key   Parameter key
     * @param mixed $value Parameter value
     */
    public function addParam($key, $value);

    /**
     * Replaces request params
     *
     * @param array $params Request parameters
     */
    public function replaceParams(array $params);
}
