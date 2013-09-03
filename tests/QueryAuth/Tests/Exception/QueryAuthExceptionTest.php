<?php

namespace QueryAuth\Tests;

use QueryAuth\Exception\QueryAuthException;
use QueryAuth\Exception\SignatureMissingException;
use QueryAuth\Exception\TimeOutOfBoundsException;

class QueryAuthExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testSignatureMissingException()
    {
        $e = new SignatureMissingException();
        $this->assertInstanceOf('QueryAuth\Exception\SignatureMissingException', $e);
        $this->assertInstanceOf('QueryAuth\Exception\QueryAuthException', $e);
    }

    public function testTimeOutOfBoundsException()
    {
        $e = new TimeOutOfBoundsException();
        $this->assertInstanceOf('QueryAuth\Exception\TimeOutOfBoundsException', $e);
        $this->assertInstanceOf('QueryAuth\Exception\QueryAuthException', $e);
    }
}
