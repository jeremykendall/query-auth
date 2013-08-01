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

    protected function setUp()
    {
        $this->client = new Client();
        $this->key = md5(time());
        $this->secret = base64_encode(time() . 'secret');
        $this->timestamp = time();
    }

    public function testGenerateSignature()
    {
        $signature = $this->client->generateSignature(
            $this->key, $this->secret, $this->timestamp
        );

        $this->assertRegexp('/^([A-Za-z0-9+\/]{4})*([A-Za-z0-9+\/]{4}|[A-Za-z0-9+\/]{3}=|[A-Za-z0-9+\/]{2}==)$/', $signature);
    }

    public function testSameDataGeneratesSameSignatures()
    {
        $sig1 = $this->client->generateSignature(
            $this->key, $this->secret, $this->timestamp
        );

        $sig2 = $this->client->generateSignature(
            $this->key, $this->secret, $this->timestamp
        );

        $this->assertEquals($sig1, $sig2);
    }

    public function testGenerateUrlEncodedSignature()
    {
        $encoded = $this->client->generateUrlEncodedSignature(
            $this->key, $this->secret, $this->timestamp
        );

        $signature = $this->client->generateSignature(
            $this->key, $this->secret, $this->timestamp
        );

        // Since the same data generates the same sig, the decoded url encoded
        // signature should match a normal signature
        $this->assertEquals($signature, urldecode($encoded));
    }
}
