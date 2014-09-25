<?php

namespace QueryAuth\Request;

interface RequestInterface
{
    /**
     * Get the HTTP method of the request
     *
     * @return string
     */
    public function getMethod();

    /**
     * Get the host of the request
     *
     * @return string
     */
    public function getHost();

    /**
     * Get the path of the request (e.g. '/', '/index.html')
     *
     * @return string
     */
    public function getPath();

    /**
     * Gets request parameters
     *
     * @return array
     */
    public function getParams();
}
