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
 * Interface for dealing with signature creation
 *
 */
interface SignatureInterface
{
    /**
     * Creates signature
     *
     * @param  RequestInterface     $request     Request
     * @param  CredentialsInterface $credentials Credentials
     * @return string               Base64 encoded signature
     */
    public function createSignature(RequestInterface $request, CredentialsInterface $credentials);
}
