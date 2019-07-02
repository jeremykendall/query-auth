<?php

namespace QueryAuth\Request\Adapter\Incoming;

use QueryAuth\Request\IncomingRequestInterface;
use QueryAuth\Request\RequestInterface;
use Illuminate\Http\Request as LaravelRequest;

/**
 * Incoming request adapter for Laravel v5
 */
class LaravelRequestAdapter implements IncomingRequestInterface, RequestInterface
{
    /**
     * @var LaravelRequest Laravel request
     */
    protected $request;

    /**
     * Public constructor
     *
     * @param LaravelRequest $request
     */
    public function __construct(LaravelRequest $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethod()
    {
        return $this->request->getMethod();
    }

    /**
     * {@inheritDoc}
     */
    public function getHost()
    {
        return $this->request->getHost();
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        return $this->request->getPathInfo();
    }

    /**
     * {@inheritDoc}
     */
    public function getParams()
    {
        return $this->request->input();
    }
}
