<?php

namespace QueryAuth\Request\Adapter\Outgoing;

class GuzzleHttpRequestAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $adapter;
    protected $adaptee;
    protected $query;

    protected function setUp()
    {
        parent::setUp();

        $this->adaptee = $this->getMockBuilder('GuzzleHttp\Message\RequestInterface')
            ->getMock();

        $this->query = $this->getMockBuilder('GuzzleHttp\Query')
            ->disableOriginalConstructor()
            ->getMock();

        $this->adapter = new GuzzleHttpRequestAdapter($this->adaptee);
    }

    protected function tearDown()
    {
        $this->adapter = null;

        parent::tearDown();
    }

    public function testGetMethod()
    {
        $this->adaptee->method('getMethod')
            ->willReturn('GET');

        $actual = $this->adapter->getMethod();

        $this->assertEquals('GET', $actual);
    }

    public function testGetHost()
    {
        $this->adaptee->method('getHost')
            ->willReturn('www.example.com');

        $actual = $this->adapter->getHost();

        $this->assertEquals('www.example.com', $actual);
    }

    public function testGetPath()
    {
        $this->adaptee->method('getPath')
            ->willReturn('/index.php');

        $actual = $this->adapter->getPath();

        $this->assertEquals('/index.php', $actual);
    }

    public function testGetParamsPost()
    {
        $expected = ['one' => 'two'];

        $postBody = $this->getMockBuilder('GuzzleHttp\Post\PostBodyInterface')
            ->getMock();

        $this->adaptee->method('getMethod')
            ->willReturn('POST');

        $this->adaptee->method('getBody')
            ->willReturn($postBody);

        $postBody->method('getFields')
            ->willReturn($expected);

        $params = $this->adapter->getParams();

        $this->assertEquals($expected, $params);
    }

    public function testGetParamsNotPost()
    {
        $expected = ['one' => 'two'];

        $this->adaptee->method('getMethod')
            ->willReturn('GET');

        $this->adaptee->method('getQuery')
            ->willReturn($this->query);

        $this->query->method('toArray')
            ->willReturn($expected);

        $params = $this->adapter->getParams();

        $this->assertEquals($expected, $params);
    }

    public function testReplaceParamsPost()
    {
        $signed = ['signature' => 'fjkdlsjfkdsljfdkls'];

        $postBody = $this->getMockBuilder('GuzzleHttp\Post\PostBodyInterface')
            ->getMock();

        $this->adaptee->expects($this->once())
            ->method('getMethod')
            ->willReturn('POST');

        $this->adaptee->expects($this->once())
            ->method('getBody')
            ->willReturn($postBody);

        $postBody->expects($this->once())
            ->method('replaceFields')
            ->with($signed);

        $this->adapter->replaceParams($signed);
    }

    public function testReplaceParamsNotPost()
    {
        $signed = ['signature' => 'fjkdlsjfkdsljfdkls'];

        $this->adaptee->expects($this->once())
            ->method('getMethod')
            ->willReturn('DELETE');

        $this->adaptee->expects($this->once())
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query->expects($this->once())
            ->method('replace')
            ->with($signed);

        $this->adapter->replaceParams($signed);
    }

    public function testAddParamPost()
    {
        $signature = 'jfkdlsjfldjfksljdlsjdls';

        $postBody = $this->getMockBuilder('GuzzleHttp\Post\PostBodyInterface')
            ->getMock();

        $this->adaptee->expects($this->once())
            ->method('getMethod')
            ->willReturn('POST');

        $this->adaptee->expects($this->once())
            ->method('getBody')
            ->willReturn($postBody);

        $postBody->expects($this->once())
            ->method('setField')
            ->with('signature', $signature);

        $this->adapter->addParam('signature', $signature);
    }

    public function testAddParamNotPost()
    {
        $signature = 'jfkdlsjfldjfksljdlsjdls';

        $this->adaptee->expects($this->once())
            ->method('getMethod')
            ->willReturn('DELETE');

        $this->adaptee->expects($this->once())
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query->expects($this->once())
            ->method('set')
            ->with('signature', $signature);

        $this->adapter->addParam('signature', $signature);
    }
}
