<?php

namespace QueryAuth\Request\Adapter;

use Guzzle\Http\Message\RequestInterface as GuzzleRequestInterface;
use QueryAuth\RequestInterface;

class GuzzleRequestAdapter implements RequestInterface
{
    /**
     * @var GuzzleRequestInterface
     */
    protected $request;

    /**
     * Public constructor
     *
     * @param GuzzleRequestInterface $request
     */
    public function __construct(GuzzleRequestInterface $request)
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
        if ($this->getMethod() === 'POST') {
            return $this->request->getPostFields()->toArray();
        }

        return $this->request->getQuery()->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function addParam($key, $value)
    {
        if ($this->getMethod() == 'POST') {
            $this->request->setPostField($key, $value);
        } else {
            $this->request->getQuery()->set($key, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function replaceParams(array $params)
    {
        if ($this->getMethod() === 'POST') {
            $this->request->getPostFields()->replace($params);
        } else {
            $this->request->getQuery()->replace($params);
        }
    }
}
