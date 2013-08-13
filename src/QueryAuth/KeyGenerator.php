<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth;

use RandomLib\Factory as RandomFactory;

/**
 * Creates API keys and secrets
 */
class KeyGenerator
{
    /**
     * @var RandomFactory Random factory
     */
    private $randomFactory;

    /**
     * Public constructor
     *
     * @var RandomFactory $randomFactory RandomLib factory
     */
    public function __construct(RandomFactory $randomFactory)
    {
        $this->randomFactory = $randomFactory;
    }

    /**
     * Returns 40 character alphanumeric random string
     *
     * @return string API key
     */
    public function generateKey()
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $generator = $this->randomFactory->getMediumStrengthGenerator();

        return $generator->generateString(40, $chars);
    }

    /**
     * Returns 60 character alphanumeric plus '.' and '/' random string
     *
     * @return string API secret
     */
    public function generateSecret()
    {
        $generator = $this->randomFactory->getMediumStrengthGenerator();

        return $generator->generateString(60);
    }
}
