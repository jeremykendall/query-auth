<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth\Request;

/**
 * Interface for outgoing requests.
 *
 * Used to facilitate request signing and differentiate from incoming requests.
 */
interface OutgoingRequestInterface
{
    /**
     * Adds parameter to request
     *
     * @param mixed $key   Parameter key
     * @param mixed $value Parameter value
     */
    public function addParam($key, $value);

    /**
     * Replaces request params
     *
     * @param array $params Request parameters
     */
    public function replaceParams(array $params);
}
