<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth\Request\Adapter\Outgoing;

use Guzzle\Http\Message\RequestInterface as GuzzleRequestInterface;
use QueryAuth\Request\OutgoingRequestInterface;
use QueryAuth\Request\RequestInterface;

/**
 * Outgoing Request Adapter for Guzzle v3
 */
class GuzzleRequestAdapter implements OutgoingRequestInterface, RequestInterface
{
    /**
     * @var GuzzleRequestInterface Guzzle request interface
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
     * Adds parameter to request
     *
     * {@inheritDoc}
     */
    public function addParam($key, $value)
    {
        if ($this->getMethod() == 'POST') {
            return $this->request->setPostField($key, $value);
        }

        $this->request->getQuery()->set($key, $value);
    }

    /**
     * Replaces request params
     *
     * {@inheritDoc}
     */
    public function replaceParams(array $params)
    {
        if ($this->getMethod() === 'POST') {
            return $this->request->getPostFields()->replace($params);
        }

        $this->request->getQuery()->replace($params);
    }
}
