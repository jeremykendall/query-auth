<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth;

use QueryAuth\Credentials\CredentialsInterface;
use QueryAuth\Request\RequestInterface;

/**
 * Creates signature
 */
class Signature implements SignatureInterface
{
    /**
     * Creates signature
     *
     * {@inheritDoc}
     */
    public function createSignature(RequestInterface $request, CredentialsInterface $credentials)
    {
        $data = $request->getMethod() . "\n"
            . $request->getHost() . "\n"
            . $this->getAbsolutePath($request->getPath()) . "\n"
            . $this->normalizeParameters($request->getParams());

        return \base64_encode(
            \hash_hmac('sha256', $data, $credentials->getSecret(), true)
        );
    }

    /**
     * Normalizes request parameters
     *
     * @params array $params Request parameters
     * @return string Normalized, rawurlencoded parameter string
     */
    protected function normalizeParameters(array $params)
    {
        // Do not encode signature
        if (isset($params['signature'])) {
            unset($params['signature']);
        }

        uksort($params, 'strcmp');

        return http_build_query($params, null, '&', PHP_QUERY_RFC3986);
    }

    protected function getAbsolutePath($path)
    {
        return '/' . ltrim($path, '/');
    }
}
