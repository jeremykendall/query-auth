<?php

namespace QueryAuth\Request\Adapter;

use QueryAuth\RequestInterface;
use Slim\Http\Request as SlimRequest;

class SlimRequestAdapter implements RequestInterface
{
    /**
     * @var SlimRequest
     */
    protected $request;

    /**
     * Public constructor
     *
     * @param SlimRequest $request
     */
    public function __construct(SlimRequest $request)
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
        return $this->request->getPath();
    }

    /**
     * {@inheritDoc}
     */
    public function getParams()
    {
        if ($this->getMethod() === SlimRequest::METHOD_GET) {
            return $this->request->get();
        }

        return $this->request->post();
    }

    /**
     * {@inheritDoc}
     */
    public function addParam($key, $value)
    {
        // Not implemented for SlimRequest
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function replaceParams(array $params)
    {
        // Not implemented for SlimRequest
        return null;
    }
}
