<?php

namespace QueryAuth\Request\Adapter\Incoming;

use QueryAuth\Request\IncomingRequestInterface;
use QueryAuth\Request\RequestInterface;
use Slim\Http\Request as SlimRequest;

class SlimRequestAdapter implements IncomingRequestInterface, RequestInterface
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
}
