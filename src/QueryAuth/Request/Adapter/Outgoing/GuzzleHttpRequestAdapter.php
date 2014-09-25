<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth\Request\Adapter\Outgoing;

use GuzzleHttp\Message\RequestInterface as GuzzleHttpRequestInterface;
use QueryAuth\Request\OutgoingRequestInterface;
use QueryAuth\Request\RequestInterface;

/**
 * Outgoing Request Adapter for Guzzle v4
 */
class GuzzleHttpRequestAdapter implements OutgoingRequestInterface, RequestInterface
{
    /**
     * @var GuzzleHttpRequestInterface
     */
    protected $request;

    /**
     * Public constructor
     *
     * @param GuzzleHttpRequestInterface $request
     */
    public function __construct(GuzzleHttpRequestInterface $request)
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
            return $this->request->getBody()->getFields();
        }

        return $this->request->getQuery()->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function addParam($key, $value)
    {
        if ($this->getMethod() == 'POST') {
            $this->request->getBody()->setField($key, $value);
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
            $this->request->getBody()->replaceFields($params);
        } else {
            $this->request->getQuery()->replace($params);
        }
    }
}
