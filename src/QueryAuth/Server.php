<?php

namespace QueryAuth;

use QueryAuth\Exception\MaximumDriftExceededException;
use QueryAuth\Exception\MinimumDriftExceededException;

class Server
{
    /**
     * @var int Permissible drift, in seconds
     */
    private $drift = 300;

    /**
     * Is signature valid?
     *
     * @param  string                        $key       API key
     * @param  string                        $secret    API secret
     * @param  int                           $timestamp Unix timestamp
     * @throws MaximumDriftExceededException If drift is greater than $drift
     * @throws MinimumDriftExceededException If drift is less than $drift
     * @return boolean
     */
    public function validateSignature($key, $secret, $timestamp, $signature)
    {
        $currentTimestamp = (int) gmdate('U');

        if ($this->exceedsMaximumDrift($currentTimestamp, $timestamp)) {
            throw new MaximumDriftExceededException(
                sprintf('Timestamp is more than %d seconds in the future.', $this->getDrift())
            );
        }

        if ($this->exceedsMinimumDrift($currentTimestamp, $timestamp)) {
            throw new MinimumDriftExceededException(
                sprintf('Timestamp is more than %d seconds in the past.', $this->getDrift())
            );
        }

        $validSignature = \base64_encode(
            \hash_hmac('sha256', $key . $timestamp, $secret, true)
        );

        if ($signature !== $validSignature) {
            return false;
        }

        return true;
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
        if ($timestamp > $now && ($timestamp - $now) > $this->drift) {
            return true;
        }

        return false;
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
        if ($timestamp < $now && ($now - $timestamp) > $this->drift) {
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
}
