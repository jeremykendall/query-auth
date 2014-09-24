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
class Credentials implements CredentialsInterface
{
    /**
     * @var string Key
     */
    private $key;

    /**
     * @var string Secret
     */
    private $secret;

    /**
     * Public constructor
     *
     * @param string $key    Key
     * @param string $secret Secret
     */
    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritDoc}
     */
    public function getSecret()
    {
        return $this->secret;
    }
}
