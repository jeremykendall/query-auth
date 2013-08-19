<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth;

use RandomLib\Generator as Generator;

/**
 * Creates API keys and secrets
 */
class KeyGenerator
{
    /**
     * @var Generator RandomLib Generator
     */
    private $generator;

    /**
     * Public constructor
     *
     * @var Generator $generator RandomLib factory
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Returns 40 character alphanumeric random string
     *
     * @return string API key
     */
    public function generateKey()
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return $this->generator->generateString(40, $chars);
    }

    /**
     * Returns 60 character alphanumeric plus '.' and '/' random string
     *
     * @return string API secret
     */
    public function generateSecret()
    {
        return $this->generator->generateString(60);
    }
}
