<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth\Request\Adapter\Incoming;

use QueryAuth\Request\IncomingRequestInterface;
use QueryAuth\Request\RequestInterface;
use Slim\Http\Request as SlimRequest;

/**
 * Incoming request adapter for Slim v2
 */
class SlimRequestAdapter implements IncomingRequestInterface, RequestInterface
{
    /**
     * @var SlimRequest Slim request
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

        if ($this->getMethod() === SlimRequest::METHOD_DELETE) {
            return $this->request->params();
        }

        return $this->request->post();
    }
}
