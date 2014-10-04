<?php

namespace QueryAuth;

class SignatureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Signature
     */
    private $signature;

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

    private $request;

    private $credentials;

    protected function setUp()
    {
        $this->request = $this->getMockBuilder('QueryAuth\Request\RequestInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->credentials = $this->getMockBuilder('QueryAuth\Credentials\CredentialsInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->signature = new Signature();
        $this->secret = base64_encode(time() . 'secret');
        $this->host = 'www.example.com';
        $this->path = '/resources';
        $this->params = array('timestamp' => (int) gmdate('U'), 'key' => md5(time()));
        $this->base64Pattern = '/^([A-Za-z0-9+\/]{4})' .
            '*([A-Za-z0-9+\/]{4}|[A-Za-z0-9+\/]{3}=|[A-Za-z0-9+\/]{2}==)$/';
    }

    protected function tearDown()
    {
        $this->signature = null;
    }

    public function testCreateSignatureForGET()
    {
        $this->request->method('getMethod')
            ->willReturn('GET');

        $this->request->method('getHost')
            ->willReturn($this->host);

        $this->request->method('getPath')
            ->willReturn($this->path);

        $this->request->method('getParams')
            ->willReturn($this->params);

        $this->credentials->method('getSecret')
            ->willReturn($this->secret);

        $signature = $this->signature->createSignature($this->request, $this->credentials);

        $this->assertNotNull($signature);
        $this->assertRegexp($this->base64Pattern, $signature);
    }

    public function testCreateSignatureForPOST()
    {
        $this->params['user'] = 'arthur.dent@example.net';

        $this->request->method('getMethod')
            ->willReturn('POST');

        $this->request->method('getHost')
            ->willReturn($this->host);

        $this->request->method('getPath')
            ->willReturn($this->path);

        $this->request->method('getParams')
            ->willReturn($this->params);

        $this->credentials->method('getSecret')
            ->willReturn($this->secret);

        $signature = $this->signature->createSignature($this->request, $this->credentials);

        $this->assertNotNull($signature);
        $this->assertRegexp($this->base64Pattern, $signature);
    }

    public function testSignaturesCreatedWithSameArgumentsShouldMatch()
    {
        $this->params['user'] = 'zaphod.beeblebrox@example.net';

        $this->request->method('getMethod')
            ->willReturn('POST');

        $this->request->method('getHost')
            ->willReturn($this->host);

        $this->request->method('getPath')
            ->willReturn($this->path);

        $this->request->method('getParams')
            ->willReturn($this->params);

        $this->credentials->method('getSecret')
            ->willReturn($this->secret);

        $signature1 = $this->signature->createSignature($this->request, $this->credentials);
        $signature2 = $this->signature->createSignature($this->request, $this->credentials);

        $this->assertEquals($signature1, $signature2);
    }

    public function testSignaturesUnsetIfPresentAndSignaturesMatch()
    {
        $this->params['user'] = 'zaphod.beeblebrox@example.net';

        $this->request->method('getMethod')
            ->willReturn('POST');

        $this->request->method('getHost')
            ->willReturn($this->host);

        $this->request->method('getPath')
            ->willReturn($this->path);

        $this->request->expects($this->at(3))
            ->method('getParams')
            ->willReturn($this->params);

        $this->request->expects($this->at(7))
            ->method('getParams')
            ->willReturn(array_merge($this->params, ['signature' => 'fjdklsjflkd']));

        $this->credentials->method('getSecret')
            ->willReturn($this->secret);

        $signature1 = $this->signature->createSignature($this->request, $this->credentials);
        $signature2 = $this->signature->createSignature($this->request, $this->credentials);

        $this->assertEquals($signature1, $signature2);
    }
}
