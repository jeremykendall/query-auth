<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013-2014 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

namespace QueryAuth\Exception;

/**
 * Thrown when request timestamp is beyond allowable clock drift
 */
class DriftExceededException extends \OutOfBoundsException implements QueryAuthException
{
}
