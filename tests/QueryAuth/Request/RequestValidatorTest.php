<?php

namespace QueryAuth\Request;

use QueryAuth\Credentials\Credentials;
use QueryAuth\Signature;

class RequestValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestValidator
     */
    private $requestValidator;

    /**
     * @var CredentialsInterface
     */
    private $credentials;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SignatureInterface
     */
    private $signature;

    protected function setUp()
    {
        $key = md5(time());
        $secret = base64_encode(time() . 'secret');

        $this->credentials = new Credentials($key, $secret);
        $this->request = $this->getMockBuilder('QueryAuth\Request\RequestInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->signature = $this->getMockBuilder('QueryAuth\SignatureInterface')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->requestValidator = new RequestValidator($this->signature);
    }

    protected function tearDown()
    {
        $this->requestValidator = null;
    }

    public function testIsValid()
    {
        $this->request->expects($this->once())
            ->method('getParams')
            ->willReturn([
                'signature' => 12345,
                'timestamp' => (int) gmdate('U'),
            ]);

        $this->signature->expects($this->once())
            ->method('createSignature')
            ->with($this->request, $this->credentials)
            ->willReturn(12345);

        $result = $this->requestValidator->isValid(
            $this->request, $this->credentials
        );

        $this->assertTrue($result);
    }

    public function testIsValidReturnsFalseForInvalidSignature()
    {
        $this->request->expects($this->once())
            ->method('getParams')
            ->willReturn([
                'signature' => 12345,
                'timestamp' => (int) gmdate('U'),
            ]);

        $this->signature->expects($this->once())
            ->method('createSignature')
            ->with($this->request, $this->credentials)
            ->willReturn(54321);

        $result = $this->requestValidator->isValid(
            $this->request, $this->credentials
        );

        $this->assertFalse($result);
    }

    public function testExceedsMaximumDriftThrowsException()
    {
        $this->setExpectedException(
            'QueryAuth\Exception\DriftExceededException',
            sprintf(
                'Timestamp is beyond the +-%d second difference allowed.',
                $this->requestValidator->getDrift()
            )
        );

        $badTimestamp = $this->requestValidator->getDrift() + 10;

        $this->request->expects($this->once())
            ->method('getParams')
            ->willReturn([
                'signature' => 12345,
                'timestamp' => (int) gmdate('U') + $badTimestamp,
            ]);

        $this->signature->expects($this->never())
            ->method('createSignature');

        $this->requestValidator->isValid(
            $this->request, $this->credentials
        );
    }

    public function testExceedsMinimumDriftThrowsException()
    {
        $this->setExpectedException(
            'QueryAuth\Exception\DriftExceededException',
            sprintf(
                'Timestamp is beyond the +-%d second difference allowed.',
                $this->requestValidator->getDrift()
            )
        );

        $badTimestamp = $this->requestValidator->getDrift() + 10;

        $this->request->expects($this->once())
            ->method('getParams')
            ->willReturn([
                'signature' => 12345,
                'timestamp' => (int) gmdate('U') - $badTimestamp,
            ]);

        $this->signature->expects($this->never())
            ->method('createSignature');

        $this->requestValidator->isValid(
            $this->request, $this->credentials
        );
    }

    public function testMissingSignatureThrowsException()
    {
        $this->setExpectedException(
            'QueryAuth\Exception\SignatureMissingException',
            'Request must contain a signature.'
        );

        $this->request->expects($this->once())
            ->method('getParams')
            ->willReturn([
                'timestamp' => (int) gmdate('U'),
            ]);

        $this->requestValidator->isValid(
            $this->request, $this->credentials
        );
    }

    public function testMissingTimestampThrowsException()
    {
        $this->setExpectedException(
            'QueryAuth\Exception\TimestampMissingException',
            'Request must contain a timestamp.'
        );

        $this->request->expects($this->once())
            ->method('getParams')
            ->willReturn([
                'signature' => 12345,
            ]);

        $this->requestValidator->isValid(
            $this->request, $this->credentials
        );
    }
    
    public function testGetSetDrift()
    {
        // Test default value
        $this->assertEquals(15, $this->requestValidator->getDrift());

        $this->requestValidator->setDrift(30);
        $this->assertEquals(30, $this->requestValidator->getDrift());
    }

    public function testGetSetSignature()
    {
        $this->assertInstanceOf('QueryAuth\SignatureInterface', $this->requestValidator->getSignature());
        $signature = new Signature();
        $this->requestValidator->setSignature($signature);
        $this->assertSame($signature, $this->requestValidator->getSignature());
    }
}
