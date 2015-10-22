<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth\Request;

/**
 * Interface for outgoing requests.
 *
 * Used to facilitate request signing and differentiate from incoming requests.
 */
interface OutgoingImmutableRequestInterface
{
    /**
     * @return mixed
     */
    public function getRequest();
}
