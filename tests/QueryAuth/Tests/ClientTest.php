<?php

namespace QueryAuthTests;

use QueryAuth\Client;
use QueryAuth\ParameterCollection;
use QueryAuth\Signer;

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
        $signer = new Signer(new ParameterCollection());
        $this->client = new Client($signer);
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
        $this->assertArrayHasKey('key', $result);
        $this->assertArrayHasKey('signature', $result);
        $this->assertEquals(3, count($result));
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
        $this->assertEquals(5, count($result));
    }

    public function testGetSetSigner()
    {
        $this->assertInstanceOf('QueryAuth\Signer', $this->client->getSigner());
        $signature = new Signer(new ParameterCollection());
        $this->client->setSigner($signature);
        $this->assertSame($signature, $this->client->getSigner());
    }
}
