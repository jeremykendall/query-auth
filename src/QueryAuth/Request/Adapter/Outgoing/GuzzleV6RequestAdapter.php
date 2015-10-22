<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth\Request\Adapter\Outgoing;

use Psr\Http\Message\RequestInterface as GuzzleHttpRequestInterface;
use QueryAuth\Request\OutgoingImmutableRequestInterface;
use QueryAuth\Request\OutgoingRequestInterface;
use QueryAuth\Request\RequestInterface;

/**
 * Outgoing request adapter for Guzzle v6
 */
class GuzzleV6RequestAdapter implements OutgoingRequestInterface, OutgoingImmutableRequestInterface, RequestInterface
{
    /**
     * @var GuzzleHttpRequestInterface Guzzle request interface
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
        return $this->request->getUri()->getHost();
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        return $this->request->getUri()->getPath();
    }

    /**
     * Gets params
     *
     * {@inheritDoc}
     */
    public function getParams()
    {
        if ($this->getMethod() === 'POST') {
            return [];
        }

        return \GuzzleHttp\Psr7\parse_query($this->request->getUri()->getQuery());
    }

    /**
     * Adds parameter to request
     *
     * {@inheritDoc}
     */
    public function addParam($key, $value)
    {
        $queryParams = \GuzzleHttp\Psr7\parse_query($this->request->getUri()->getQuery());
        $queryParams[$key] = $value;

        $this->request = \GuzzleHttp\Psr7\modify_request($this->request, [
            'query' => \GuzzleHttp\Psr7\build_query($queryParams)
        ]);
        return $this->request;
    }

    /**
     * Replaces request params
     *
     * {@inheritDoc}
     */
    public function replaceParams(array $params)
    {
        $queryParams = \GuzzleHttp\Psr7\parse_query($this->request->getUri()->getQuery());
        foreach ($params as $key => $value) {
            $queryParams[$key] = $value;
        }

        $this->request = \GuzzleHttp\Psr7\modify_request($this->request, [
            'query' => \GuzzleHttp\Psr7\build_query($queryParams)
        ]);
        return $this->request;
    }

    /**
     * @return GuzzleHttpRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }
}
