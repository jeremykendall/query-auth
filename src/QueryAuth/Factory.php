<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth;

use QueryAuth\Client;
use QueryAuth\ParameterCollection;
use QueryAuth\Server;
use QueryAuth\Signer;
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
     * @return Client Client instance
     */
    public function newClient()
    {
        return new Client($this->newSigner(), $this->newKeyGenerator());
    }

    /**
     * Creates a server instance
     *
     * @return Server Server instance
     */
    public function newServer()
    {
        return new Server($this->newSigner());
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
     * Creates a signer for either server or client
     *
     * @return Signer Signer instance
     */
    protected function newSigner()
    {
        return new Signer(new ParameterCollection());
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
