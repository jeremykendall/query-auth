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
use QueryAuth\Exception\DriftExceededException;
use QueryAuth\Exception\SignatureMissingException;
use QueryAuth\Exception\TimestampMissingException;
use QueryAuth\SignatureInterface;

/**
 * Validates requests
 */
class RequestValidator
{
    /**
     * @var int Permissible drift, in seconds
     */
    private $drift = 15;

    /**
     * @var Signature Instance of the signature creation class
     */
    private $signature;

    /**
     * Public constructor
     *
     * @param Signature $signature Instance of the signature creation interface
     */
    public function __construct(SignatureInterface $signature)
    {
        $this->signature = $signature;
    }

    /**
     * Is signature valid?
     *
     * @param  RequestInterface          $request     Request
     * @param  CredentialsInterface      $credentials Credentials
     * @throws DriftExceededException    If timestamp greater than or less than allowable drift
     * @throws SignatureMissingException If signature is missing from request
     * @throws TimestampMissingException If timestamp is missing from request
     * @return boolean
     */
    public function isValid(
        RequestInterface $request,
        CredentialsInterface $credentials
    )
    {
        $params = $request->getParams();

        $this->isSignaturePresent($params);
        $this->isTimestampPresent($params);
        $this->isDriftExceeded($params);

        return $params['signature'] === $this->signature->createSignature($request, $credentials);
    }

    /**
     * Is $timestamp greater than or less than $drift seconds?
     *
     * @param  array   $params Request params
     * @return boolean
     */
    protected function isDriftExceeded(array $params)
    {
        $now = (int) gmdate('U');

        if (abs($params['timestamp'] - $now) > $this->drift) {
            throw new DriftExceededException(
                sprintf(
                    'Timestamp is beyond the +-%d second difference allowed.',
                    $this->getDrift()
                )
            );
        }
    }

    /**
     * Throws exception if signature is not present in request
     *
     * @param  array                     $params Request params
     * @throws SignatureMissingException
     */
    protected function isSignaturePresent(array $params)
    {
        if (!isset($params['signature'])) {
            throw new SignatureMissingException('Request must contain a signature.');
        }
    }

    /**
     * Throws exception if timestamp is not present in request
     *
     * @param  array                     $params Request params
     * @throws TimestampMissingException
     */
    protected function isTimestampPresent(array $params)
    {
        if (!isset($params['timestamp'])) {
            throw new TimestampMissingException('Request must contain a timestamp.');
        }
    }

    /**
     * Get drift
     *
     * @return int $drift Permissible drift in seconds
     */
    public function getDrift()
    {
        return $this->drift;
    }

    /**
     * Set drift
     *
     * @param int $drift Permissible drift in seconds
     */
    public function setDrift($drift)
    {
        $this->drift = (int) $drift;
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
}
