<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth\Credentials;

/**
 * Stores key and secret
 */
interface CredentialsInterface
{
    /**
     * Gets key
     *
     * @return string Key
     */
    public function getKey();

    /**
     * Gets secret
     *
     * @return string Secret
     */
    public function getSecret();
}
