<?php

namespace QueryAuth\Tests;

use QueryAuth\Client;
use QueryAuth\Factory;
use QueryAuth\ParameterCollection;
use QueryAuth\Server;
use QueryAuth\Signer;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Server
     */
    private $server;

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
        $this->server = $factory->newServer();
        $this->client = $factory->newClient();
        $this->key = md5(time());
        $this->secret = base64_encode(time() . 'secret');
        $this->host = 'www.example.com';
        $this->path = '/resources';
    }

    protected function tearDown()
    {
        $this->server = null;
        $this->client = null;
    }

    public function testValidateSignatureGetRequest()
    {
        $signedParams = $this->client->getSignedRequestParams(
            $this->key, $this->secret, 'GET', $this->host, $this->path, $params = array()
        );

        $result = $this->server->validateSignature(
            $this->secret, 'GET', $this->host, $this->path, $signedParams
        );

        $this->assertTrue($result);
    }

    public function testValidateSignaturePostRequest()
    {
        $signedParams = $this->client->getSignedRequestParams(
            $this->key, $this->secret, 'POST', $this->host, $this->path, $params = array('foo' => 'bar', 'baz' => 'bat')
        );

        $result = $this->server->validateSignature(
            $this->secret, 'POST', $this->host, $this->path, $signedParams
        );

        $this->assertTrue($result);
    }

    public function testValidateSignatureReturnsFalseForInvalidSignature()
    {
        $signedParams = $this->client->getSignedRequestParams(
            $this->key, $this->secret, 'GET', $this->host, $this->path, $params = array()
        );

        $signedParams['signature'] = 'WAT';

        $result = $this->server->validateSignature(
            $this->secret, 'GET', $this->host, $this->path, $signedParams
        );

        $this->assertFalse($result);
    }

    public function testExceedsMaximumDriftThrowsException()
    {
        $this->setExpectedException(
            'QueryAuth\Exception\TimeOutOfBoundsException',
            sprintf(
                'Timestamp is beyond the +-%d second difference allowed.',
                $this->server->getDrift()
            )
        );

        $signedParams = $this->client->getSignedRequestParams(
            $this->key, $this->secret, 'GET', $this->host, $this->path, $params = array()
        );

        $signedParams['timestamp'] = $signedParams['timestamp'] + ($this->server->getDrift() + 10);

        $this->server->validateSignature(
            $this->secret, 'GET', $this->host, $this->path, $signedParams
        );
    }

    public function testExceedsMinimumDriftThrowsException()
    {
        $this->setExpectedException(
            'QueryAuth\Exception\TimeOutOfBoundsException',
            sprintf(
                'Timestamp is beyond the +-%d second difference allowed.',
                $this->server->getDrift()
            )
        );

        $signedParams = $this->client->getSignedRequestParams(
            $this->key, $this->secret, 'GET', $this->host, $this->path, $params = array()
        );

        $signedParams['timestamp'] = $signedParams['timestamp'] - ($this->server->getDrift() + 10);

        $this->server->validateSignature(
            $this->secret, 'GET', $this->host, $this->path, $signedParams
        );
    }

    public function testMissingSignatureThrowsException()
    {
        $this->setExpectedException(
            'QueryAuth\Exception\SignatureMissingException',
            'Request must contain a signature.'
        );

        $signedParams = $this->client->getSignedRequestParams(
            $this->key, $this->secret, 'GET', $this->host, $this->path, $params = array()
        );

        unset($signedParams['signature']);

        $this->server->validateSignature(
            $this->secret, 'GET', $this->host, $this->path, $signedParams
        );
    }

    public function testGetSetDrift()
    {
        // Test default value
        $this->assertEquals(15, $this->server->getDrift());

        $this->server->setDrift(30);
        $this->assertEquals(30, $this->server->getDrift());
    }

    public function testGetSetSigner()
    {
        $this->assertInstanceOf('QueryAuth\Signer', $this->server->getSigner());
        $signature = new Signer(new ParameterCollection());
        $this->server->setSigner($signature);
        $this->assertSame($signature, $this->server->getSigner());
    }
}
