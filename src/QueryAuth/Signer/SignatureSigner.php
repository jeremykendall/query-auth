<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth\Signer;

/**
 * Interface for dealing with signature creation
 *
 */
interface SignatureSigner
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
    public function createSignature($method, $host, $path, $secret, array $params);
}
