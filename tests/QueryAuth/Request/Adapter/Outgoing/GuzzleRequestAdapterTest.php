<?php

namespace QueryAuth\Request\Adapter\Outgoing;

use Guzzle\Http\Message\RequestInterface as GuzzleRequestInterface;

class GuzzleRequestAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $adapter;
    protected $adaptee;
    protected $queryString;

    protected function setUp()
    {
        parent::setUp();

        $this->adaptee = $this->getMockBuilder('Guzzle\Http\Message\RequestInterface')
            ->getMock();

        $this->queryString = $this->getMockBuilder('Guzzle\Http\QueryString')
            ->disableOriginalConstructor()
            ->getMock();

        $this->adapter = new GuzzleRequestAdapter($this->adaptee);
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

        $adaptee = $this->getMockBuilder('Guzzle\Http\Message\EntityEnclosingRequestInterface')
            ->getMock();

        $adapter = new GuzzleRequestAdapter($adaptee);

        $adaptee->method('getMethod')
            ->willReturn('POST');

        $adaptee->method('getPostFields')
            ->willReturn($this->queryString);

        $this->queryString->method('toArray')
            ->willReturn($expected);

        $params = $adapter->getParams();

        $this->assertEquals($expected, $params);
    }

    public function testGetParamsNotPost()
    {
        $expected = ['one' => 'two'];

        $this->adaptee->method('getMethod')
            ->willReturn('GET');

        $this->adaptee->method('getQuery')
            ->willReturn($this->queryString);

        $this->queryString->method('toArray')
            ->willReturn($expected);

        $params = $this->adapter->getParams();

        $this->assertEquals($expected, $params);
    }

    public function testReplaceParamsPost()
    {
        $signed = ['signature' => 'fjkdlsjfkdsljfdkls'];

        $adaptee = $this->getMockBuilder('Guzzle\Http\Message\EntityEnclosingRequestInterface')
            ->getMock();

        $adapter = new GuzzleRequestAdapter($adaptee);

        $adaptee->expects($this->once())
            ->method('getMethod')
            ->willReturn('POST');

        $adaptee->expects($this->once())
            ->method('getPostFields')
            ->willReturn($this->queryString);

        $this->queryString->expects($this->once())
            ->method('replace')
            ->with($signed);

        $adapter->replaceParams($signed);
    }

    public function testReplaceParamsNotPost()
    {
        $signed = ['signature' => 'fjkdlsjfkdsljfdkls'];

        $this->adaptee->expects($this->once())
            ->method('getMethod')
            ->willReturn('DELETE');

        $this->adaptee->expects($this->once())
            ->method('getQuery')
            ->willReturn($this->queryString);

        $this->queryString->expects($this->once())
            ->method('replace')
            ->with($signed);

        $this->adapter->replaceParams($signed);
    }

    public function testAddParamPost()
    {
        $signature = 'jfkdlsjfldjfksljdlsjdls';

        $adaptee = $this->getMockBuilder('Guzzle\Http\Message\EntityEnclosingRequestInterface')
            ->getMock();

        $adapter = new GuzzleRequestAdapter($adaptee);

        $adaptee->expects($this->once())
            ->method('getMethod')
            ->willReturn('POST');

        $adaptee->expects($this->once())
            ->method('setPostField')
            ->with('signature', $signature);

        $adapter->addParam('signature', $signature);
    }

    public function testAddParamNotPost()
    {
        $signature = 'jfkdlsjfldjfksljdlsjdls';

        $this->adaptee->expects($this->once())
            ->method('getMethod')
            ->willReturn('DELETE');

        $this->adaptee->expects($this->once())
            ->method('getQuery')
            ->willReturn($this->queryString);

        $this->queryString->expects($this->once())
            ->method('set')
            ->with('signature', $signature);

        $this->adapter->addParam('signature', $signature);
    }
}
