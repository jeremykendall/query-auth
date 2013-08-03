<?php

namespace QueryAuthTests;

use QueryAuth\Client;

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
     * @var int
     */
    private $timestamp;

    /**
        * @var string Base 64 regex
     */
    private $base64Pattern;

    protected function setUp()
    {
        $this->base64Pattern = '/^([A-Za-z0-9+\/]{4})*([A-Za-z0-9+\/]{4}|[A-Za-z0-9+\/]{3}=|[A-Za-z0-9+\/]{2}==)$/';
        $this->client = new Client();
        $this->key = md5(time());
        $this->secret = base64_encode(time() . 'secret');
        $this->host = 'www.example.com';
        $this->path = '/resources';
    }

    /**
     * This would be an example of a GET request that should be authenticated
     * but has no required or optional params of its own.
     *
     * http://www.example.com/resources
     *
     * @covers QueryAuth\Client::getSignedRequestParams()
     */
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

    /**
     * @covers QueryAuth\Client::getSignedRequestParams()
     */
    public function testGetRequestSignatureIsUrlEncoded()
    {
        $result = $this->client->getSignedRequestParams(
            $this->key, $this->secret, 'GET', $this->host, $this->path, $params = array()
        );

        $encodedSignature = $result['signature'];
        $signature = urldecode($encodedSignature);
        $this->assertRegexp($this->base64Pattern, $signature);
    }

    /**
     * @covers QueryAuth\Client::getSignedRequestParams()
     */
    public function testNotGetRequestSignatureIsNotUrlEncoded()
    {
        $result = $this->client->getSignedRequestParams(
            $this->key, $this->secret, 'POST', $this->host, $this->path, $params = array('bar' => 'baz')
        );

        $this->assertRegexp($this->base64Pattern, $result['signature']);
    }

    /**
     * @covers QueryAuth\Client::getSignedRequestParams()
     */
    public function testSameDataGeneratesSameSignatures()
    {
        $result1 = $this->client->getSignedRequestParams(
            $this->key, $this->secret, 'POST', $this->host, $this->path, $params = array('bar' => 'baz')
        );

        $result2 = $this->client->getSignedRequestParams(
            $this->key, $this->secret, 'POST', $this->host, $this->path, $params = array('bar' => 'baz')
        );

        $this->assertEquals($result1['signature'], $result2['signature']);
    }

    public function testGetNormalizedParameterString()
    {
        $params = array('email' => 'sam@example.com', 'name' => 'Sam Jones', 'comment' => "Y'all rock!");
        $expected = 'comment=Y%27all%20rock%21&email=sam%40example.com&name=Sam%20Jones';
        $actual = $this->client->getNormalizedParameterString($params);

        $this->assertEquals($expected, $actual);
    }

    public function testGetNormalizedParameterStringDoesNotOperateOnSignature()
    {
        $params = array('signature' => 'John Hancock');
        $result = $this->client->getNormalizedParameterString($params);
        $this->assertEquals('', $result);
    }
}
