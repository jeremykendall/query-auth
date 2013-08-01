<?php

namespace QueryAuth\Tests;

use QueryAuth\Client;
use QueryAuth\Server;

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
     * @var int
     */
    private $timestamp;

    protected function setUp()
    {
        $this->server = new Server();
        $this->client = new Client();
        $this->key = md5(time());
        $this->secret = base64_encode(time() . 'secret');
        $this->timestamp = time();
    }

    public function testValidateSignature()
    {
        $testSignature = $this->client
            ->generateSignature($this->key, $this->secret, $this->timestamp);

        $result = $this->server->validateSignature(
            $this->key, $this->secret, $this->timestamp, $testSignature
        );
        $this->assertTrue($result);
    }

    public function testValidateSignatureReturnsFalseForInvalidSignature()
    {
        $testSignature = 'WAT';
        $result = $this->server->validateSignature(
            $this->key, $this->secret, $this->timestamp, $testSignature
        );
        $this->assertFalse($result);
    }
}
