<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth;

use QueryAuth\NormalizedParameterCollection;

/**
 * Creates signature
 */
class Signer
{
    /**
     * @var NormalizedParameterCollection Request parameter collection
     */
    private $collection;

    /**
     * Public constructor
     *
     * @param NormalizedParameterCollection $collection Parameter collection
     */
    public function __construct(NormalizedParameterCollection $collection)
    {
        $this->collection = $collection;
    }

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
        $this->collection->setFromArray($params);

        $data = $method . "\n"
            . $host . "\n"
            . $path . "\n"
            . $this->collection->normalize();

        return \base64_encode(\hash_hmac('sha256', $data, $secret, true));
    }
}
