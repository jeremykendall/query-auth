<?php

namespace QueryAuth;

use QueryAuth\Credentials\Credentials;
use RandomLib\Factory as RandomFactory;

class RequestSignerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $key = md5(time());
        $secret = base64_encode(time() . 'secret');

        $this->credentials = new Credentials($key, $secret);
        $this->request = $this->getMockBuilder('QueryAuth\RequestInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->signature = $this->getMockBuilder('QueryAuth\SignatureInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->keyGenerator = $this->getMockBuilder('QueryAuth\KeyGenerator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestSigner = new RequestSigner($this->signature, $this->keyGenerator);
    }

    protected function tearDown()
    {
        $this->requestSigner = null;
    }

    public function testSignRequest()
    {
        $signature = 'fjkdlajflkdjkasdljflasd';
        $cnonce = 'ahssgajsgajgusibanriuuei';

        $this->keyGenerator->expects($this->once())
            ->method('generateNonce')
            ->willReturn($cnonce);

        $this->signature->expects($this->once())
            ->method('createSignature')
            ->with($this->request, $this->credentials)
            ->willReturn($signature);

        $this->request->expects($this->exactly(4))
            ->method('addParam')
            ->withConsecutive(
                ['key', $this->credentials->getKey()],
                ['timestamp', $this->requestSigner->getTimestamp()],
                ['cnonce', $cnonce],
                ['signature', $signature]
            );

        $this->requestSigner->signRequest($this->request, $this->credentials);
    }

    public function testGetSetSignature()
    {
        $this->assertInstanceOf('QueryAuth\SignatureInterface', $this->requestSigner->getSignature());
        $signature = new Signature(new ParameterCollection());
        $this->requestSigner->setSignature($signature);
        $this->assertSame($signature, $this->requestSigner->getSignature());
    }

    public function testGetSetKeyGenerator()
    {
        $this->assertInstanceOf('QueryAuth\KeyGenerator', $this->requestSigner->getKeyGenerator());
        $randomFactory = new RandomFactory();
        $keyGenerator = new KeyGenerator($randomFactory->getMediumStrengthGenerator());
        $this->requestSigner->setKeyGenerator($keyGenerator);
        $this->assertSame($keyGenerator, $this->requestSigner->getKeyGenerator());
    }

    public function testGetSetTimestamp()
    {
        $default = $this->requestSigner->getTimestamp();
        $this->assertLessThanOrEqual(gmdate('U'), $default);
        $this->assertNotNull($default);
        $this->assertInternalType('int', $default);
        $new = gmdate('U');
        $this->requestSigner->setTimestamp($new);
        $this->assertEquals($new, $this->requestSigner->getTimestamp());
    }
}
