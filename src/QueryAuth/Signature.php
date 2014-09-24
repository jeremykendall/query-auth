<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth;

use QueryAuth\Signer\SignatureSigner;

/**
 * Creates signature
 */
class Signer implements SignatureSigner
{
    /**
     * Creates signature
     *
     * @param  string $method HTTP method
     * @param  string $host   Host where request is being sent
     * @param  string $path   Request path
     * @param  string $secret API secret
     * @param  array  $params Request params (querystring, post body, etc)
     * @return string Base64 encoded signature
     */
    public function createSignature($method, $host, $path, $secret, array $params)
    {
        $data = $method . "\n"
            . $host . "\n"
            . $path . "\n"
            . $this->normalize($params);

        return \base64_encode(\hash_hmac('sha256', $data, $secret, true));
    }

    /**
     * Normalizes request parameters
     *
     * @return string Normalized, rawurlencoded parameter string
     */
    public function normalize(array $params)
    {
        uksort($params, 'strcmp');

        $signature = null;

        // Do not encode signature
        if (isset($params['signature'])) {
            $signature = $params['signature'];
            unset($params['signature']);
        }

        $query = http_build_query($params, null, '&', PHP_QUERY_RFC3986);

        if ($signature !== null) {
            $params['signature'] = $signature;
        }

        return $query;
    }
}
