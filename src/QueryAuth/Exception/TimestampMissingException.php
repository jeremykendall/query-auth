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
 * Exception thrown when timestamp param is missing from request
 */
class TimestampMissingException extends \BadMethodCallException implements QueryAuthException
{
}
