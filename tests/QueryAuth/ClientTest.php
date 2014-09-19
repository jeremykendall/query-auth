<?php

namespace QueryAuth;

use QueryAuth\Signer\SignatureSigner;
use RandomLib\Factory as RandomFactory;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $path;

    protected function setUp()
    {
        $factory = new Factory();
        $this->client = $factory->newClient();
        $this->key = md5(time());
        $this->secret = base64_encode(time() . 'secret');
        $this->host = 'www.example.com';
        $this->path = '/resources';
    }

    protected function tearDown()
    {
        $this->client = null;
    }

    public function testGetSignedRequestParamsForGetRequestWithoutParams()
    {
        $result = $this->client->getSignedRequestParams(
            $this->key, $this->secret, 'GET', $this->host, $this->path, $params = array()
        );

        $this->assertInternalType('array', $result);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('timestamp', $result);
        $this->assertArrayHasKey('cnonce', $result);
        $this->assertArrayHasKey('key', $result);
        $this->assertArrayHasKey('signature', $result);
        $this->assertEquals(4, count($result));
    }

    public function testGetSignedRequestParamsForPostRequestWithParams()
    {
        $result = $this->client->getSignedRequestParams(
            $this->key,
            $this->secret,
            'POST',
            $this->host,
            $this->path,
            $params = array('foo' => 'bar', 'baz' => 'bat')
        );

        $this->assertInternalType('array', $result);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('foo', $result);
        $this->assertArrayHasKey('baz', $result);
        $this->assertEquals(6, count($result));
    }

    public function testSignaturesWithSameDataAndTimestampAreUnique()
    {
        $this->client->setTimestamp(gmdate('U'));

        $result1 = $this->client->getSignedRequestParams(
            $this->key,
            $this->secret,
            'POST',
            $this->host,
            $this->path,
            $params = array('foo' => 'bar', 'baz' => 'bat')
        );

        $result2 = $this->client->getSignedRequestParams(
            $this->key,
            $this->secret,
            'POST',
            $this->host,
            $this->path,
            $params = array('foo' => 'bar', 'baz' => 'bat')
        );

        $this->assertNotEquals($result1, $result2);
    }

    public function testGetSetSigner()
    {
        $this->assertInstanceOf('QueryAuth\Signer\SignatureSigner', $this->client->getSigner());
        $signature = new Signer(new ParameterCollection());
        $this->client->setSigner($signature);
        $this->assertSame($signature, $this->client->getSigner());
    }

    public function testGetSetKeyGenerator()
    {
        $this->assertInstanceOf('QueryAuth\KeyGenerator', $this->client->getKeyGenerator());
        $randomFactory = new RandomFactory();
        $keyGenerator = new KeyGenerator($randomFactory->getMediumStrengthGenerator());
        $this->client->setKeyGenerator($keyGenerator);
        $this->assertSame($keyGenerator, $this->client->getKeyGenerator());
    }

    public function testGetSetTimestamp()
    {
        $default = $this->client->getTimestamp();
        $this->assertLessThanOrEqual(gmdate('U'), $default);
        $this->assertNotNull($default);
        $this->assertInternalType('int', $default);
        $new = gmdate('U');
        $this->client->setTimestamp($new);
        $this->assertEquals($new, $this->client->getTimestamp());
    }
}
