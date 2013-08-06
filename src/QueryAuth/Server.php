<?php

namespace QueryAuth;

use QueryAuth\Exception\MaximumDriftExceededException;
use QueryAuth\Exception\MinimumDriftExceededException;
use QueryAuth\Exception\SignatureMissingException;
use QueryAuth\Signer;

class Server
{
    /**
     * @var int Permissible drift, in seconds
     */
    private $drift = 15;

    /**
     * Instance of the signature creation class
     *
     * @var Signer
     */
    private $signer;

    /**
     * Public constructor
     *
     * @param Signer $signer Instance of the signature creation class
     */
    public function __construct(Signer $signer)
    {
        $this->signer = $signer;
    }

    /**
     * Is signature valid?
     *
     * @param  string                        $secret API secret
     * @param  string                        $method Request method (GET, POST, PUT, HEAD, etc)
     * @param  string                        $host   Host portion of API resource URL (including subdomain, excluding scheme)
     * @param  string                        $path   Path portion of API resource URL (excluding query and fragment)
     * @param  array                         $params Request params
     * @throws MaximumDriftExceededException If drift is greater than $drift
     * @throws MinimumDriftExceededException If drift is less than $drift
     * @throws SignatureMissingException     If signature is missing from request
     * @return boolean
     */
    public function validateSignature($secret, $method, $host, $path, array $params)
    {
        if (!isset($params['signature'])) {
            throw new SignatureMissingException('Request must contain a signature.');
        }

        $currentTimestamp = (int) gmdate('U');

        if ($this->exceedsMaximumDrift($currentTimestamp, $params['timestamp'])) {
            throw new MaximumDriftExceededException(
                sprintf('Timestamp is more than %d seconds in the future.', $this->getDrift())
            );
        }

        if ($this->exceedsMinimumDrift($currentTimestamp, $params['timestamp'])) {
            throw new MinimumDriftExceededException(
                sprintf('Timestamp is more than %d seconds in the past.', $this->getDrift())
            );
        }

        $validSignature = $this->signer->createSignature(
            $method,
            $host,
            $path,
            $secret,
            $params
        );

        // Does the signature match what it's supposed to?
        return $params['signature'] === $validSignature;
    }

    /**
     * Is $timestamp more than $drift seconds in the future?
     *
     * @param  int     $now       GMT server timestamp
     * @param  int     $timestamp GMT timestamp from request
     * @return boolean
     */
    protected function exceedsMaximumDrift($now, $timestamp)
    {
        // Default the return to false
        $return = false;

        // If the timestamp allows for it set the return to true
        if ($timestamp > $now && ($timestamp - $now) > $this->drift) {
            $return = true;
        }

        return $return;
    }

    /**
     * Is $timestamp more than $drift seconds in the past?
     *
     * @param  int     $now       GMT server timestamp
     * @param  int     $timestamp GMT timestamp from request
     * @return boolean
     */
    protected function exceedsMinimumDrift($now, $timestamp)
    {
        // Default the return to false
        $return = false;

        // If the timestamp allows for it set the return to true
        if ($timestamp < $now && ($now - $timestamp) > $this->drift) {
            $return = true;
        }

        return $return;
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
    public function setSigner(Signer $signer)
    {
        $this->signer = $signer;
    }
}
