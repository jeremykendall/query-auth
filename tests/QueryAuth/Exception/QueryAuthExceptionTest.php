<?php

namespace QueryAuth\Exception;

class QueryAuthExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testDriftExceededException()
    {
        $e = new DriftExceededException();
        $this->assertInstanceOf('QueryAuth\Exception\DriftExceededException', $e);
        $this->assertInstanceOf('QueryAuth\Exception\QueryAuthException', $e);
    }

    public function testSignatureMissingException()
    {
        $e = new SignatureMissingException();
        $this->assertInstanceOf('QueryAuth\Exception\SignatureMissingException', $e);
        $this->assertInstanceOf('QueryAuth\Exception\QueryAuthException', $e);
    }

    public function testTimestampMissingException()
    {
        $e = new TimestampMissingException();
        $this->assertInstanceOf('QueryAuth\Exception\TimestampMissingException', $e);
        $this->assertInstanceOf('QueryAuth\Exception\QueryAuthException', $e);
    }
}
