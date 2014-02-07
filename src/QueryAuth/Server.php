<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth;

use QueryAuth\Exception\TimeOutOfBoundsException;
use QueryAuth\Exception\SignatureMissingException;
use QueryAuth\Signer\SignatureSigner;

/**
 * Validates signatures
 */
class Server
{
    /**
     * @var int Permissible drift, in seconds
     */
    private $drift = 15;

    /**
     * @var Signer Instance of the signature creation class
     */
    private $signer;

    /**
     * Public constructor
     *
     * @param Signer $signer Instance of the signature creation class
     */
    public function __construct(SignatureSigner $signer)
    {
        $this->signer = $signer;
    }

    /**
     * Is signature valid?
     *
     * @param  string                    $secret API secret
     * @param  string                    $method Request method (GET, POST, PUT, HEAD, etc)
     * @param  string                    $host   Host portion of API resource URL (including subdomain, excluding scheme)
     * @param  string                    $path   Path portion of API resource URL (excluding query and fragment)
     * @param  array                     $params Request params
     * @throws TimeOutOfBoundsException  If timestamp greater than or less than allowable drift
     * @throws SignatureMissingException If signature is missing from request
     * @return boolean
     */
    public function validateSignature($secret, $method, $host, $path, array $params)
    {
        if (!isset($params['signature'])) {
            throw new SignatureMissingException('Request must contain a signature.');
        }

        $currentTimestamp = (int) gmdate('U');

        if ($this->timeOutOfBounds($currentTimestamp, $params['timestamp'])) {
            throw new TimeOutOfBoundsException(
                sprintf('Timestamp is beyond the +-%d second difference allowed.', $this->getDrift())
            );
        }

        $validSignature = $this->signer->createSignature(
            $method,
            $host,
            $path,
            $secret,
            $params
        );

        // By @RobertGonzalez from PR #5
        return $params['signature'] === $validSignature;
    }

    /**
     * Is $timestamp greater than or less than $drift seconds?
     *
     * @param  int     $now       GMT server timestamp
     * @param  int     $timestamp GMT timestamp from request
     * @return boolean
     */
    protected function timeOutOfBounds($now, $timestamp)
    {
        if (abs($timestamp - $now) > $this->drift) {
            return true;
        }

        return false;
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
     * Get Signer
     *
     * @return Signer Instance of the signature creation class
     */
    public function getSigner()
    {
        return $this->signer;
    }

    /**
     * Set Signer
     *
     * @param Signer $signer Instance of the signature creation class
     */
    public function setSigner(SignatureSigner $signer)
    {
        $this->signer = $signer;
    }
}
