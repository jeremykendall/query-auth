<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth\Request;

use QueryAuth\Credentials\CredentialsInterface;
use QueryAuth\KeyGenerator;
use QueryAuth\Request\OutgoingRequestInterface;
use QueryAuth\SignatureInterface;

/**
 * Signs requests
 */
class RequestSigner
{
    /**
     * @var Signature Instance of SignatureInterface
     */
    private $signature;

    /**
     * @var KeyGenerator Instance of KeyGenerator
     */
    private $keyGenerator;

    /**
     * @var int Unix timestamp
     */
    private $timestamp;

    /**
     * Public constructor
     *
     * @param SignatureInterface $signature    SingatureInterface
     * @param KeyGenerator       $keyGenerator Key generator
     */
    public function __construct(SignatureInterface $signature, KeyGenerator $keyGenerator)
    {
        $this->signature = $signature;
        $this->keyGenerator = $keyGenerator;
    }

    /**
     * Sign request
     *
     * @param  RequestInterface     $request     Request
     * @param  CredentialsInterface $credentials Credentials
     * @return mixed
     */
    public function signRequest(
        OutgoingRequestInterface $request,
        CredentialsInterface $credentials
    )
    {
        $request->addParam('key', $credentials->getKey());
        $request->addParam('timestamp', $this->getTimestamp());
        $request->addParam('cnonce', $this->keyGenerator->generateNonce());

        $signature = $this->signature->createSignature($request, $credentials);

        $request->addParam('signature', $signature);

        if ($request instanceof OutgoingImmutableRequestInterface) {
            return $request->getRequest();
        }
    }

    /**
     * Get Signature
     *
     * @return Signature Instance of the signature creation class
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Set Signature
     *
     * @param Signature $signature Instance of the signature creation class
     */
    public function setSignature(SignatureInterface $signature)
    {
        $this->signature = $signature;
    }

    /**
     * Gets instance of KeyGenerator
     *
     * @return KeyGenerator Instance of KeyGenerator
     */
    public function getKeyGenerator()
    {
        return $this->keyGenerator;
    }

    /**
     * Sets instance of KeyGenerator
     *
     * @param KeyGenerator $keyGenerator Instance of KeyGenerator
     */
    public function setKeyGenerator(KeyGenerator $keyGenerator)
    {
        $this->keyGenerator = $keyGenerator;
    }

    /**
     * Get timestamp
     *
     * Returns GMT timestamp if timestamp has not been set.
     *
     * @return int timestamp
     */
    public function getTimestamp()
    {
        if ($this->timestamp === null) {
            return (int) gmdate('U');
        }

        return $this->timestamp;
    }

    /**
     * Set timestamp
     *
     * @param int $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }
}
