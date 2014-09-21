<?php

namespace QueryAuth;

class SignerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Signer
     */
    private $signer;

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

    /**
     * @var array
     */
    private $params;

    /**
     * @var string Base 64 regex
     */
    private $base64Pattern;

    protected function setUp()
    {
        $this->signer = new Signer();
        $this->secret = base64_encode(time() . 'secret');
        $this->host = 'www.example.com';
        $this->path = '/resources';
        $this->params = array('timestamp' => (int) gmdate('U'), 'key' => md5(time()));
        $this->base64Pattern = '/^([A-Za-z0-9+\/]{4})' .
            '*([A-Za-z0-9+\/]{4}|[A-Za-z0-9+\/]{3}=|[A-Za-z0-9+\/]{2}==)$/';
    }

    protected function tearDown()
    {
        $this->signer = null;
    }

    public function testCreateSignatureForGET()
    {
        $signature = $this->signer->createSignature(
            'GET',
            $this->host,
            $this->path,
            $this->secret,
            $this->params
        );

        $this->assertNotNull($signature);
        $this->assertRegexp($this->base64Pattern, $signature);
    }

    public function testCreateSignatureForPOST()
    {
        $this->params['user'] = 'arthur.dent@example.net';
        $signature = $this->signer->createSignature(
            'POST',
            $this->host,
            $this->path,
            $this->secret,
            $this->params
        );

        $this->assertNotNull($signature);
        $this->assertRegexp($this->base64Pattern, $signature);
    }

    public function testSignaturesCreatedWithSameArgumentsShouldMatch()
    {
        $this->params['user'] = 'zaphod.beeblebrox@example.net';

        $signature1 = $this->signer->createSignature(
            'POST',
            $this->host,
            $this->path,
            $this->secret,
            $this->params
        );

        $signature2 = $this->signer->createSignature(
            'POST',
            $this->host,
            $this->path,
            $this->secret,
            $this->params
        );

        $this->assertEquals($signature1, $signature2);
    }
}
