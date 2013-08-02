<?php

namespace QueryAuth;

use RandomLib\Factory as RandomFactory;

class KeyGenerator
{
    /**
     * @var RandomFactory
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
