<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth;

use QueryAuth\Signer;

/**
 * Signs requests
 */
class Client
{
    /**
     * @var Signer Instance of Signer
     */
    private $signer;

    /**
     * Public constructor
     *
     * @param Signer $signer Instance of singature creation class
     */
    public function __construct(Signer $signer)
    {
        $this->signer = $signer;
    }

    /**
     * Sign request params
     *
     * @param  string $key    API key
     * @param  string $secret API secret
     * @param  string $method Request method (GET, POST, PUT, HEAD, etc)
     * @param  string $host   Host portion of API resource URL (including subdomain, excluding scheme)
     * @param  string $path   Path portion of API resource URL (excluding query and fragment)
     * @param  array  $params OPTIONAL Request params (query or POST fields), only needed if required by endpoint
     * @return array  Request params provided PLUS key, timestamp, and signature
     */
    public function getSignedRequestParams($key, $secret, $method, $host, $path, array $params = array())
    {
        $params['key'] = $key;
        $params['timestamp'] = (int) gmdate('U');
        // Ensure path is absolute
        $path = '/' . ltrim($path, '/');
        $signature = $this->signer->createSignature($method, $host, $path, $secret, $params);
        $params['signature'] = $signature;

        return $params;
    }

    /**
     * Get Signer
     *
     * @return Signer Instance of the signature creation class
     */
    public function getSigner()
    {
        return $this->signer;
    }

    /**
     * Set Signer
     *
     * @param Signer $signer Instance of the signature creation class
     */
    public function setSigner(Signer $signer)
    {
        $this->signer = $signer;
    }
}
