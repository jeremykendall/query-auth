<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth;

use QueryAuth\Request\RequestSigner;
use QueryAuth\Request\RequestValidator;
use QueryAuth\Signature;
use RandomLib\Factory as RandomFactory;

/**
 * Creates QueryAuth classes
 */
class Factory
{
    /**
     * @var RandomFactory RandomLib Factory
     */
    private $randomFactory;

    /**
     * Creates a client instance
     *
     * @return RequestSigner RequestSigner instance
     */
    public function newRequestSigner()
    {
        return new RequestSigner($this->newSignature(), $this->newKeyGenerator());
    }

    /**
     * Creates a RequestValidator
     *
     * @return RequestValidator RequestValidator instance
     */
    public function newRequestValidator()
    {
        return new RequestValidator($this->newSignature());
    }

    /**
     * Creates new KeyGenerator created with medium strength RandomLib\Generator
     */
    public function newKeyGenerator()
    {
        return new KeyGenerator(
            $this->getRandomFactory()->getMediumStrengthGenerator()
        );
    }

    /**
     * Creates a Signature instance
     *
     * @return Signature Signature instance
     */
    protected function newSignature()
    {
        return new Signature();
    }

    /**
     * Get an instance of RandomFactory.  If property is null, creates a new
     * instance.
     *
     * @return RandomFactory Instance of RandomFactory
     */
    public function getRandomFactory()
    {
        if ($this->randomFactory === null) {
            $this->randomFactory = new RandomFactory();
        }

        return $this->randomFactory;
    }

    /**
     * Set randomFactory
     *
     * @param RandomFactory $randomFactory Instance of RandomFactory
     */
    public function setRandomFactory(RandomFactory $randomFactory)
    {
        $this->randomFactory = $randomFactory;
    }
}
