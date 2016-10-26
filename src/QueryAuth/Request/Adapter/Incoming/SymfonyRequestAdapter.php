<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2016 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace AppBundle\Helper;

use QueryAuth\Request\IncomingRequestInterface;
use QueryAuth\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Incoming request adapter for Symfony v3
 */
class SymfonyRequestAdapter implements IncomingRequestInterface, RequestInterface
{
    /**
     * @var SymfonyRequest Symfony request
     */
    protected $request;

    /**
     * Public constructor
     *
     * @param SymfonyRequest $request
     */
    public function __construct(SymfonyRequest $request)
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
        if($this->getMethod() === SymfonyRequest::METHOD_GET) {
            return $this->request->query->all();
        }

        if($this->getMethod() === SymfonyRequest::METHOD_DELETE) {
            return $this->request->query->all();
        }

        return $this->request->request->all();
    }
}
