<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth\Storage;

/**
 * Interface for dealing with signature persistence
 *
 * Use to prevent replay attacks by checking a persistence layer to see if the
 * requesting signature is already present.  If it is present, the request should
 * be denied. If it is not present, the signature should be persisted and the
 * request should be approved.
 *
 * In order to minimize reads and writes, it's highly recommended to do so only
 * after the signature has been otherwise validated.
 */
interface SignatureStorage
{
    /**
     * Checks persistence layer to see if a signature exists for the requester.
     * If a signature is found in the persistence layer, then it has already
     * been used and the associated request should be denied.
     *
     * If the persistence layer will return an error or throw an exception when
     * a duplicate apikey and signature are inserted, you don't have to use
     * this method to check for a key.  Simply attempt to save the signature and
     * check for the exception.
     *
     * @param  string  $key       API key of the requster
     * @param  string  $signature Request signature
     * @return boolean True if signature exists, false if not
     */
    public function exists($key, $signature);

    /**
     * Saves a key, signature, and the signature's expiration date
     *
     * @param string  $key       API key of the requster
     * @param string  $signature Request signature
     * @param integer $expires   Expiration timestamp
     */
    public function save($key, $signature, $expires);

    /**
     * Deletes any signature with an expiration date <= now
     */
    public function purge();
}
